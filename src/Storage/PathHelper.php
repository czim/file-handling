<?php
namespace Czim\FileHandling\Storage;

use Czim\FileHandling\Contracts\Storage\PathHelperInterface;
use Czim\FileHandling\Handler\FileHandler;

class PathHelper implements PathHelperInterface
{

    /**
     * Modifies a base storage path for a specific variant.
     *
     * @param string $path
     * @param string $variant
     * @return string
     */
    public function addVariantToBasePath($path, $variant = FileHandler::ORIGINAL)
    {
        return rtrim($path, '/') . '/' . $variant;
    }

    /**
     * Modifies a full storage path, replacing one variant for another.
     *
     * @param string $path      full path with (other) variant
     * @param string $variant
     * @return string
     */
    public function replaceVariantInPath($path, $variant = FileHandler::ORIGINAL)
    {
        return $this->basePath($path) . '/' . $variant;
    }

    /**
     * Returns the base path for a full path for a variant.
     *
     * @param string $path
     * @return string
     */
    public function basePath($path)
    {
        $segments = explode('/', rtrim($path, '/'));

        if ( ! count($segments)) {
            return '';
        }

        array_pop($segments);

        return implode('/', $segments);
    }

}
