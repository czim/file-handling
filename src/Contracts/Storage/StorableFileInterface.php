<?php

namespace Czim\FileHandling\Contracts\Storage;

use Czim\FileHandling\Exceptions\StorableFileCouldNotBeDeletedException;

interface StorableFileInterface
{
    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Returns the source as a (read-only) resource, if possible.
     *
     * Not all storable files should be expected to support this.
     * Where not available, StorableFileInterface::content() should be used as a fallback.
     *
     * Warning: when calling this method, a stream is opened, but it is not automatically closed!
     * Clients of this method must close the stream when they are done with it, by calling closeStream().
     *
     * @return resource|null
     */
    public function openStream();

    /**
     * Closes a stream previously opened.
     *
     * @param resource|null $resource   stream resource handle previously given by openStream().
     */
    public function closeStream($resource): void;

    /**
     * Writes a copy to a given (local) file path;
     *
     * @param string $path
     * @return bool
     */
    public function copy(string $path): bool;

    /**
     * Deletes the storable file (if possible and allowed).
     *
     * @throws StorableFileCouldNotBeDeletedException
     */
    public function delete(): void;

    /**
     * Returns (local) path to file, if possible.
     *
     * @return string|null
     */
    public function path(): ?string;

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType(): ?string;

    /**
     * Returns the (storage) name for the file.
     *
     * @return string|null
     */
    public function name(): ?string;

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension(): ?string;

    /**
     * Returns the size of the file in bytes.
     *
     * @return int|null
     */
    public function size(): ?int;

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded(): bool;
}
