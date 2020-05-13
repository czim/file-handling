<?php

namespace Czim\FileHandling\Contracts\Storage;

interface TargetSetupInterface
{
    /**
     * @param string[] $filenames
     */
    public function setVariantFilenames(array $filenames): void;

    /**
     * @param string[] $extensions
     */
    public function setVariantExtensions(array $extensions): void;

    /**
     * @param string $variant
     * @param string $filename
     */
    public function setVariantFilename(string $variant, string $filename): void;

    /**
     * @param string $variant
     * @param string $extension
     */
    public function setVariantExtension(string $variant, string $extension): void;
}
