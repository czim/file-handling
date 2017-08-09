<?php
namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\OrientationFixer;
use SplFileInfo;

class ImageAutoOrientStrategy extends AbstractImageStrategy
{

    /**
     * @var OrientationFixer
     */
    protected $fixer;


    /**
     * @param OrientationFixer $fixer
     */
    public function __construct(OrientationFixer $fixer)
    {
        $this->fixer = $fixer;
    }


    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform()
    {
        if ($this->isQuietModeDisabled()) {
            $this->fixer->disableQuietMode();
        }

        $spl = new SplFileInfo($this->file->path());

        return (bool) $this->fixer->fixFile($spl);
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
