<?php
namespace Czim\FileHandling\Variant;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileFactoryInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Variant\VariantProcessorInterface;
use Czim\FileHandling\Contracts\Variant\VariantStrategyFactoryInterface;
use Czim\FileHandling\Exceptions\CouldNotProcessDataException;
use Czim\FileHandling\Exceptions\VariantStrategyNotAppliedException;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;
use Czim\FileHandling\Storage\File\ProcessableFile;
use Exception;

/**
 * Class VariantProcessor
 *
 * Handles the creation of variants for a file upload.
 */
class VariantProcessor implements VariantProcessorInterface
{
    const CONFIG_VARIANT_FACTORY = 'factory';
    const CONFIG_FORCE_APPLY     = 'force-apply';

    /**
     * @var StorableFileFactoryInterface
     */
    protected $fileFactory;

    /**
     * @var VariantStrategyFactoryInterface
     */
    protected $strategyFactory;

    /**
     * @var array
     */
    protected $config = [];


    /**
     * @param StorableFileFactoryInterface    $fileFactory
     * @param VariantStrategyFactoryInterface $strategyFactory
     */
    public function __construct(
        StorableFileFactoryInterface $fileFactory,
        VariantStrategyFactoryInterface $strategyFactory
    ) {
        $this->fileFactory     = $fileFactory;
        $this->strategyFactory = $strategyFactory;
    }


    /**
     * Sets configuration for the processor.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        if (array_key_exists(static::CONFIG_VARIANT_FACTORY, $config)) {
            $this->strategyFactory->setConfig(
                $config[ static::CONFIG_VARIANT_FACTORY ]
            );
        }

        return $this;
    }

    /**
     * Returns a processed variant for a given source file.
     *
     * @param StorableFileInterface $source
     * @param string                $variant    the name/prefix name of the variant
     * @param array[]               $strategies associative, ordered set of strategies to apply
     * @return StorableFileInterface
     * @throws VariantStrategyNotAppliedException
     * @throws CouldNotProcessDataException
     */
    public function process(StorableFileInterface $source, $variant, array $strategies)
    {
        $file = $this->makeTemporaryCopy($source);

        foreach ($strategies as $strategy => $options) {

            $instance = $this->strategyFactory->make($strategy)->setOptions($options);

            // The file returned by the strategy step may have altered the path,
            // name, extension and/or mime type of the file being processed.
            // This information is present in the returned ProcessableFile instance.

            try {
                $newFile = $instance->apply($file);

            } catch (VariantStrategyShouldNotBeAppliedException $e) {

                if ($this->shouldThrowExceptionForUnappliedStrategy()) {
                    throw new VariantStrategyNotAppliedException(
                        "Strategy '{$strategy}' not applied to '{$source->path()}'"
                    );
                }

                continue;
            }

            if (false === $newFile) {
                throw new VariantStrategyNotAppliedException(
                    "Failed to apply '{$strategy}' to '{$source->path()}'"
                );
            }

            $file = $newFile;
        }

        return $file;
    }

    /**
     * Returns whether exceptions should be thrown if a strategy was not applied.
     *
     * @return bool
     */
    protected function shouldThrowExceptionForUnappliedStrategy()
    {
        if ( ! array_key_exists(static::CONFIG_FORCE_APPLY, $this->config)) {
            return false;
        }

        return (bool) $this->config[ static::CONFIG_FORCE_APPLY ];
    }

    /**
     * Makes a copy of the original file info that will be manipulated into the variant.
     *
     * @param StorableFileInterface $source
     * @return ProcessableFileInterface
     * @throws CouldNotProcessDataException
     */
    protected function makeTemporaryCopy(StorableFileInterface $source)
    {
        $path = $this->makeLocalTemporaryPath($source->extension());

        try {
            $success = $source->copy($path);

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new CouldNotProcessDataException("Failed to make variant copy to '{$path}'", $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }

        // @codeCoverageIgnoreStart
        if ( ! $success) {
            throw new CouldNotProcessDataException("Failed to make variant copy to '{$path}'");
        }
        // @codeCoverageIgnoreEnd

        $file = new ProcessableFile;

        $file->setName($source->name());
        $file->setMimeType($source->mimeType());
        $file->setData($path);

        return $file;
    }

    /**
     * @param string $extension
     * @return string
     */
    protected function makeLocalTemporaryPath($extension = null)
    {
        return sys_get_temp_dir() . '/' . uniqid('filehandling-variant-')
             . ($extension ? ".{$extension}" : null);
    }

}
