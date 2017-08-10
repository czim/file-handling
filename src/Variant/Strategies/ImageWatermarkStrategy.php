<?php
namespace Czim\FileHandling\Variant\Strategies;

use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use RuntimeException;

class ImageWatermarkStrategy extends AbstractImageStrategy
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
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform()
    {
        $image = $this->imagine->open($this->file->path());

        if ( ! ($watermark = $this->getWatermarkImage())) {
            return false;
        }


        $imageSize     = $image->getSize();
        $watermarkSize = $watermark->getSize();

        // Calculate the placement of the watermark (we're aiming for the bottom right corner here).


        // Place the image in the correct position
        switch ($this->getWatermarkPosition()) {

            case 'top-left':
                $position = new \Imagine\Image\Point(0, 0);
                break;

            case 'top-right':
                $position = new \Imagine\Image\Point(
                    $imageSize->getWidth() - $watermarkSize->getWidth(),
                    0
                );
                break;

            case 'bottom-left':
                $position = new \Imagine\Image\Point(
                    0,
                    $imageSize->getHeight() - $watermarkSize->getHeight()
                );
                break;

            case 'center';
                $position = new \Imagine\Image\Point(
                    ((int) round($imageSize->getWidth() / 2)) - ((int) round($watermarkSize->getWidth() / 2)),
                    ((int) round($imageSize->getHeight() / 2)) - ((int) round($watermarkSize->getHeight() / 2))
                );
                break;

            case 'bottom-right':
            default:
                $position = new \Imagine\Image\Point(
                    $imageSize->getWidth() - $watermarkSize->getWidth(),
                    $imageSize->getHeight() - $watermarkSize->getHeight()
                );
        }

        $image->paste($watermark, $position);
        $image->save();

        return true;
    }

    /**
     * @return ImageInterface|bool
     */
    protected function getWatermarkImage()
    {
        // Get image to embed as watermark
        $watermarkPath = $this->getWatermarkImagePath();

        if ( ! $watermarkPath) {
            return false;
        }

        try {
            return $this->imagine->open($watermarkPath);

        } catch (\Exception $e) {

            throw new RuntimeException("Could not find or open watermark image at '{$watermarkPath}'", $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    protected function getWatermarkPosition()
    {
        if (array_key_exists('position', $this->options)) {
            return $this->options['position'];
        }

        return 'bottom-right';
    }

    /**
     * @return string|false
     */
    protected function getWatermarkImagePath()
    {
        if (array_key_exists('watermark', $this->options)) {
            return $this->options['watermark'];
        }

        return false;
    }

}
