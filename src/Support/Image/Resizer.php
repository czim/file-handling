<?php

namespace Czim\FileHandling\Support\Image;

use Czim\FileHandling\Contracts\Support\ImageResizerInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;
use InvalidArgumentException;
use SplFileInfo;

/**
 * This is based on (a slight rewrite of) the Codesleeve Stapler resizer.
 */
class Resizer implements ImageResizerInterface
{
    /**
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * @param ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * Resize an image using given options.
     *
     * @param SplFileInfo $file
     * @param array       $options
     * @return bool
     */
    public function resize(SplFileInfo $file, array $options): bool
    {
        $filePath = $file->getRealPath();

        [$width, $height, $option] = $this->parseOptionDimensions($options);

        $method = 'resize' . ucfirst($option);

        // Custom resize (that still expects imagine to be used)
        if ($method == 'resizeCustom') {
            $callable = $options['dimensions'];

            $this
                ->resizeCustom($file, $callable)
                ->save($filePath, $this->getConvertOptions($options));

            return true;
        }

        $image = $this->imagine->open($file->getRealPath());

        $this->$method($image, $width, $height)
            ->save($filePath, $this->getConvertOptions($options));

        return true;
    }


    /**
     * Returns the convert options for image manipulation.
     *
     * @param array $options
     * @return array
     */
    protected function getConvertOptions(array $options): array
    {
        if ($this->arrHas($options, 'convertOptions')) {
            return $this->arrGet($options, 'convertOptions', []);
        }

        return $this->arrGet($options, 'convert_options', []);
    }

    /**
     * Parses the dimensions given in the options.
     *
     * Parse the given style dimensions to extract out the file processing options,
     * perform any necessary image resizing for a given style.
     *
     * @param array $options
     * @return array
     */
    protected function parseOptionDimensions(array $options): array
    {
        $sourceDimensions = $this->arrGet($options, 'dimensions');

        if (is_callable($sourceDimensions)) {
            return [null, null, 'custom'];
        }

        $dimensions = explode('x', $sourceDimensions);

        if (count($dimensions) === 1 || $dimensions[1] === '') {
            // Width given, height automagically selected to preserve aspect ratio (landscape).
            $width = $dimensions[0];

            return [$width, null, 'landscape'];
        }

        $width  = $dimensions[0];
        $height = $dimensions[1];

        if (empty($width)) {
            // Height given, width matched to preserve aspect ratio (portrait)
            return [null, $height, 'portrait'];
        }

        $resizingOption = substr($height, -1, 1);


        if ($resizingOption == '#') {
            // Resize, then crop
            $height = rtrim($height, '#');

            return [$width, $height, 'crop'];
        }

        if ($resizingOption == '!') {
            // Resize by exact width/height (ignore ratio)
            $height = rtrim($height, '!');

            return [$width, $height, 'exact'];
        }

        return [$width, $height, 'auto'];
    }


    /**
     * Resize an image as closely as possible to a given width and height while still maintaining aspect ratio
     *
     * This method is really just a proxy to other resize methods:.
     *
     * If the current image is wider than it is tall, we'll resize landscape.
     * If the current image is taller than it is wide, we'll resize portrait.
     * If the image is as tall as it is wide (it's a squarey) then we'll
     * apply the same process using the new dimensions (we'll resize exact if
     * the new dimensions are both equal since at this point we'll have a square
     * image being resized to a square).
     *
     * @param ImageInterface $image
     * @param string         $width     new width
     * @param string         $height    new height
     * @return ImageInterface
     */
    protected function resizeAuto(ImageInterface $image, string $width, string $height): ImageInterface
    {
        $size           = $image->getSize();
        $originalWidth  = $size->getWidth();
        $originalHeight = $size->getHeight();

        if ($originalHeight < $originalWidth) {
            return $this->resizeLandscape($image, $width, $height);
        }

        if ($originalHeight > $originalWidth) {
            return $this->resizePortrait($image, $width, $height);
        }

        if ($height < $width) {
            return $this->resizeLandscape($image, $width, $height);
        }

        if ($height > $width) {
            return $this->resizePortrait($image, $width, $height);
        }

        return $this->resizeExact($image, $width, $height);
    }

