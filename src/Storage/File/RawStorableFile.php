<?php
namespace Czim\FileHandling\Storage\File;

use UnexpectedValueException;

class RawStorableFile extends AbstractStorableFile
{

    /**
     * @var string
     */
    protected $content;


    /**
     * Initializes the storable file with mixed data.
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        if ( ! is_string($data)) {
            throw new UnexpectedValueException('Expected string with file content');
        }

        $this->content = $data;

        $this->size = strlen($data);

        return $this;
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path)
    {
        return (bool) file_put_contents($path, $this->content());
    }

}
