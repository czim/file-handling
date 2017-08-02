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

        return (bool) $fixer->fixFile($this->file, new Imagine);
    }

    /**
     * @return OrientationFixer
     */
    protected function getOrientationFixer()
    {
        return new OrientationFixer;
    }

}
