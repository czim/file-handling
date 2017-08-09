<?php
namespace Czim\FileHandling\Contracts\Storage;

interface ProcessableFileInterface extends StorableFileInterface
{

    /**
     * @param \SplFileInfo|string $data     file or path
     * @return $this
     */
    public function setData($data);

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
