<?php
namespace Czim\FileHandling\Contracts\Variant;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;

interface VariantStrategyInterface
{

    /**
     * Applies strategy to a file.
     *
     * @param ProcessableFileInterface $file
     * @return ProcessableFileInterface|false
     * @throws VariantStrategyShouldNotBeAppliedException
     */
    public function apply(ProcessableFileInterface $file);

    /**
     * Sets the options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);

}
