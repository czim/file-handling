<?php
namespace Czim\FileHandling\Test\Helpers\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Contracts\Variant\VariantStrategyInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;

class SpyVariantStrategy implements VariantStrategyInterface
{

    /**
     * @var bool
     */
    public $shouldApply = true;

    /**
     * @var bool
     */
    public $applied = false;

    /**
     * @var bool
     */
    public $applySuccessfully = true;

    /**
     * @var bool
     */
    public $optionsSet = false;


    /**
     * Applies strategy to a file.
     *
     * @param ProcessableFileInterface $file
     * @return ProcessableFileInterface|false
     * @throws VariantStrategyShouldNotBeAppliedException
     */
    public function apply(ProcessableFileInterface $file)
    {
        if ( ! $this->shouldApply) {
            throw new VariantStrategyShouldNotBeAppliedException;
        }

        $this->applied = true;

        if ( ! $this->applySuccessfully) {
            return false;
        }

        return $file;
    }

    /**
     * Sets the options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->optionsSet = true;

        return $this;
    }

}
