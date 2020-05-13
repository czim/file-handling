<?php

namespace Czim\FileHandling\Contracts\Storage;

interface UploadedMarkableInterface
{
    /**
     * Marks the file as having been uploaded (or not).
     *
     * @param bool $uploaded
     */
    public function setUploaded(bool $uploaded = true): void;
}
