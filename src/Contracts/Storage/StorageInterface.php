<?php
namespace Czim\FileHandling\Contracts\Storage;

interface StorageInterface
{

    /**
     * Returns whether a stored file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists($path);

    /**
     * Returns a public URL to the stored file.
     *
     * @param string $path
     * @return string
     */
    public function url($path);

    /**
     * Returns the file from storage.
     *
     * @param string $path
     * @return StoredFileInterface
     */
    public function get($path);

    /**
     * Stores a file.
     *
     * @param StorableFileInterface $file   mixed content to store
     * @param string                $path   where the file should be stored, including filename
     * @return StoredFileInterface
     */
    public function store(StorableFileInterface $file, $path);

    /**
     * Deletes a stored media file.
     *
     * @param string $path
     * @return bool
     */
    public function delete($path);

}
