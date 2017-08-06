<?php
namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Support\Image\OrientationFixer;
use Czim\FileHandling\Support\Image\Resizer;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy;
use Czim\FileHandling\Variant\Strategies\ImageResizeStrategy;
use Mockery;
use SplFileInfo;

class ImageResizeStrategyTest extends TestCase
{

    /**
     * @test
     */
    function it_should_apply_only_to_images()
    {
        /** @var Mockery\MockInterface|Resizer $resizer */
        $resizer = Mockery::mock(Resizer::class);

        $strategy = new ImageResizeStrategy($resizer);

        static::assertTrue($strategy->shouldApplyForMimeType('image/jpeg'));
        static::assertFalse($strategy->shouldApplyForMimeType('video/mpeg'));
        static::assertFalse($strategy->shouldApplyForMimeType('text/plain'));
    }

    /**
     * @test
     */
    function it_rotates_an_image()
    {
        /** @var Mockery\MockInterface|Resizer $resizer */
        $resizer = Mockery::mock(Resizer::class);

        /** @var Mockery\MockInterface|SplFileInfo $file */
        $file = Mockery::mock(SplFileInfo::class);

        $options = ['test' => true];

        $resizer->shouldReceive('resize')->once()->with($file, $options)->andReturn(true);

        $strategy = new ImageResizeStrategy($resizer);
        $strategy->setOptions($options);

        static::assertTrue($strategy->apply($file));
    }

}
