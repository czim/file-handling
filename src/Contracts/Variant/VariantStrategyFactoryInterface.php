<?php
namespace Czim\FileHandling\Contracts\Variant;

interface VariantStrategyFactoryInterface
{

    /**
     * Returns strategy instance.
     *
     * @param string $strategy  strategy class or alias
     * @param array  $options   options for the strategy
     * @return VariantStrategyInterface
     */
    public function make($strategy, array $options = []);

    /**
     * Sets the configuration for the factory.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config);

}
