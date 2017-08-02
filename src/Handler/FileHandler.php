<?php
namespace Czim\FileHandling\Handler;

use Czim\FileHandling\Contracts\Storage\PathHelperInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorageInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Contracts\Variant\VariantProcessorInterface;
use UnexpectedValueException;

class FileHandler
{
    const ORIGINAL = 'original';
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
     * @var PathHelperInterface
     */
    protected $pathHelper;


    /**
     * @param StorageInterface          $storage
     * @param VariantProcessorInterface $processor
     * @param PathHelperInterface       $pathHelper
     */
    public function __construct(
        StorageInterface $storage,
        VariantProcessorInterface $processor,
        PathHelperInterface $pathHelper
    ) {
        $this->storage    = $storage;
        $this->processor  = $processor;
        $this->pathHelper = $pathHelper;
    }


    /**
     * Processes and stores a storable file.
     *
     * @param StorableFileInterface $source
     * @param string $targetPath
     * @param array $options
     * @return StoredFileInterface
     */
    public function process(StorableFileInterface $source, $targetPath, array $options = [])
    {
        $originalPath = $this->pathHelper->addVariantToBasePath($targetPath);

        $originalStored = $this->storage->store($source, $originalPath);

        if (array_key_exists(static::CONFIG_VARIANTS, $options)) {
            foreach ($options[ static::CONFIG_VARIANTS ] as $variant => $variantOptions) {

                $this->processVariant($source, $targetPath, $variant, $variantOptions);
            }
        }

        return $originalStored;
    }

    /**
     * Processes and stores a single variant for a storable file.
     *
     * @param StorableFileInterface $source
     * @param string                $targetPath
     * @param string                $variant
     * @param array                 $options
     * @return StoredFileInterface
     */
    public function processVariant(StorableFileInterface $source, $targetPath, $variant, array $options = [])
    {
        $variantPath = $this->pathHelper->addVariantToBasePath($targetPath, $variant);

        $storableVariant = $this->processor->process($source, $variant, $options);

        return $this->storage->store($storableVariant, $variantPath);
    }

    /**
     * @param StoredFileInterface $file
     * @param string[]            $variants
     * @return string[]
     */
    public function variantUrlForStoredFile(StoredFileInterface $file, array $variants = [])
    {
        $basePath = $this->pathHelper->basePath($file->path());

        return $this->variantUrlsForBasePath($basePath, $file->name(), $variants);
    }

    /**
     * Returns the URLs keyed by the variant keys requested.
     *
     * @param string   $path        base path without variant and filename
     * @param string   $file        file name
     * @param string[] $variants    keys for variants to include
     * @return string[]
     */
    public function variantUrlsForBasePath($path, $file, array $variants = [])
    {
        $urls = [];

        if ( ! in_array(static::ORIGINAL, $variants)) {
            array_unshift($variants, static::ORIGINAL);
        }

        foreach ($variants as $variant) {

            $urls[ $variant ] = $this->storage->url(
                $this->pathHelper->addVariantToBasePath($path, $variant) . '/' . $file
            );
        }

        return $urls;
    }

    /**
     * Deletes a file and all indicated variants.
     *
     * @param string   $basePath
     * @param string   $file
     * @param string[] $variants    variant keys
     * @return bool
     */
    public function delete($basePath, $file, array $variants = [])
    {
        $success = true;

        if ( ! in_array(static::ORIGINAL, $variants)) {
            $variants[] = static::ORIGINAL;
        }

        foreach ($variants as $variant) {
            if ( ! $this->deleteVariant($basePath, $variant, $file)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Deletes a single variant.
     *
     * @param string      $path         may be a full file path, or a base path
     * @param null|string $variant      must be given if file path is not full
     * @param null|string $file         must be given if file path is not full
     * @return bool
     */
    public function deleteVariant($path, $variant = null, $file = null)
    {
        if (null !== $variant && null !== $file) {
            // If variant and file are given, the path is expected to be a basepath
            $variantPath = $this->pathHelper->addVariantToBasePath($path, $variant) . '/' . $file;

        } elseif (null === $variant && null === $file) {
            // If neither file nor variant are given, the path is expected to be a full path to the variant
            $variantPath = $path;

        } else {
            throw new UnexpectedValueException("Expected either no variant or file parameter, or both");
        }

        // If the file does not exist, consider 'deletion' a success
        if ( ! $this->storage->exists($variantPath)) {
            return true;
        }

        return $this->storage->delete($variantPath);
    }

}
