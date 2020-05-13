<?php

namespace Czim\FileHandling\Contracts\Storage;

interface DataSettableInterface
{
    /**
     * @param mixed $data   raw content, file path, URL, etc.
     */
    public function setData($data): void;
}
