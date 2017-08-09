<?php
namespace Czim\FileHandling\Contracts\Storage;

interface ProcessableFileInterface extends StorableFileInterface
{

    /**
     * Sets a new mime type for the processed file.
     *
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType($mimeType);

    /**
     * Sets a new name for the processed file.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

}
