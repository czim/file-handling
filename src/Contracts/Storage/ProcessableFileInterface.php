<?php

namespace Czim\FileHandling\Contracts\Storage;

interface ProcessableFileInterface extends StorableFileInterface, DataSettableInterface
{
    /**
     * Sets a new mime type for the processed file.
     *
     * @param string $mimeType
     */
    public function setMimeType(string $mimeType): void;

    /**
     * Sets a new name for the processed file.
     *
     * @param string $name
     */
    public function setName(string $name): void;
}
