<?php
namespace Czim\FileHandling\Contracts\Storage;

interface StoredFileInterface extends StorableFileInterface
{

    /**
     * Returns a public URL to the stored media file.
     *
     * @return string
     */
    public function url();

}
