<?php

namespace Czim\FileHandling\Support\Image;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use SplFileInfo;

class Optimizer
{
    /**
     * @var OptimizerChain
     */
    protected $optimizerChain;


    public function __construct(OptimizerChainFactory $optimizerChain)
    {
        $this->optimizerChain = $optimizerChain::create();
    }


    /**
     * Optimize an image using given options.
     *
     * @param array<string, mixed> $options
     */
    public function optimize(SplFileInfo $file, array $options): bool
    {
        $options  = $this->arrayGet($options, 'convertOptions', []);
        $filePath = $file->getRealPath();

        $this->optimizerChain
            ->setTimeout(10)
            ->optimize($filePath);

        return true;
    }

    /**
     * Safely get array value from config array.
     *
     * @param array<string, mixed> $array
     * @param string               $key
     * @param null|mixed          $default
     * @return mixed|null
     */
    protected function arrayGet(array $array, $key, $default = null)
    {
        // @codeCoverageIgnoreStart
        if (! array_key_exists($key, $array)) {
            return $default;
        }
        // @codeCoverageIgnoreEnd

        return $array[ $key ];
    }
}