    /**
     * Resize an image as a landscape (width fixed).
     *
     * @param ImageInterface $image
     * @param string         $width     new width
     * @param string|null    $height    new height
     * @return ImageInterface
     */
    protected function resizeLandscape(ImageInterface $image, string $width, ?string $height): ImageInterface
    {
        // @codeCoverageIgnoreStart
        if (empty($width)) {
            throw new InvalidArgumentException(
                'Width value for portrait resize is empty. This may be caused by unfixed EXIF-rotated images.'
            );
        }
        // @codeCoverageIgnoreEnd

        $optimalHeight = $this->getSizeByFixedWidth($image, $width);

        $dimensions = $image->getSize()
            ->widen($width)
            ->heighten($optimalHeight);

        $image = $image->resize($dimensions);

        return $image;
    }

    /**
     * Resize an image as a portrait (height fixed).
     *
     * @param ImageInterface $image
     * @param string|null    $width     new width
     * @param string         $height    new height
     * @return ImageInterface
     */
    protected function resizePortrait(ImageInterface $image, ?string $width, string $height): ImageInterface
    {
        // @codeCoverageIgnoreStart
        if (empty($height)) {
            throw new InvalidArgumentException(
                'Height value for portrait resize is empty. This may be caused by unfixed EXIF-rotated images'
            );
        }
        // @codeCoverageIgnoreEnd

        $optimalWidth = $this->getSizeByFixedHeight($image, $height);

        $dimensions = $image->getSize()
            ->heighten($height)
            ->widen($optimalWidth);

        $image = $image->resize($dimensions);

        return $image;
    }

    /**
     * Resize an image and then center crop it
     *
     * @param ImageInterface $image
     * @param string         $width     new width
     * @param string         $height    new height
     * @return ImageInterface
     */
    protected function resizeCrop(ImageInterface $image, string $width, string $height): ImageInterface
    {
        [$optimalWidth, $optimalHeight] = $this->getOptimalCrop($image->getSize(), $width, $height);

        // Find center - this will be used for the crop
        $centerX = ($optimalWidth / 2) - ($width / 2);
        $centerY = ($optimalHeight / 2) - ($height / 2);

        return $image->resize(new Box($optimalWidth, $optimalHeight))
            ->crop(new Point($centerX, $centerY), new Box($width, $height));
    }

    /**
     * Resize an image to an exact width and height.
     *
     * @param ImageInterface $image
     * @param string         $width     new width
     * @param string         $height    new height
     * @return ImageInterface
     */
    protected function resizeExact(ImageInterface $image, string $width, string $height): ImageInterface
    {
        return $image->resize(new Box($width, $height));
    }

    /**
     * Resize an image using a user defined callback.
     *
     * @param SplFileInfo $file
     * @param callable    $callable
     * @return ImageInterface
     */
    protected function resizeCustom(SplFileInfo $file, callable $callable): ImageInterface
    {
        return call_user_func_array($callable, [$file, $this->imagine]);
    }

    /**
     * Returns the width based on the new image height.
     *
     * @param ImageInterface $image
     * @param int            $newHeight - The image's new height.
     * @return int
     */
    private function getSizeByFixedHeight(ImageInterface $image, int $newHeight): int
    {
        $box   = $image->getSize();
        $ratio = $box->getWidth() / $box->getHeight();

        return (int) $newHeight * $ratio;
    }

    /**
     * Returns the height based on the new image width.
     *
     * @param ImageInterface $image
     * @param int            $newWidth - The image's new width.
     * @return int
     */
    private function getSizeByFixedWidth(ImageInterface $image, int $newWidth): int
    {
        $box   = $image->getSize();
        $ratio = $box->getHeight() / $box->getWidth();

        return (int) $newWidth * $ratio;
    }

    /**
     * Attempts to find the best way to crop.
     * Takes into account the image being a portrait or landscape.
     *
     * @param BoxInterface $size    current size.
     * @param string       $width   new width.
     * @param string       $height  new height.
     * @return array
     */
    protected function getOptimalCrop(BoxInterface $size, string $width, string $height): array
    {
        $heightRatio = $size->getHeight() / $height;
        $widthRatio  = $size->getWidth() / $width;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = round($size->getHeight() / $optimalRatio, 2);
        $optimalWidth  = round($size->getWidth() / $optimalRatio, 2);

        return [$optimalWidth, $optimalHeight];
    }

    /**
     * Safely get array value from config array.
     *
     * @param array      $array
     * @param string     $key
     * @param null|mixed $default
     * @return mixed|null
     */
    protected function arrGet(array $array, string $key, $default = null)
    {
        // @codeCoverageIgnoreStart
        if (! $this->arrHas($array, $key)) {
            return $default;
        }
        // @codeCoverageIgnoreEnd

        return $array[ $key ];
    }

    protected function arrHas(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }
}
