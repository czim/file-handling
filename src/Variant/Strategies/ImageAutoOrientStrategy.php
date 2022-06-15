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
    protected function perform(): ?bool
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
    protected function isQuietModeDisabled(): bool
    {
        if (! array_key_exists('quiet', $this->options)) {
            return false;
        }

        return ! $this->options['quiet'];
    }

    /**
     * {@inheritDoc}
     */
    protected function shouldBeApplied(): bool
    {
        if (! parent::shouldBeApplied()) {
            return false;
        }

        return empty($this->file->extension())
            || $this->doesFileExtensionSupportExif($this->file->extension());
    }

    protected function doesFileExtensionSupportExif(string $extension): bool
    {
        return in_array(strtolower($extension), [
            // Standard formats
            'jpg',
            'jpeg',
            'tiff',
            'wav',

            // Raw camera formats
            '3fr',
            'ari',
            'cap',
            'cr2',
            'crw',
            'dat',
            'dcf',
            'dcr',
            'dcs',
            'dng',
            'eip',
            'erf',
            'k25',
            'kdc',
            'lfp',
            'ndf',
            'nef',
            'nrw',
            'nef',
            'pxn',
            'r3d',
            'raf',
            'raw',
            'rdc',
            'rw2',
            'rwz',
        ]);
    }
}
