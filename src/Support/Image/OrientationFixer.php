<?php

namespace Czim\FileHandling\Support\Image;

use ErrorException;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use SplFileInfo;

/**
 * Class OrientationFixer
 *
 * Uses Exif data to fix the orientation of an image, if it is rotated or flipped.
 */
class OrientationFixer
{
    public const ORIENTATION_TOPLEFT     = 1;
    public const ORIENTATION_TOPRIGHT    = 2;
    public const ORIENTATION_BOTTOMRIGHT = 3;
    public const ORIENTATION_BOTTOMLEFT  = 4;
    public const ORIENTATION_LEFTTOP     = 5;
    public const ORIENTATION_RIGHTTOP    = 6;
    public const ORIENTATION_RIGHTBOTTOM = 7;
    public const ORIENTATION_LEFTBOTTOM  = 8;

    /**
     * Whether to silently ignore exceptions.
     *
     * @var bool
     */
    protected $quiet = true;

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


    public function enableQuietMode(): void
    {
        $this->quiet = true;
    }

    public function disableQuietMode(): void
    {
        $this->quiet = false;
    }

    public function isQuiet(): bool
    {
        return $this->quiet;
    }

    /**
     * Fixes the orientation in a local file.
     *
     * This overwrites the current file.
     *
     * @param SplFileInfo $file
     * @return bool
     * @throws ErrorException
     */
    public function fixFile(SplFileInfo $file): bool
    {
        $filePath = $file->getRealPath();

        $image = $this->imagine->open($file->getRealPath());

        $image = $this->fixImage($filePath, $image);
        $image->save();

        return true;
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
     * @throws ErrorException
     */
    public function fixImage(string $path, ImageInterface $image): ImageInterface
    {
        // @codeCoverageIgnoreStart
        if (! function_exists('exif_read_data')) {
            return $image;
        }
        // @codeCoverageIgnoreEnd

        if ($this->quiet) {

            try {
                $exif = @exif_read_data($path);
                // @codeCoverageIgnoreStart
            } catch (ErrorException $e) {
                if ($this->quiet) {
                    return $image;
                }
                throw $e;
                // @codeCoverageIgnoreEnd
            }

        } else {
            $exif = exif_read_data($path);
        }

        if (! isset($exif['Orientation']) || $exif['Orientation'] == static::ORIENTATION_TOPLEFT) {
            return $image;
        }

        switch ($exif['Orientation']) {

            case static::ORIENTATION_TOPRIGHT:
                $image->flipHorizontally();
                break;

            case static::ORIENTATION_BOTTOMRIGHT:
                $image->rotate(180);
                break;

            case static::ORIENTATION_BOTTOMLEFT:
                $image->flipVertically();
                break;

            case static::ORIENTATION_LEFTTOP:
                $image->flipVertically()->rotate(90);
                break;

            case static::ORIENTATION_RIGHTTOP:
                $image->rotate(90);
                break;

            case static::ORIENTATION_RIGHTBOTTOM:
                $image->flipHorizontally()->rotate(90);
                break;

            case static::ORIENTATION_LEFTBOTTOM:
                $image->rotate(-90);
                break;
        }

        return $image->strip();
    }
}
