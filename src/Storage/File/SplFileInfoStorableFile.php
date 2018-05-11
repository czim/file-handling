<?php
namespace Czim\FileHandling\Storage\File;

use RuntimeException;
use SplFileInfo;
use UnexpectedValueException;

class SplFileInfoStorableFile extends AbstractStorableFile
{

    /**
     * @var SplFileInfo
     */
    protected $file;


    /**
     * Initializes the storable file with mixed data.
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        if ( ! ($data instanceof SplFileInfo)) {
            throw new UnexpectedValueException('Expected SplFileInfo instance');
        }

        $this->file = $data;

        $this->setDerivedFileProperties();

        return $this;
    }

    /**
     * Sets properties based on the given data.
     */
    protected function setDerivedFileProperties()
    {
        if ( ! $this->file || ! file_exists($this->file->getRealPath())) {
            throw new RuntimeException("Local file not found at {$this->file->getPath()}");
        }

        $this->size = $this->file->getSize();

        if (null === $this->name) {
            $this->name = $this->file->getBasename();
        }
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content()
    {
        return file_get_contents($this->file->getRealPath());
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path)
    {
        return copy($this->path(), $path);
    }

    /**
     * {@inheritdoc}
     */
    public function path()
    {
        return $this->file->getRealPath();
    }

}
