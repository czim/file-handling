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
