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
     * @return ProcessableFileInterface|false
     * @throws VariantStrategyShouldNotBeAppliedException
     */
    public function apply(ProcessableFileInterface $file)
    {
        $this->file = $file;

        if ( ! $this->shouldBeApplied()) {
            throw new VariantStrategyShouldNotBeAppliedException;
        }

        $result = $this->perform();

        return ($result || null === $result) ? $this->file : false;
    }

    /**
     * Sets the options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Returns whether the variant strategy should be applied.
     *
     * The file property should be available at this point.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function shouldBeApplied()
    {
        return true;
    }

    /**
     * Performs manipulation of the file.
     *
     * @return bool|null|void
     */
    abstract protected function perform();

}
