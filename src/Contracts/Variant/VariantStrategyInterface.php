<?php
namespace Czim\FileHandling\Contracts\Variant;

use SplFileInfo;

interface VariantStrategyInterface
{

    /**
     * Returns whether this strategy can be applied to a file with a given mimeType.
     *
     * @param string $mimeType
     * @return bool
     */
    public function shouldApplyForMimeType($mimeType);

    /**
     * Applies strategy to a file.
     *
     * @param SplFileInfo $file
     * @return bool
     */
    public function apply(SplFileInfo $file);

    /**
     * Sets the options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);

}
