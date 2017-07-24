<?php
namespace Czim\FileHandling\Contracts\Storage;

interface StorageInterface
{

    /**
     * Returns whether a stored media file exists.
     *
     * If a variant key is given, existance of the variant is returned.
     *
     * @param string      $path
     * @param string|null $variant  the key/name for a variant of the file
     * @return bool
     */
    public function exists($path, $variant = null);

    /**
     * Returns a public URL to the stored media file.
     *
     * @param string      $path
     * @param string|null $variant
     * @return string
     */
    public function url($path, $variant = null);

    /**
     * Returns the file from storage.
     *
     * @param string      $path
     * @param string|null $variant
     * @return StorableFileInterface
     */
    public function get($path, $variant = null);

    /**
     * Stores a media file.
     *
     * If a variant key is given, the given data will be stored as a variant of the file.
     *
     * @param StorableFileInterface $data       mixed content to store
     * @param string                $path       where the file should be stored
     * @param string|null           $variant    the key/name for a variant of the file
     */
    public function store(StorableFileInterface $data, $path, $variant = null);

    /**
     * Deletes a stored media file.
     *
     * @param string               $path
     * @param string|string[]|null $variants    if set, only delete these variants
     * @return bool
     */
    public function delete($path, $variants = null);

}
