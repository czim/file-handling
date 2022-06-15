<?php

namespace Czim\FileHandling\Test\Unit\Support\Container;

use Czim\FileHandling\Exceptions\Container\NotFoundException;
use Czim\FileHandling\Support\Container\SimpleContainer;
use Czim\FileHandling\Test\TestCase;

class SimpleContainerTest extends TestCase
{

    /**
     * @test
     */
    function it_returns_whether_a_binding_is_set()
    {
        $container = new SimpleContainer();

        $container->registerInstance('test', 'is set');

        static::assertTrue($container->has('test'));
        static::assertFalse($container->has('not-set'));
    }

    /**
     * @test
     */
    function it_returns_registerd_binding()
    {
        $container = new SimpleContainer();

        $container->registerInstance('test', 'is set');

        static::assertEquals('is set', $container->get('test'));
    }

    /**
     * @test
     */
    function it_returns_registered_binding_callable()
    {
        $container = new SimpleContainer();

        $container->registerCallable('test', function () {
            return 'is set';
        });

        static::assertEquals('is set', $container->get('test'));
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_a_binding_could_not_be_found()
    {
        $this->expectException(NotFoundException::class);

        $container = new SimpleContainer();

        $container->get('test');
    }

}
