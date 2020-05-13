<?php

namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\DataSettableInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\UploadedMarkableInterface;
use Czim\FileHandling\Exceptions\StorableFileCouldNotBeDeletedException;

abstract class AbstractStorableFile implements StorableFileInterface, DataSettableInterface, UploadedMarkableInterface
{
    /**
     * @var string|null
     */
    protected $mimeType;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var int|null
     */
    protected $size;

    /**
     * @var bool
     */
    protected $uploaded = false;


    /**
     * {@inheritDoc}
     */
    public function openStream()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function closeStream($resource): void
    {
    }

    /**
     * Deletes the storable file (if possible and allowed).
     *
     * @throws StorableFileCouldNotBeDeletedException
     */
    public function delete(): void
    {
        throw new StorableFileCouldNotBeDeletedException(
            "File of type '" . get_class($this) . "' may not be deleted"
        );
    }

    /**
     * Sets the mime type for the file.
     *
     * @param string $type
     */
    public function setMimeType(string $type): void
    {
        $this->mimeType = $type;
    }

    /**
     * Sets the name for the file.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Marks the file as having been uploaded (or not).
     *
     * @param bool $uploaded
     */
    public function setUploaded(bool $uploaded = true): void
    {
        $this->uploaded = (bool) $uploaded;
    }

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Returns the (storage) name for the file.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension(): ?string
    {
        if (null === $this->name) {
            return null;
        }

        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @return int|null
     */
    public function size(): ?int
    {
        return $this->size;
    }

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    /**
     * Returns (local) path to file, if possible.
     *
     * @return string|null
     */
    public function path(): ?string
    {
        return null;
    }
}
