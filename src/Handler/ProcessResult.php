<?php

namespace Czim\FileHandling\Handler;

use Czim\FileHandling\Contracts\Handler\ProcessResultInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

class ProcessResult implements ProcessResultInterface
{
    /**
     * @var StoredFileInterface[]    keyed by variant name (or 'original')
     */
    protected $storedFiles;

    /**
     * @var StoredFileInterface[]
     */
    protected $temporaryFiles;


    public function __construct($stored, $temporary = [])
    {
        $this->storedFiles    = $stored;
        $this->temporaryFiles = $temporary;
    }


    /**
     * Returns the files stored as a result of processing.
     *
     * @return StoredFileInterface[]    keyed by variant name (or 'original')
     */
    public function storedFiles(): array
    {
        return $this->storedFiles;
    }

    /**
     * Returns a list of temporary files created while processing.
     *
     * @return StoredFileInterface[]
     */
    public function temporaryFiles(): array
    {
        return $this->temporaryFiles;
    }
}
