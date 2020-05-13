<?php

namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\Optimizer;
use SplFileInfo;

class ImageOptimizationStrategy extends AbstractImageStrategy
{
    /**
     * @var imageOptimizer
     */
    protected $imageOptimizer;


    /**
     * @param Optimizer $optimizer
     */
    public function __construct(Optimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }


    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform(): ?bool
    {
        $spl = new SplFileInfo($this->file->path());

        return (bool) $this->optimizer->optimize($spl, $this->options);
    }
}
