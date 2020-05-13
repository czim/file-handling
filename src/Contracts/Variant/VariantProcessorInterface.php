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
    public function process(StorableFileInterface $source, string $variant, array $strategies): StorableFileInterface;

    /**
     * Returns list of temporary files created while processing.
     *
     * @return StorableFileInterface[]
     */
    public function getTemporaryFiles(): array;

    /**
     * Purges memory of temporary files.
     *
     * Note that this does not delete the files, just the processor's history of them.
     */
    public function clearTemporaryFiles(): void;
}
