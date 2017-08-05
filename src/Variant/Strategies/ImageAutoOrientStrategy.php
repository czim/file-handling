<?php
namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\OrientationFixer;
use Imagine\Gd\Imagine;

class ImageAutoOrientStrategy extends AbstractImageStrategy
{

    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform()
    {
        $fixer = $this->getOrientationFixer();

        if ($this->isQuietModeDisabled()) {
            $fixer->disableQuietMode();
        }

        return (bool) $fixer->fixFile($this->file, new Imagine);
    }

    /**
     * @return OrientationFixer
     */
    protected function getOrientationFixer()
    {
        return new OrientationFixer;
    }

    /**
     * Returns whether we should throw exceptions on exif problems.
     *
     * @return bool
     */
    protected function isQuietModeDisabled()
    {
        if ( ! array_key_exists('quiet', $this->options)) {
            return false;
        }

        return ! $this->options['quiet'];
    }

}
