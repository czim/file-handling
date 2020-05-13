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
     * @return ProcessableFileInterface|null
     * @throws VariantStrategyShouldNotBeAppliedException
     */
    public function apply(ProcessableFileInterface $file): ?ProcessableFileInterface;

    /**
     * Sets the options
     *
     * @param array $options
     */
    public function setOptions(array $options): void;
}
