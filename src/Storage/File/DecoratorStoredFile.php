<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

/**
 * Class DecoratorStoredFile
 *
 * Decoration layer
 */
class DecoratorStoredFile implements StoredFileInterface
{

    /**
     * @var StorableFileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param StorableFileInterface $file
     */
    public function __construct(StorableFileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Sets the full public URL to the file.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Returns a public URL to the stored media file.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content()
    {
        return $this->file->content();
    }

    /**
     * Writes a copy to a (local) path.
     *
     * @param string $path
     * @return bool
     */
    public function copy($path)
    {
        return $this->file->copy($path);
    }

    /**
     * Returns (local) path to file, if possible.
     *
     * @return string|null
     */
    public function path()
    {
        return $this->file->path();
    }

    /**
     * Returns the content type of the file.
     *
     * @return string|null
     */
    public function mimeType()
    {
        return $this->file->mimeType();
    }

    /**
     * Returns the (storage) name for the file.
     *
     * @return string
     */
    public function name()
    {
        return $this->file->name();
    }

    /**
     * Returns the extension for the file.
     *
     * @return string|null
     */
    public function extension()
    {
        return $this->file->extension();
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @return int
     */
    public function size()
    {
        return $this->file->size();
    }

    /**
     * Returns whether the file was marked as uploaded.
     *
     * @return bool
     */
    public function isUploaded()
    {
        return $this->file->isUploaded();
    }

}
