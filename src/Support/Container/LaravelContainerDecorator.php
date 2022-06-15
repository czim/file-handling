<?php

namespace Czim\FileHandling\Support\Container;

use Illuminate\Contracts\Container\Container;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Copy of Prooph container wrapper.
 *
 * Modified to work with PHP 5.6.
 *
 * @see       http://getprooph.org/
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016-2017 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 *
 * @codeCoverageIgnore
 */
class LaravelContainerDecorator implements ContainerInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $cacheForHas = [];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->container->make($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id): bool
    {
        if ($this->hasIsCached($id)) {
            return $this->hasFromCache($id);
        }

        $has = $this->container->bound($id) || $this->isInstantiable($id);
        $this->cacheHas($id, $has);

        return $has;
    }

    private function hasIsCached($id)
    {
        return array_key_exists($id, $this->cacheForHas);
    }

    private function hasFromCache($id)
    {
        return $this->cacheForHas[ $id ];
    }

    private function cacheHas($id, $has)
    {
        $this->cacheForHas[ $id ] = (bool) $has;
    }

    private function isInstantiable($id)
    {
        if (class_exists($id)) {
            return true;
        }

        try {
            $reflectionClass = new ReflectionClass($id);
            return $reflectionClass->isInstantiable();
        } catch (ReflectionException $e) {
            return false;
        }
    }
}
