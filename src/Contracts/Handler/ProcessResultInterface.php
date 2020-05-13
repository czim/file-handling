<?php

namespace Czim\FileHandling\Contracts\Handler;

use Czim\FileHandling\Contracts\Storage\StoredFileInterface;

interface ProcessResultInterface
{
    /**
     * Returns the files stored as a result of processing.
     *
     * @return StoredFileInterface[]    keyed by variant name (or 'original')
     */
    public function storedFiles(): array;

    /**
     * Returns a list of temporary files created while processing.
     *
     * @return StoredFileInterface[]
     */
    public function temporaryFiles(): array;
}
