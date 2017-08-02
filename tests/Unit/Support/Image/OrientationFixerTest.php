<?php
namespace Czim\FileHandling\Test\Unit\Support\Image;

use Czim\FileHandling\Support\Image\OrientationFixer;
use Czim\FileHandling\Test\TestCase;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Mockery;
use SplFileInfo;

class OrientationFixerTest extends TestCase
{
    const ROTATED_IMAGE_PATH   = __DIR__ . '/../../../resources/rotated-?.jpg';
    const UNROTATED_IMAGE_PATH = __DIR__ . '/../../../resources/unrotated.jpg';
    const IMAGE_COPY_PATH      = __DIR__ . '/../../../resources/tmp.jpg';

    public function setUp()
    {
        $this->cleanupTempFile();
    }

    public function tearDown()
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
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_3()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_UPSIDE_DOWN);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_UPSIDE_DOWN)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_4()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_5()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY_AND_ROTATED_LEFT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY_AND_ROTATED_LEFT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_6()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_ROTATED_LEFT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_ROTATED_LEFT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_7()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY_AND_ROTATED_LEFT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY_AND_ROTATED_LEFT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_reorients_a_rotated_image_instance_at_orientation_8()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_ROTATED_RIGHT);

        $image = $fixer->fixImage(
            $source->getRealPath(),
            $this->getMockImage(OrientationFixer::ORIENTATION_ROTATED_RIGHT)
        );

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     * @requires function exif_read_data
     */
    function it_does_not_reorient_an_image_instance_that_is_not_rotated()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(false);

        $image = $fixer->fixImage($source->getRealPath(), $this->getMockImage(false));

        static::assertInstanceof(ImageInterface::class, $image);
    }

    /**
     * @test
     */
    function it_fixes_the_orientation_for_a_file()
    {
        $fixer = new OrientationFixer;

        $source = $this->makeSourceFile(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY);
        $file   = new SplFileInfo($source->getRealPath());

        $image   = $this->getMockImage(OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY, true);
        $imagine = $this->getMockImageProcessor($image);

        static::assertTrue($fixer->fixFile($file, $imagine));
    }

    // ------------------------------------------------------------------------------
    //      Helpers
    // ------------------------------------------------------------------------------

    /**
     * @param int|false $rotated
     * @return SplFileInfo
     */
    protected function makeSourceFile($rotated = OrientationFixer::ORIENTATION_ROTATED_LEFT)
    {
        if ( ! $rotated) {
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
    protected function getMockImage($expectsChange = OrientationFixer::ORIENTATION_ROTATED_LEFT, $expectsSave = false)
    {
        $image = Mockery::mock(ImageInterface::class);

        if ($expectsChange) {
            switch ($expectsChange) {

                case OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY:
                    $image->shouldReceive('flipHorizontally')->once()->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_UPSIDE_DOWN:
                    $image->shouldReceive('rotate')->once()->with(180)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY:
                    $image->shouldReceive('flipVertically')->once()->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_FLIPPED_VERTICALLY_AND_ROTATED_LEFT:
                    $image->shouldReceive('flipVertically')->once()->andReturnSelf();
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_ROTATED_LEFT:
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_FLIPPED_HORIZONTALLY_AND_ROTATED_LEFT:
                    $image->shouldReceive('flipHorizontally')->once()->andReturnSelf();
                    $image->shouldReceive('rotate')->once()->with(90)->andReturnSelf();
                    break;

                case OrientationFixer::ORIENTATION_ROTATED_RIGHT:
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
    protected function getMockImageProcessor($image)
    {
        $imageProcessor = Mockery::mock(ImagineInterface::class);
        $imageProcessor->shouldReceive('open')->once()->andReturn($image);

        return $imageProcessor;
    }

    /**
     * Cleans up the temporary image file.
     */
    protected function cleanupTempFile()
    {
        if (file_exists(realpath(static::IMAGE_COPY_PATH))) {
            unlink(realpath(static::IMAGE_COPY_PATH));
        }
    }

}
