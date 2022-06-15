<?php

namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;
use Czim\FileHandling\Support\Image\OrientationFixer;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy;
use Mockery;

class ImageAutoOrientStrategyTest extends TestCase
{

    /**
     * @test
     */
    function it_should_throw_an_exception_if_it_is_applied_to_a_non_image()
    {
        $this->expectException(VariantStrategyShouldNotBeAppliedException::class);

        /** @var Mockery\Mock|Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        $strategy = new ImageAutoOrientStrategy($fixer);

        /** @var Mockery\Mock|Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('video/mpeg');

        $strategy->apply($file);
    }

    /**
     * @test
     */
    function it_auto_orients_an_image()
    {
        /** @var Mockery\Mock|Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        /** @var Mockery\Mock|Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('extension')->andReturn('jpg');
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $fixer->shouldReceive('fixFile')->once()->andReturn(true);

        $strategy = new ImageAutoOrientStrategy($fixer);

        static::assertSame($file, $strategy->apply($file));
    }

    /**
     * @test
     */
    function it_can_disable_quiet_mode_on_the_fixer()
    {
        /** @var Mockery\Mock|Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        /** @var Mockery\Mock|Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('extension')->andReturn('jpg');
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $fixer->shouldReceive('fixFile')->once()->andReturn(true);
        $fixer->shouldReceive('disableQuietMode')->once()->andReturnSelf();

        $strategy = new ImageAutoOrientStrategy($fixer);
        $strategy->setOptions(['quiet' => false]);

        static::assertSame($file, $strategy->apply($file));
    }

}
