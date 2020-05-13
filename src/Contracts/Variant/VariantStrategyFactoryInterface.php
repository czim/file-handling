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
    public function make(string $strategy, array $options = []): VariantStrategyInterface;

    /**
     * Sets the configuration for the factory.
     *
     * @param array $config
     * @return $this|VariantStrategyFactoryInterface
     */
    public function setConfig(array $config): VariantStrategyFactoryInterface;
}
