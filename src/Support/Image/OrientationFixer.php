<?php
namespace Czim\FileHandling\Support\Image;

use ErrorException;
use Imagine\Gd\Imagine;
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
    const ORIENTATION_CORRECT                               = 1;
    const ORIENTATION_FLIPPED_HORIZONTALLY                  = 2;
    const ORIENTATION_UPSIDE_DOWN                           = 3;
    const ORIENTATION_FLIPPED_VERTICALLY                    = 4;
    const ORIENTATION_FLIPPED_VERTICALLY_AND_ROTATED_LEFT   = 5;
    const ORIENTATION_ROTATED_LEFT                          = 6;
    const ORIENTATION_FLIPPED_HORIZONTALLY_AND_ROTATED_LEFT = 7;
    const ORIENTATION_ROTATED_RIGHT                         = 8;


    /**
     * Fixes the orientation in a local file.
     *
     * This overwrites the current file.
     *
     * @param SplFileInfo           $file
     * @param ImagineInterface|null $imagine
     * @return bool
     */
    public function fixFile(SplFileInfo $file, ImagineInterface $imagine = null)
    {
        $imagine = $imagine ?: new Imagine;

        $filePath = $file->getRealPath();

        $image = $imagine->open($file->getRealPath());

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
     */
    public function fixImage($path, ImageInterface $image)
    {
        // @codeCoverageIgnoreStart
        if ( ! function_exists('exif_read_data')) {
            return $image;
        }
        // @codeCoverageIgnoreEnd

        try {
            $exif = exif_read_data($path);
            // @codeCoverageIgnoreStart
        } catch (ErrorException $e) {
            return $image;
            // @codeCoverageIgnoreEnd
        }

        if ( ! isset($exif['Orientation']) || $exif['Orientation'] == static::ORIENTATION_CORRECT) {
            return $image;
        }

        switch ($exif['Orientation']) {

            case static::ORIENTATION_FLIPPED_HORIZONTALLY:
                $image->flipHorizontally();
                break;

            case static::ORIENTATION_UPSIDE_DOWN:
                $image->rotate(180);
                break;

            case static::ORIENTATION_FLIPPED_VERTICALLY:
                $image->flipVertically();
                break;

            case static::ORIENTATION_FLIPPED_VERTICALLY_AND_ROTATED_LEFT:
                $image->flipVertically()->rotate(90);
                break;

            case static::ORIENTATION_ROTATED_LEFT:
                $image->rotate(90);
                break;

            case static::ORIENTATION_FLIPPED_HORIZONTALLY_AND_ROTATED_LEFT:
                $image->flipHorizontally()->rotate(90);
                break;

            case static::ORIENTATION_ROTATED_RIGHT:
                $image->rotate(-90);
                break;
        }

        return $image->strip();
    }

}
