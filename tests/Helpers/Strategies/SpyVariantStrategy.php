<?php
namespace Czim\FileHandling\Test\Helpers\Strategies;

use Czim\FileHandling\Contracts\Variant\VariantStrategyInterface;
use SplFileInfo;

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
     * Returns whether this strategy can be applied to a file with a given mimeType.
     *
     * @param string $mimeType
     * @return bool
     */
    public function shouldApplyForMimeType($mimeType)
    {
        return $this->shouldApply;
    }

    /**
     * Applies strategy to a file.
     *
     * @param SplFileInfo $file
     * @return bool
     */
    public function apply(SplFileInfo $file)
    {
        $this->applied = true;

        return $this->applySuccessfully;
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
