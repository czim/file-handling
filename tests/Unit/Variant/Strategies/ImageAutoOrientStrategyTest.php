<?php
namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Support\Image\OrientationFixer;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy;
use Mockery;
use SplFileInfo;

class ImageAutoOrientStrategyTest extends TestCase
{

    /**
     * @test
     */
    function it_should_apply_only_to_images()
    {
        /** @var Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        $strategy = new ImageAutoOrientStrategy($fixer);

        static::assertTrue($strategy->shouldApplyForMimeType('image/jpeg'));
        static::assertFalse($strategy->shouldApplyForMimeType('video/mpeg'));
        static::assertFalse($strategy->shouldApplyForMimeType('text/plain'));
    }

    /**
     * @test
     */
    function it_auto_orients_an_image()
    {
        /** @var Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        /** @var Mockery\MockInterface|SplFileInfo $file */
        $file = Mockery::mock(SplFileInfo::class);

        $fixer->shouldReceive('fixFile')->once()->andReturn(true);

        $strategy = new ImageAutoOrientStrategy($fixer);

        static::assertTrue($strategy->apply($file));
    }

    /**
     * @test
     */
    function it_can_disable_quiet_mode_on_the_fixer()
    {
        /** @var Mockery\MockInterface|OrientationFixer $fixer */
        $fixer = Mockery::mock(OrientationFixer::class);

        /** @var Mockery\MockInterface|SplFileInfo $file */
        $file = Mockery::mock(SplFileInfo::class);

        $fixer->shouldReceive('fixFile')->once()->with($file)->andReturn(true);
        $fixer->shouldReceive('disableQuietMode')->once()->andReturnSelf();

        $strategy = new ImageAutoOrientStrategy($fixer);
        $strategy->setOptions(['quiet' => false]);

        static::assertTrue($strategy->apply($file));
    }

}
