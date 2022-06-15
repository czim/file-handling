<?php

namespace Czim\FileHandling\Support\Container;

use Czim\FileHandling\Exceptions\Container\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Very simple container implementation.
 * If you're using a framework, use its container solution instead.
 */
class SimpleContainer implements ContainerInterface
{
    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    protected $callables = [];

    /**
     * @param string   $id
     * @param callable $callable
     * @return $this|ContainerInterface
     */
    public function registerCallable(string $id, callable $callable): ContainerInterface
    {
        $this->bindings[ $id ]  = $callable;
        $this->callables[ $id ] = true;

        return $this;
    }

    /**
     * @param string $id
     * @param mixed $instance
     * @return $this
     */
    public function registerInstance(string $id, $instance): ContainerInterface
    {
        $this->bindings[ $id ]  = $instance;
        $this->callables[ $id ] = false;

        return $this;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        if (! $this->has($id)) {
            throw new NotFoundException("Failed to instantiate {$id}");
        }

        if (array_key_exists($id, $this->callables) && $this->callables[ $id ]) {

            $callable = $this->bindings[ $id ];
            return $callable($this);
        }

        return $this->bindings[ $id ];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->bindings);
    }
}
