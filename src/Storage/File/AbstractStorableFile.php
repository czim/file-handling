<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\UploadedMarkableInterface;

abstract class AbstractStorableFile implements StorableFileInterface, UploadedMarkableInterface
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
     * Initializes the storable file with mixed data.
     *
     * @param mixed $data
     * @return $this
     */
    abstract public function setData($data);

    /**
     * Writes a copy to a given (local) file path;
     *
     * @param string $path
     * @return bool
     */
    abstract public function copy($path);

    /**
     * Sets the mime type for the file.
     *
     * @param string $type
     * @return $this
     */
    public function setMimeType($type)
    {
        $this->mimeType = $type;

        return $this;
    }

    /**
     * Sets the name for the file.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Marks the file as having been uploaded (or not).
     *
     * @param bool $uploaded
     */
    public function setUploaded($uploaded = true)
    {
        $this->uploaded = (bool) $uploaded;
    }

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    /**
     * Returns the (storage) name for the file.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension()
    {
        if (null === $this->name) {
            return null;
        }

        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @return int
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded()
    {
        return $this->uploaded;
    }

    /**
     * Returns (local) path to file, if possible.
     *
     * @return string|null
     */
    public function path()
    {
        return null;
    }

}
