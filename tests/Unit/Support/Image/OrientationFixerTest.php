<?php

namespace Czim\FileHandling\Test\Unit\Support\Image;

use Czim\FileHandling\Support\Image\OrientationFixer;
use Czim\FileHandling\Test\TestCase;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Mockery;
use SplFileInfo;

class OrientationFixerTest extends TestCase
{
    protected const ROTATED_IMAGE_PATH   = __DIR__ . '/../../../resources/rotated-?.jpg';
    protected const UNROTATED_IMAGE_PATH = __DIR__ . '/../../../resources/unrotated.jpg';
    protected const IMAGE_COPY_PATH      = __DIR__ . '/../../../resources/tmp.jpg';

    public function setUp(): void
    {
        $this->cleanupTempFile();
    }

    public function tearDown(): void
    {
        Mockery::close();

        $this->cleanupTempFile();
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_2()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_TOPRIGHT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_TOPRIGHT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_3()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_BOTTOMRIGHT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_BOTTOMRIGHT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_4()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_BOTTOMLEFT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_BOTTOMLEFT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_5()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_LEFTTOP);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_LEFTTOP)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_6()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_RIGHTTOP);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_RIGHTTOP)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_7()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_RIGHTBOTTOM);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_RIGHTBOTTOM)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_8()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_LEFTBOTTOM);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_LEFTBOTTOM)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_does_not_reorient_an_image_instance_that_is_not_rotated()
    {
        $fixer = new OrientationFixer(new Imagine());

        $source = $this->makeSourceFile(false);

        $image = $fixer->fixImage($source->getRealPath(), $this->getMockImage(false));

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     */
    function it_fixes_the_orientation_for_a_file()
    {
        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_TOPRIGHT);
        $file   = new SplFileInfo($source->getRealPath());

        $image   = $this->getMockImage(OrientationFixer::ORIENTATION_TOPRIGHT, true);
        $imagine = $this->getMockImageProcessor($image);

        $fixer = new OrientationFixer($imagine);

        static::assertTrue($fixer->fixFile($file));
    }

    /**
     * @test
     */
    function it_enables_and_disables_quiet_mode()
    {
        $fixer = new OrientationFixer(new Imagine());

        static::assertTrue($fixer->isQuiet());

        $fixer->disableQuietMode();

        static::assertFalse($fixer->isQuiet());

        $fixer->enableQuietMode();

        static::assertTrue($fixer->isQuiet());
    }

    // ------------------------------------------------------------------------------
    //      Helpers
    // ------------------------------------------------------------------------------

    /**
     * @param int|false $rotated
     * @return SplFileInfo
     */
    protected function makeSourceFile($rotated = OrientationFixer::ORIENTATION_RIGHTTOP): SplFileInfo
    {
        if (! $rotated) {
            $original = realpath(static::UNROTATED_IMAGE_PATH);
        } else {
            $original = realpath(str_replace('?', $rotated, static::ROTATED_IMAGE_PATH));
        }
        $copy = static::IMAGE_COPY_PATH;

        copy($original, $copy);

        return new SplFileInfo($copy);
    }

    /**
     * @param int|false $expectsChange
     * @param bool      $expectsSave
     * @return ImageInterface|Mockery\MockInterface
     */
    protected function getMockImage($expectsChange = OrientationFixer::ORIENTATION_RIGHTTOP, bool $expectsSave = false)
    {
        /** @var Mockery\MockInterface|Mockery\Mock|ImageInterface $image */
        $image = Mockery::mock(ImageInterface::class);

        if ($expectsChange) {
            switch ($expectsChange) {

                case OrientationFixer::ORIENTATION_TOPRIGHT:
                    $image->shouldReceive('flipHorizontally')->once()->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_BOTTOMRIGHT:
                    $image->shouldReceive('rotate')->once()->with(180)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_BOTTOMLEFT:
                    $image->shouldReceive('flipVertically')->once()->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_LEFTTOP:
                    $image->shouldReceive('flipVertically')->once()->andReturnSelf();
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_RIGHTTOP:
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_RIGHTBOTTOM:
                    $image->shouldReceive('flipHorizontally')->once()->andReturnSelf();
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_LEFTBOTTOM:
                    $image->shouldReceive('rotate')->once()->with(-90)->andReturnSelf();
                    break;
            }

            $image->shouldReceive('strip')->once()->andReturnSelf();

        } else {
            $image->shouldReceive('strip')->never();
        }

        if ($expectsSave) {
            $image->shouldReceive('save')->once()->andReturnSelf();
        } else {
            $image->shouldReceive('save')->never();
        }

        return $image;
    }

    /**
     * @param ImageInterface $image
     * @return Mockery\MockInterface|ImagineInterface
     */
    protected function getMockImageProcessor(ImageInterface $image)
    {
        /** @var Mockery\Mock|Mockery\MockInterface|ImagineInterface $imageProcessor */
        $imageProcessor = Mockery::mock(ImagineInterface::class);
        $imageProcessor->shouldReceive('open')->once()->andReturn($image);

        return $imageProcessor;
    }

    protected function cleanupTempFile(): void
    {
        if (file_exists(realpath(static::IMAGE_COPY_PATH))) {
            unlink(realpath(static::IMAGE_COPY_PATH));
        }
    }

}
