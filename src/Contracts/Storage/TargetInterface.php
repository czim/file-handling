<?php

namespace Czim\FileHandling\Contracts\Storage;

interface TargetInterface
{
    /**
     * Returns the (relative) target path for the original file.
     *
     * @return string
     */
    public function original(): string;

    /**
     * Returns the (relative) target path for a variant by name.
     *
     * @param string $variant
     * @return string
     */
    public function variant(string $variant): string;
}
