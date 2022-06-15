<?php

namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;
use Czim\FileHandling\Support\Image\Resizer;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageResizeStrategy;
use Mockery;

class ImageResizeStrategyTest extends TestCase
{

    /**
     * @test
     */
    function it_should_throw_an_exception_if_it_is_applied_to_a_non_image()
    {
        $this->expectException(VariantStrategyShouldNotBeAppliedException::class);

        /** @var Mockery\Mock|Mockery\MockInterface|Resizer $resizer */
        $resizer = Mockery::mock(Resizer::class);

        $strategy = new ImageResizeStrategy($resizer);

        /** @var Mockery\Mock|Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('text/plain');

        $strategy->apply($file);
    }

    /**
     * @test
     */
    function it_rotates_an_image()
    {
        /** @var Mockery\Mock|Mockery\MockInterface|Resizer $resizer */
        $resizer = Mockery::mock(Resizer::class);

        /** @var Mockery\Mock|Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = ['test' => true];

        $resizer->shouldReceive('resize')
            ->once()
            ->with(Mockery::type(\SplFileInfo::class), $options)
            ->andReturn(true);

        $strategy = new ImageResizeStrategy($resizer);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

}
