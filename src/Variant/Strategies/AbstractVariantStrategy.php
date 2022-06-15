<?php

namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Contracts\Variant\VariantStrategyInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;

abstract class AbstractVariantStrategy implements VariantStrategyInterface
{
    /**
     * The file to be manipulated.
     *
     * @var ProcessableFileInterface
     */
    protected $file;

    /**
     * The options given for this
     *
     * @var array
     */
    protected $options = [];

    /**
     * Applies strategy to a file.
     *
     * @param ProcessableFileInterface $file
     * @return ProcessableFileInterface|null
     * @throws VariantStrategyShouldNotBeAppliedException
     */
    public function apply(ProcessableFileInterface $file): ?ProcessableFileInterface
    {
        $this->file = $file;

        if (! $this->shouldBeApplied()) {
            throw new VariantStrategyShouldNotBeAppliedException();
        }

        $result = $this->perform();

        return ($result || null === $result) ? $this->file : null;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Returns whether the variant strategy should be applied.
     *
     * The file property should be available at this point.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function shouldBeApplied(): bool
    {
        return true;
    }

    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    abstract protected function perform(): ?bool;
}
