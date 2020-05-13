<?php

namespace Czim\FileHandling\Contracts\Storage;

interface StoredFileInterface extends StorableFileInterface
{
    /**
     * Sets the full public URL to the file.
     *
     * @param string $url
     */
    public function setUrl(string $url): void;

    /**
     * Returns a public URL to the stored media file.
     *
     * @return string
     */
    public function url(): string;
}
