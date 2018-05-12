<?php
namespace Czim\FileHandling\Storage\Path;

use Czim\FileHandling\Contracts\Storage\TargetInterface;
use Czim\FileHandling\Contracts\Storage\TargetSetupInterface;

class Target implements TargetInterface, TargetSetupInterface
{
    const VARIANT_PLACEHOLDER = ':variant';


    /**
     * The path to the original file.
     *
     * @var string
     */
    protected $originalPath;

    /**
     * If the variant path structure is different from the original path,
     * it may be set here. :variant is the expected placeholder for the
     * variant name.
     *
     * If this is not set, it is expected that the directory directly
     * above that in which the original path's file resides is the
     * 'variant' directory. (/base/original/file.ext would become
     * /base/variantname/file.ext for a different variant).
     *
     * @var string|null
     */
    protected $variantPath = null;

    /**
     * Optional mapping of alternative filenames to use per variant.
     * This should exclude the extension.
     *
     * @var string[]
     */
    protected $variantFileNames = [];

    /**
     * Optional mapping of alternative extensions to use per variant.
     * These should not include the separating period (.).
     *
     * @var string[]
     */
    protected $variantExtensions = [];


    /**
     * @param string      $path
     * @param string|null $variantPath      use :variant as a placeholder
     */
    public function __construct($path, $variantPath = null)
    {
        $this->originalPath = $path;
        $this->variantPath  = $variantPath;
    }


    /**
     * @param string[] $filenames
     * @return $this
     */
    public function setVariantFilenames(array $filenames)
    {
        $this->variantFileNames = $filenames;

        return $this;
    }

    /**
     * @param string[] $extensions
     * @return $this
     */
    public function setVariantExtensions(array $extensions)
    {
        $this->variantExtensions = $extensions;

        return $this;
    }

    /**
     * @param string $variant
     * @param string $filename
     * @return $this
     */
    public function setVariantFilename($variant, $filename)
    {
        $this->variantFileNames[ $variant ] = $filename;

        return $this;
    }

    /**
     * @param string $variant
     * @param string $extension
     * @return $this
     */
    public function setVariantExtension($variant, $extension)
    {
        $this->variantExtensions[ $variant ] = $extension;

        return $this;
    }


    /**
     * Returns the (relative) target path for the original file.
     *
     * @return string
     */
    public function original()
    {
        return $this->originalPath;
    }

    /**
     * Returns the (relative) target path for a variant by name.
     *
     * @param string $variant
     * @return string
     */
    public function variant($variant)
    {
        $variantPath = $this->getVariantPathWithPlaceholder();

        $path = str_replace(static::VARIANT_PLACEHOLDER, $variant, $variantPath);

        if (array_key_exists($variant, $this->variantFileNames)) {
            $path = $this->replaceFileName($path, $this->variantFileNames[ $variant ]);
        }

        if (array_key_exists($variant, $this->variantExtensions)) {
            $path = $this->replaceExtension($path, $this->variantExtensions[ $variant ]);
        }

        return $path;
    }


    /**
     * @return string
     */
    protected function getVariantPathWithPlaceholder()
    {
        if ($this->variantPath) {
            return $this->variantPath;
        }

        // Alter the original path to make it a variant path.
        $file = pathinfo($this->originalPath, PATHINFO_BASENAME);
        $dir  = pathinfo($this->originalPath, PATHINFO_DIRNAME);

        $dir = $this->replaceLastLevelDirectory($dir, static::VARIANT_PLACEHOLDER);

        return $dir . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @param string $dirname
     * @param string $replacement
     * @return string
     */
    protected function replaceLastLevelDirectory($dirname, $replacement)
    {
        $segments = explode(DIRECTORY_SEPARATOR, $dirname);

        if (count($segments) < 2) {
            return $replacement;
        }

        array_pop($segments);

        return implode(DIRECTORY_SEPARATOR, $segments)
             . DIRECTORY_SEPARATOR
             . $replacement;
    }

    /**
     * @param string $path
     * @param string $extension
     * @return string
     */
    protected function replaceExtension($path, $extension)
    {
        return pathinfo($path, PATHINFO_DIRNAME)
             . DIRECTORY_SEPARATOR
             . pathinfo($path, PATHINFO_FILENAME)
             . ($extension ? '.' . $extension : null);
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    protected function replaceFileName($path, $filename)
    {
        return pathinfo($path, PATHINFO_DIRNAME)
            . DIRECTORY_SEPARATOR
            . $filename
            . '.' . pathinfo($path, PATHINFO_EXTENSION);
    }

}
