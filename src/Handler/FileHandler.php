<?php
namespace Czim\FileHandling\Handler;

use Czim\FileHandling\Contracts\Handler\FileHandlerInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorageInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Contracts\Storage\TargetInterface;
use Czim\FileHandling\Contracts\Variant\VariantProcessorInterface;

class FileHandler implements FileHandlerInterface
{

    /**
     * The name of the original image 'variant'.
     *
     * @var string
     */
    const ORIGINAL = 'original';

    /**
     * The configuration key for the variant definitions.
     *
     * @var string
     */
    const CONFIG_VARIANTS = 'variants';


    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var VariantProcessorInterface
     */
    protected $processor;


    /**
     * @param StorageInterface          $storage
     * @param VariantProcessorInterface $processor
     */
    public function __construct(
        StorageInterface $storage,
        VariantProcessorInterface $processor
    ) {
        $this->storage    = $storage;
        $this->processor  = $processor;
    }


    /**
     * Processes and stores a storable file.
     *
     * @param StorableFileInterface $source
     * @param TargetInterface $target
     * @param array $options
     * @return StoredFileInterface[]    keyed by variant name (or 'original')
     */
    public function process(StorableFileInterface $source, TargetInterface $target, array $options = [])
    {
        $stored = [
            static::ORIGINAL => $this->storage->store($source, $target->original()),
        ];

        if (array_key_exists(static::CONFIG_VARIANTS, $options)) {
            foreach ($options[ static::CONFIG_VARIANTS ] as $variant => $variantOptions) {

                $stored[ $variant ] = $this->processVariant($source, $target, $variant, $variantOptions);
            }
        }

        return $stored;
    }

    /**
     * Processes and stores a single variant for a storable file.
     *
     * @param StorableFileInterface $source
     * @param TargetInterface       $target
     * @param string                $variant
     * @param array                 $options
     * @return StoredFileInterface
     */
    public function processVariant(StorableFileInterface $source, TargetInterface $target, $variant, array $options = [])
    {
        $storableVariant = $this->processor->process($source, $variant, $options);

        return $this->storage->store($storableVariant, $target->variant($variant));
    }

    /**
     * Returns the URLs keyed by the variant keys requested.
     *
     * @param TargetInterface $target
     * @param string[]        $variants     keys for variants to include
     * @return string[]
     */
    public function variantUrlsForTarget(TargetInterface $target, array $variants = [])
    {
        $urls = [
            static::ORIGINAL => $this->storage->url($target->original()),
        ];

        if (in_array(static::ORIGINAL, $variants)) {
            $variants = array_diff($variants, [ static::ORIGINAL ]);
        }

        foreach ($variants as $variant) {
            $urls[ $variant ] = $this->storage->url($target->variant($variant));
        }

        return $urls;
    }

    /**
     * Deletes a file and all indicated variants.
     *
     * @param TargetInterface $target
     * @param string[]        $variants     variant keys
     * @return bool
     */
    public function delete(TargetInterface $target, array $variants = [])
    {
        $success = true;

        if ( ! in_array(static::ORIGINAL, $variants)) {
            $variants[] = static::ORIGINAL;
        }

        foreach ($variants as $variant) {
            if ( ! $this->deleteVariant($target, $variant)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Deletes a single variant.
     *
     * @param TargetInterface $target       may be a full file path, or a base path
     * @param string          $variant      'original' refers to the original file
     * @return bool
     */
    public function deleteVariant(TargetInterface $target, $variant)
    {
        if ($variant == static::ORIGINAL) {
            $path = $target->original();
        } else {
            $path = $target->variant($variant);
        }

        // If the file does not exist, consider 'deletion' a success
        if ( ! $this->storage->exists($path)) {
            return true;
        }

        return $this->storage->delete($path);
    }

}
