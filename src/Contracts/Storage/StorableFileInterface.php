<?php
namespace Czim\FileHandling\Contracts\Storage;

interface StorableFileInterface
{

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content();

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType();

    /**
     * Returns the (storage) name for the file.
     *
     * @return string
     */
    public function name();

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension();

    /**
     * Returns the size of the file in bytes.
     *
     * @return int
     */
    public function size();

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded();

}
