<?php

namespace Czim\FileHandling\Test\Unit\Support\Content;

use Czim\FileHandling\Test\Helpers\Strategies\SpyVariantStrategy;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\VariantStrategyFactory;
use Mockery;
use Psr\Container\ContainerInterface;
use RuntimeException;

class VariantStrategyFactoryTest extends TestCase
{

    /**
     * Bindings for mock container.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * @test
     */
    function it_sets_a_config()
    {
        $factory = new VariantStrategyFactory($this->getMockContainer());

        static::assertSame($factory, $factory->setConfig(['test' => 'value']));
    }

    /**
     * @test
     */
    function it_makes_a_strategy_by_classname()
    {
        $this->bindings[ SpyVariantStrategy::class ] = new SpyVariantStrategy();

        $factory = new VariantStrategyFactory($this->getMockContainer());

        $strategy = $factory->make(SpyVariantStrategy::class);

        /** @var SpyVariantStrategy $strategy */
        static::assertInstanceOf(SpyVariantStrategy::class, $strategy);
        static::assertTrue($strategy->optionsSet, 'Options were not set on strategy instance');
    }

    /**
     * @test
     */
    function it_makes_a_strategy_by_alias()
    {
        $this->bindings[ SpyVariantStrategy::class ] = new SpyVariantStrategy();

        $factory = new VariantStrategyFactory($this->getMockContainer());

        $factory->setConfig([
            VariantStrategyFactory::CONFIG_ALIASES => [
                'test-alias' => SpyVariantStrategy::class,
            ],
        ]);

        $strategy = $factory->make('test-alias');

        static::assertInstanceOf(SpyVariantStrategy::class, $strategy);
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_the_container_cannot_resolve_a_binding()
    {
        $this->expectException(RuntimeException::class);

        $factory = new VariantStrategyFactory($this->getMockContainer());

        $factory->make(VariantStrategyFactoryTest::class);
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_the_instantiated_class_is_not_a_variant_strategy()
    {
        $this->expectException(RuntimeException::class);

        $this->bindings[ VariantStrategyFactoryTest::class ] = new VariantStrategyFactoryTest();

        $factory = new VariantStrategyFactory($this->getMockContainer());

        $factory->make(VariantStrategyFactoryTest::class);
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_the_strategy_class_does_not_exist()
    {
        $this->expectException(RuntimeException::class);

        $factory = new VariantStrategyFactory($this->getMockContainer());

        $factory->make(DoesNotExist::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|ContainerInterface
     */
    protected function getMockContainer()
    {
        /** @var Mockery\Mock|Mockery\MockInterface|ContainerInterface $container */
        $container = Mockery::mock(ContainerInterface::class);

        $container->shouldReceive('has')->andReturnUsing(function ($id) {
            return array_key_exists($id, $this->bindings);
        });

        $container->shouldReceive('get')->andReturnUsing(function ($id) {
            return $this->bindings[ $id ];
        });

        return $container;
    }

}
