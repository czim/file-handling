<?php

namespace Czim\FileHandling\Support\Image;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use SplFileInfo;

class Optimizer
{
    /**
     * @var $optimizerChain
     */
    protected $optimizerChain;


    /**
     * @param OptimizerChainFactory $optimizer
     */
    public function __construct(OptimizerChainFactory $optimizerChain)
    {
        $this->optimizerChain = $optimizerChain::create();
    }


    /**
     * Optimize an image using given options.
     *
     * @param SplFileInfo $file
     * @param array       $options
     * @return bool
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
     * @param array      $array
     * @param string     $key
     * @param null|mixed $default
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
