<?php

namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\Resizer;
use SplFileInfo;

class ImageResizeStrategy extends AbstractImageStrategy
{
    /**
     * @var Resizer
     */
    protected $resizer;


    /**
     * @param Resizer $resizer
     */
    public function __construct(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }


    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform(): ?bool
    {
        $spl = new SplFileInfo($this->file->path());

        return (bool) $this->resizer->resize($spl, $this->options);
    }
}
