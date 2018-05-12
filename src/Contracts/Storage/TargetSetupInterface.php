<?php
namespace Czim\FileHandling\Contracts\Storage;

interface TargetSetupInterface
{

    /**
     * @param string[] $filenames
     * @return $this
     */
    public function setVariantFilenames(array $filenames);

    /**
     * @param string[] $extensions
     * @return $this
     */
    public function setVariantExtensions(array $extensions);

    /**
     * @param string $variant
     * @param string $filename
     * @return $this
     */
    public function setVariantFilename($variant, $filename);

    /**
     * @param string $variant
     * @param string $extension
     * @return $this
     */
    public function setVariantExtension($variant, $extension);

}
