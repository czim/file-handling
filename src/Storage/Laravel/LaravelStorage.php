<?php

namespace Czim\FileHandling\Storage\Laravel;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorageInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Exceptions\FileStorageException;
use Czim\FileHandling\Storage\File\DecoratorStoredFile;
use Czim\FileHandling\Storage\File\RawStorableFile;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravelStorage implements StorageInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Whether the filesystem (and thus the paths given) are local
     *
     * @var bool
     */
    protected $isLocal;

    /**
     * The URL to prepend to relative file paths.
     *
     * @var null|string
     */
    protected $baseUrl;

    /**
     * @param Filesystem  $filesystem
     * @param bool        $isLocal
     * @param null|string $baseUrl
     */
    public function __construct(
        Filesystem $filesystem,
        $isLocal = true,
        $baseUrl = null
    ) {
        $this->filesystem = $filesystem;
        $this->isLocal    = $isLocal;
        $this->baseUrl    = trim($baseUrl ?: '', '/');
    }

    /**
     * Returns whether a stored file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * Returns a public URL to the stored file.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return $this->prefixBaseUrl($path);
    }

    /**
     * Returns the file from storage.
     *
     * Note that the mimetype is not filled in here. Tackle this manually if it is required.
     *
     * @param string $path
     * @return StoredFileInterface
     */
    public function get(string $path): StoredFileInterface
    {
        $raw = new RawStorableFile();

        $raw->setName(pathinfo($path, PATHINFO_BASENAME));
        $raw->setData($this->filesystem->get($path));

        $stored = new DecoratorStoredFile($raw);
        $stored->setUrl($this->prefixBaseUrl($path));

        return $stored;
    }

    /**
     * Stores a file.
     *
     * @param StorableFileInterface $file mixed content to store
     * @param string                $path where the file should be stored, including the filename
     * @return StoredFileInterface
     * @throws FileStorageException
     */
    public function store(StorableFileInterface $file, string $path): StoredFileInterface
    {
        $stream = $file->openStream();

        if ($stream !== null) {
            $this->writeFileAsStream($file, $path, $stream);
        } else {
            $this->writeFileDirectly($file, $path);
        }

        $stored = new DecoratorStoredFile($file);
        $stored->setUrl($this->prefixBaseUrl($path));

        return $stored;
    }

    protected function writeFileAsStream(StorableFileInterface $file, string $path, $resource): void
    {
        // In order to have the same overwriting behavior as put, any existing file must be deleted first.
        if ($this->filesystem->exists($path)) {
            if (! $this->filesystem->delete($path)) {
                throw new FileStorageException(
                    'Failed to delete existing file in preparation of writing stream, '
                    . "'{$file->name()}' to '{$path}'"
                );
            }
        }

        if (! $this->filesystem->writeStream($path, $resource)) {
            throw new FileStorageException("Failed to store '{$file->name()}' to '{$path}' (stream)");
        }

        $file->closeStream($resource);
    }

    protected function writeFileDirectly(StorableFileInterface $file, string $path): void
    {
        if (! $this->filesystem->put($path, $file->content())) {
            throw new FileStorageException("Failed to store '{$file->name()}' to '{$path}'");
        }
    }

    /**
     * Deletes a stored media file.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return $this->filesystem->delete($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function prefixBaseUrl(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
