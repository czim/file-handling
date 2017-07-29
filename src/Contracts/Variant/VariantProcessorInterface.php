<?php
namespace Czim\FileHandling\Contracts\Variant;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;

interface VariantProcessorInterface
{

    /**
     * Returns a processed variant for a given source file.
     *
     * @param StorableFileInterface $source
     * @param string                $variant        the name/prefix name of the variant
     * @param array[]               $strategies     associative, ordered set of strategies to apply
     * @return StorableFileInterface
     */
    public function process(StorableFileInterface $source, $variant, array $strategies);

}
