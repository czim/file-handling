<?php
namespace Czim\FileHandling\Contracts\Storage;

interface PathHelperInterface
{

    /**
     * Modifies a base storage path for a specific variant.
     *
     * @param string $path
     * @param string $variant
     * @return string
     */
    public function addVariantToBasePath($path, $variant = 'original');

    /**
     * Modifies a full storage path, replacing one variant for another.
     *
     * @param string $path      full path with (other) variant
     * @param string $variant
     * @return string
     */
    public function replaceVariantInPath($path, $variant = 'original');

    /**
     * Returns the base path for a full path for a variant.
     *
     * @param string $path
     * @return string
     */
    public function basePath($path);

}
