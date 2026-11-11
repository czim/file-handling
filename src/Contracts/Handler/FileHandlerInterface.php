<?php

namespace Czim\FileHandling\Contracts\Handler;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\TargetInterface;

interface FileHandlerInterface
{
    /**
     * Processes and stores a storable file.
     *
     * @param array<string, mixed>  $options
     */
    public function process(StorableFileInterface $source, TargetInterface $target, array $options = []): ProcessResultInterface;

    /**
     * Processes and stores a single variant for a storable file.
     *
     * @param array<string, mixed> $options
     */
    public function processVariant(StorableFileInterface $source, TargetInterface $target, string $variant, array $options = []): ProcessResultInterface;

    /**
     * Returns the URLs keyed by the variant keys requested.
     *
     * @param string[] $variants     keys for variants to include
     * @return string[]
     */
    public function variantUrlsForTarget(TargetInterface $target, array $variants = []): array;

    /**
     * Deletes a file and all indicated variants.
     *
     * @param string[] $variants     variant keys
     */
    public function delete(TargetInterface $target, array $variants = []): bool;

    /**
     * Deletes a single variant.
     *
     * @param TargetInterface $target       may be a full file path, or a base path
     * @param string          $variant      'original' refers to the original file
     */
    public function deleteVariant(TargetInterface $target, string $variant): bool;
}
