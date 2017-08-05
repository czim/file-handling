<?php
namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Contracts\Variant\VariantStrategyInterface;
use SplFileInfo;

abstract class AbstractVariantStrategy implements VariantStrategyInterface
{

    /**
     * The file to be manipulated.
     *
     * @var SplFileInfo
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
     * @param SplFileInfo $file
     * @return bool
     */
    public function apply(SplFileInfo $file)
    {
        $this->file = $file;

        $result = $this->perform();

        return $result || null === $result;
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
     * Performs manipulation of the file.
     *
     * @return bool|null|void
     */
    abstract protected function perform();

}
