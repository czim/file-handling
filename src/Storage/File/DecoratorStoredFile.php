<?php

namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

/**
 * Class DecoratorStoredFile
 *
 * Decoration layer
 */
class DecoratorStoredFile implements StoredFileInterface
{
    /**
     * @var StorableFileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @param StorableFileInterface $file
     */
    public function __construct(StorableFileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Sets the full public URL to the file.
     *
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Returns a public URL to the stored media file.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content(): string
    {
        return $this->file->content();
    }

    /**
     * @return resource|null
     */
    public function openStream()
    {
        return $this->file->openStream();
    }

    /**
     * {@inheritDoc}
     */
    public function closeStream($resource): void
    {
        $this->file->closeStream($resource);
    }

    /**
     * Writes a copy to a (local) path.
     *
     * @param string $path
     * @return bool
     */
    public function copy(string $path): bool
    {
        return $this->file->copy($path);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->file->delete();
    }

    /**
     * Returns (local) path to file, if possible.
     *
     * @return string|null
     */
    public function path(): ?string
    {
        return $this->file->path();
    }

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType(): ?string
    {
        return $this->file->mimeType();
    }

    /**
     * Returns the (storage) name for the file.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->file->name();
    }

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension(): ?string
    {
        return $this->file->extension();
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @return int|null
     */
    public function size(): ?int
    {
        return $this->file->size();
    }

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->file->isUploaded();
    }
}
