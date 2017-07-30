<?php
namespace Czim\FileHandling\Contracts\Support;

use SplFileInfo;

interface ImageResizerInterface
{

    /**
     * Resize an image using given options.
     *
     * @param SplFileInfo $file
     * @param array       $options
     * @return string   resized file path
     */
    public function resize(SplFileInfo $file, array $options);

}
