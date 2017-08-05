<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

class StoredFile extends RawStorableFile implements StoredFileInterface
{

    /**
     * @var string
     */
    protected $url;

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

}
