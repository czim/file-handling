<?php
namespace Czim\FileHandling\Contracts\Handler;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

interface FileHandlerInterface
{

    /**
     * Processes and stores a storable file.
     *
     * @param StorableFileInterface $source
     * @param string $targetPath
     * @param array $options
     * @return StoredFileInterface[]    keyed by variant name
     */
    public function process(StorableFileInterface $source, $targetPath, array $options = []);

    /**
     * Processes and stores a single variant for a storable file.
     *
     * @param StorableFileInterface $source
     * @param string                $targetPath
     * @param string                $variant
     * @param array                 $options
     * @return StoredFileInterface
     */
    public function processVariant(StorableFileInterface $source, $targetPath, $variant, array $options = []);

    /**
     * @param StoredFileInterface $file
     * @param string[]            $variants
     * @return string[]
     */
    public function variantUrlsForStoredFile(StoredFileInterface $file, array $variants = []);

    /**
     * Returns the URLs keyed by the variant keys requested.
     *
     * @param string   $path        base path without variant and filename
     * @param string   $file        file name
     * @param string[] $variants    keys for variants to include
     * @return string[]
     */
    public function variantUrlsForBasePath($path, $file, array $variants = []);

    /**
     * Deletes a file and all indicated variants.
     *
     * @param string   $basePath
     * @param string   $file
     * @param string[] $variants    variant keys
     * @return bool
     */
    public function delete($basePath, $file, array $variants = []);

    /**
     * Deletes a single variant.
     *
     * @param string      $path         may be a full file path, or a base path
     * @param null|string $variant      must be given if file path is not full
     * @param null|string $file         must be given if file path is not full
     * @return bool
     */
    public function deleteVariant($path, $variant = null, $file = null);

}
