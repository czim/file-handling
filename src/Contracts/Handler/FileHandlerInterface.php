<?php

namespace Czim\FileHandling\Contracts\Handler;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\TargetInterface;

interface FileHandlerInterface
{
    /**
     * Processes and stores a storable file.
     *
     * @param StorableFileInterface $source
     * @param TargetInterface       $target
     * @param array                 $options
     * @return ProcessResultInterface
     */
    public function process(StorableFileInterface $source, TargetInterface $target, array $options = []): ProcessResultInterface;

    /**
     * Processes and stores a single variant for a storable file.
     *
     * @param StorableFileInterface $source
     * @param TargetInterface       $target
     * @param string                $variant
     * @param array                 $options
     * @return ProcessResultInterface
     */
    public function processVariant(StorableFileInterface $source, TargetInterface $target, $variant, array $options = []): ProcessResultInterface;

    /**
     * Returns the URLs keyed by the variant keys requested.
     *
     * @param TargetInterface $target
     * @param string[]        $variants     keys for variants to include
     * @return string[]
     */
    public function variantUrlsForTarget(TargetInterface $target, array $variants = []): array;

    /**
     * Deletes a file and all indicated variants.
     *
     * @param TargetInterface $target
     * @param string[]        $variants     variant keys
     * @return bool
     */
    public function delete(TargetInterface $target, array $variants = []): bool;

    /**
     * Deletes a single variant.
     *
     * @param TargetInterface $target       may be a full file path, or a base path
     * @param string          $variant      'original' refers to the original file
     * @return bool
     */
    public function deleteVariant(TargetInterface $target, string $variant): bool;
}
