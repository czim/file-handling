<?php
namespace Czim\FileHandling\Test\Unit\Support\Content;

use Czim\FileHandling\Test\Helpers\Strategies\SpyVariantStrategy;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\VariantStrategyFactory;

class VariantStrategyFactoryTest extends TestCase
{

    /**
     * @test
     */
    function it_sets_a_config()
    {
        $factory = new VariantStrategyFactory;

        static::assertSame($factory, $factory->setConfig(['test' => 'value']));
    }

    /**
     * @test
     */
    function it_makes_a_strategy_by_classname()
    {
        $factory = new VariantStrategyFactory;

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
        $factory = new VariantStrategyFactory;

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
     * @expectedException \RuntimeException
     */
    function it_throws_an_exception_if_the_instantiated_class_is_not_a_variant_strategy()
    {
        $factory = new VariantStrategyFactory;

        $factory->make(VariantStrategyFactoryTest::class);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function it_throws_an_exception_if_the_strategy_class_does_not_exist()
    {
        $factory = new VariantStrategyFactory;

        $factory->make(DoesNotExist::class);
    }

}
