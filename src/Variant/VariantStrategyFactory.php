<?php
namespace Czim\FileHandling\Variant;

use Czim\FileHandling\Contracts\Variant\VariantStrategyFactoryInterface;
use Czim\FileHandling\Contracts\Variant\VariantStrategyInterface;
use RuntimeException;

/**
 * Class VariantStrategyFactory
 *
 * Creates instances of variant manipulation strategies.
 */
class VariantStrategyFactory implements VariantStrategyFactoryInterface
{
    const CONFIG_ALIASES = 'aliases';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Sets the configuration for the factory.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Returns strategy instance.
     *
     * @param string $strategy  strategy class or alias
     * @param array  $options   options for the strategy
     * @return VariantStrategyInterface
     */
    public function make($strategy, array $options = [])
    {
        $instance = $this->instantiateClass(
            $this->resolveStrategyClassName($strategy)
        );

        if ( ! ($instance instanceof VariantStrategyInterface)) {
            throw new RuntimeException("Variant strategy created for '{$strategy}' is of incorrect type");
        }

        return $instance->setOptions($options);
    }

    /**
     * @param string $strategy
     * @return string
     */
    protected function resolveStrategyClassName($strategy)
    {
        if (    array_key_exists(static::CONFIG_ALIASES, $this->config)
            &&  array_key_exists($strategy, $this->config[ static::CONFIG_ALIASES ])
        ) {
            $strategyClass = $this->config[ static::CONFIG_ALIASES ][ $strategy ];
        } else {
            $strategyClass = $strategy;
        }

        if ( ! class_exists($strategyClass)) {
            throw new RuntimeException("Resolved variant strategy '{$strategy}' does not exist");
        }

        return $strategyClass;
    }

    /**
     * @param string $class
     * @return object
     */
    protected function instantiateClass($class)
    {
        return new $class;
    }

}
