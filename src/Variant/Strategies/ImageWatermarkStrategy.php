<?php

namespace Czim\FileHandling\Variant\Strategies;

use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use RuntimeException;

class ImageWatermarkStrategy extends AbstractImageStrategy
{
    public const POSITION_TOP_LEFT     = 'top-left';
    public const POSITION_TOP_RIGHT    = 'top-right';
    public const POSITION_BOTTOM_LEFT  = 'bottom-left';
    public const POSITION_BOTTOM_RIGHT = 'bottom-right';
    public const POSITION_CENTER       = 'center';

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
    protected function perform(): ?bool
    {
        $image = $this->imagine->open($this->file->path());

        if (! ($watermark = $this->getWatermarkImage())) {
            return false;
        }

        $imageSize     = $image->getSize();
        $watermarkSize = $watermark->getSize();

        // Calculate the placement of the watermark (we're aiming for the bottom right corner here).

        // Place the image in the correct position
        switch ($this->getWatermarkPosition()) {
            case static::POSITION_TOP_LEFT:
                $position = new Point(0, 0);
                break;

            case static::POSITION_TOP_RIGHT:
                $position = new Point(
                    $imageSize->getWidth() - $watermarkSize->getWidth(),
                    0
                );
                break;

            case static::POSITION_BOTTOM_LEFT:
                $position = new Point(
                    0,
                    $imageSize->getHeight() - $watermarkSize->getHeight()
                );
                break;

            case static::POSITION_CENTER;
                $position = new Point(
                    ((int) round($imageSize->getWidth() / 2)) - ((int) round($watermarkSize->getWidth() / 2)),
                    ((int) round($imageSize->getHeight() / 2)) - ((int) round($watermarkSize->getHeight() / 2))
                );
                break;

            case static::POSITION_BOTTOM_RIGHT:
            default:
                $position = new Point(
                    $imageSize->getWidth() - $watermarkSize->getWidth(),
                    $imageSize->getHeight() - $watermarkSize->getHeight()
                );
        }

        $image->paste($watermark, $position);
        $image->save();

        return true;
    }

    protected function getWatermarkImage(): ?ImageInterface
    {
        // Get image to embed as watermark
        $watermarkPath = $this->getWatermarkImagePath();

        if (! $watermarkPath) {
            return null;
        }

        try {
            return $this->imagine->open($watermarkPath);
        } catch (\Exception $e) {
            throw new RuntimeException("Could not find or open watermark image at '{$watermarkPath}'", $e->getCode(), $e);
        }
    }

    protected function getWatermarkPosition(): string
    {
        if (array_key_exists('position', $this->options)) {
            return $this->options['position'];
        }

        return 'bottom-right';
    }

    protected function getWatermarkImagePath(): ?string
    {
        if (array_key_exists('watermark', $this->options)) {
            return $this->options['watermark'];
        }

        return null;
    }
}
