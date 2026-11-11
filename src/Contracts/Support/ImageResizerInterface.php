<?php

namespace Czim\FileHandling\Contracts\Support;

use SplFileInfo;

interface ImageResizerInterface
{
    /**
     * Resize an image using given options.
     *
     * @param array<string, mixed> $options
     */
    public function resize(SplFileInfo $file, array $options): bool;
}
