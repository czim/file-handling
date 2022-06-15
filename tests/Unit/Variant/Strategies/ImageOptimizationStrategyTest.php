<?php

namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;
use Czim\FileHandling\Support\Image\Optimizer;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageOptimizationStrategy;
use Mockery;

class ImageOptimizationStrategyTest extends TestCase
{

    /**
     * @test
     */
    function it_should_throw_an_exception_if_it_is_applied_to_a_non_image()
    {
        $this->expectException(VariantStrategyShouldNotBeAppliedException::class);

        /** @var Mockery\MockInterface|Optimizer $optimizer */
        $optimizer = Mockery::mock(Optimizer::class);

        $strategy = new ImageOptimizationStrategy($optimizer);

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('text/plain');

        $strategy->apply($file);
    }

    /**
     * @test
     */
    function it_optimizes_an_image()
    {
        /** @var Mockery\MockInterface|Optimizer $optimizer */
        $optimizer = Mockery::mock(Optimizer::class);

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = ['test' => true];

        $optimizer->shouldReceive('optimize')
            ->once()
            ->with(Mockery::type(\SplFileInfo::class), $options)
            ->andReturn(true);

        $strategy = new ImageOptimizationStrategy($optimizer);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

}
