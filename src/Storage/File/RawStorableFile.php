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
     */
    public function setData($data): void
    {
        if (! is_string($data)) {
            throw new UnexpectedValueException('Expected string with file content');
        }

        $this->content = $data;
        $this->size    = strlen($data);
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $path): bool
    {
        return (bool) file_put_contents($path, $this->content());
    }
}
