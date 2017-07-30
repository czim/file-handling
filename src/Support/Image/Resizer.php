<?php
namespace Czim\FileHandling\Support\Image;

use Czim\FileHandling\Contracts\Support\ImageResizerInterface;
use ErrorException;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;
use SplFileInfo;

/**
 * Class Resizer
 *
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
     * @return string   resized file path
     */
    public function resize(SplFileInfo $file, array $options)
    {
        $filePath = $file->getRealPath();

        list($width, $height, $option) = $this->parseOptionDimensions($options);

        $method = 'resize' . ucfirst($option);

        // Custom resize (that still expects imagine to be used)
        if ($method == 'resizeCustom') {

            $callable = $options['dimensions'];

            $this
                ->resizeCustom($file, $callable)
                ->save($filePath, $this->arrGet($options, 'convertOptions'));

            return $filePath;
        }

        $image = $this->imagine->open($file->getRealPath());

        if ($this->arrGet($options, 'autoOrient')) {
            $image = $this->autoOrient($file->getRealPath(), $image);
        }

        $this->$method($image, $width, $height)
           ->save($filePath, $this->arrGet($options, 'convertOptions'));

        return $filePath;
    }


    /**
     * Parses the dimensions given in the options.
     *
     * Parse the given style dimensions to extract out the file processing options,
     * perform any necessary image resizing for a given style.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptionDimensions(array $options)
    {
        $sourceDimensions = $this->arrGet($options, 'dimensions');

        if (is_callable($sourceDimensions)) {
            return [null, null, 'custom'];
        }

        if (strpos($sourceDimensions, 'x') === false) {
            // Width given, height automagically selected to preserve aspect ratio (landscape).
            $width = $sourceDimensions;

            return [$width, null, 'landscape'];
        }

        $dimensions = explode('x', $sourceDimensions);
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
     * Resize an image as closely as possible to a given
     * width and height while still maintaining aspect ratio.
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
    protected function resizeAuto(ImageInterface $image, $width, $height)
    {
        $size = $image->getSize();
        $originalWidth = $size->getWidth();
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
     * @param string         $height    new height
     * @return ImageInterface
     */
    protected function resizeLandscape(ImageInterface $image, $width, $height)
    {
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
     * @param string         $width  - The image's new width.
     * @param string         $height - The image's new height.
     *
     * @return ImageInterface
     */
    protected function resizePortrait(ImageInterface $image, $width, $height)
    {
        $optimalWidth = $this->getSizeByFixedHeight($image, $height);
        $dimensions = $image->getSize()
            ->heighten($height)
            ->widen($optimalWidth);

        $image = $image->resize($dimensions);

        return $image;
    }

    /**
     * Resize an image and then center crop it.
     *
     * @param ImageInterface $image
     * @param string         $width  - The image's new width.
     * @param string         $height - The image's new height.
     *
     * @return ImageInterface
     */
    protected function resizeCrop(ImageInterface $image, $width, $height)
    {
        list($optimalWidth, $optimalHeight) = $this->getOptimalCrop($image->getSize(), $width, $height);

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
     * @param string         $width  - The image's new width.
     * @param string         $height - The image's new height.
     *
     * @return ImageInterface
     */
    protected function resizeExact(ImageInterface $image, $width, $height)
    {
        return $image->resize(new Box($width, $height));
    }

    /**
     * Resize an image using a user defined callback.
     *
     * @param SplFileInfo $file
     * @param  $callable
     *
     * @return ImageInterface
     */
    protected function resizeCustom(SplFileInfo $file, callable $callable)
    {
        return call_user_func_array($callable, [$file, $this->imagine]);
    }

    /**
     * Returns the width based on the new image height.
     *
     * @param ImageInterface $image
     * @param int            $newHeight - The image's new height.
     *
     * @return int
     */
    private function getSizeByFixedHeight(ImageInterface $image, $newHeight)
    {
        $box   = $image->getSize();
        $ratio = $box->getWidth() / $box->getHeight();

        $newWidth = $newHeight * $ratio;

        return $newWidth;
    }

    /**
     * Returns the height based on the new image width.
     *
     * @param ImageInterface $image
     * @param int            $newWidth - The image's new width.
     *
     * @return int
     */
    private function getSizeByFixedWidth(ImageInterface $image, $newWidth)
    {
        $box   = $image->getSize();
        $ratio = $box->getHeight() / $box->getWidth();

        $newHeight = $newWidth * $ratio;

        return $newHeight;
    }

    /**
     * Attempts to find the best way to crop.
     * Takes into account the image being a portrait or landscape.
     *
     * @param BoxInterface $size    current size.
     * @param string       $width   new width.
     * @param string       $height  new height.
     *
     * @return array
     */
    protected function getOptimalCrop(BoxInterface $size, $width, $height)
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

        return [ $optimalWidth, $optimalHeight ];
    }

    /**
     * Re-orient an image using its embedded Exif profile orientation.
     *
     * 1. Attempt to read the embedded exif data inside the image to determine it's orientation.
     *    if there is no exif data (i.e an exeption is thrown when trying to read it) then we'll
     *    just return the image as is.
     * 2. If there is exif data, we'll rotate and flip the image accordingly to re-orient it.
     * 3. Finally, we'll strip the exif data from the image so that there can be no
     *    attempt to 'correct' it again.
     *
     * @param string         $path
     * @param ImageInterface $image
     * @return ImageInterface $image
     */
    protected function autoOrient($path, ImageInterface $image)
    {
        if ( ! function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $exif = exif_read_data($path);
        } catch (ErrorException $e) {
            return $image;
        }

        if (isset($exif['Orientation'])) {

            switch ($exif['Orientation']) {

                case 2:
                    $image->flipHorizontally();
                    break;

                case 3:
                    $image->rotate(180);
                    break;
                case 4:
                    $image->flipVertically();
                    break;

                case 5:
                    $image->flipVertically()
                        ->rotate(90);
                    break;

                case 6:
                    $image->rotate(90);
                    break;

                case 7:
                    $image->flipHorizontally()
                        ->rotate(90);
                    break;

                case 8:
                    $image->rotate(-90);
                    break;
            }
        }

        return $image->strip();
    }

    /**
     * Safely get array value from config array.
     *
     * @param array      $array
     * @param string     $key
     * @param null|mixed $default
     * @return mixed|null
     */
    protected function arrGet(array $array, $key, $default = null)
    {
        if ( ! array_key_exists($key, $array)) {
            return $default;
        }

        return $array[ $key ];
    }

}
