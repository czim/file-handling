<?php

namespace Czim\FileHandling\Test\Unit\Support\Image;

use Czim\FileHandling\Support\Image\Resizer;
use Czim\FileHandling\Test\TestCase;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Mockery;
use SplFileInfo;

class ResizerTest extends TestCase
{
    protected const IMAGE_COPY_PATH = __DIR__ . '/../../../resources/tmp.gif';


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
     */
    public function it_resizes_and_crops_an_image()
    {
        $source = $this->makeSourceFile();

        $originalSize      = new Box(600, 400);
        $expectedResize    = new Box(768, 512);
        $expectedCropPoint = new Point(128, 0);
        $expectedCropBox   = new Box(512, 512);

        $image          = $this->getMockImage($originalSize, $expectedResize, $expectedCropPoint, $expectedCropBox);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('512x512#');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_exact_dimensions()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(600, 400);
        $expectedResize = new Box(50, 50);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('50x50!');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_automatic_dimensions_for_landscape()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(600, 400);
        $expectedResize = new Box(519, 346);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('519x360');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_automatic_dimensions_for_portrait()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(400, 600);
        $expectedResize = new Box(240, 360);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('520x360');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_automatic_dimensions_for_square_to_landscape()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(400, 400);
        $expectedResize = new Box(520, 520);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('520x360');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_automatic_dimensions_for_square_to_portrait()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(400, 400);
        $expectedResize = new Box(520, 520);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('360x520');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_automatic_dimensions_for_square_to_square()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(400, 400);
        $expectedResize = new Box(360, 360);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('360x360');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_keep_ratio_for_specific_width()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(600, 400);
        $expectedResize = new Box(510, 340);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('510');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_an_image_to_keep_ratio_for_specific_height()
    {
        $source = $this->makeSourceFile();

        $originalSize   = new Box(600, 400);
        $expectedResize = new Box(510, 340);

        $image          = $this->getMockImage($originalSize, $expectedResize);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('x340');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    public function it_resizes_and_crops_an_edge_case()
    {
        $source = $this->makeSourceFile();

        $originalSize      = new Box(1000, 653);
        $expectedResize    = new Box(440, 287.32);
        $expectedCropPoint = new Point(0, 21.66);
        $expectedCropBox   = new Box(440, 244);

        $image          = $this->getMockImage($originalSize, $expectedResize, $expectedCropPoint, $expectedCropBox);
        $imageProcessor = $this->getMockImageProcessor($image);

        $options = $this->buildMockOptions('440x244#');

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
    }

    /**
     * @test
     */
    function it_resizes_using_a_custom_callback()
    {
        $source = $this->makeSourceFile();

        /** @var ImagineInterface $imageProcessor */
        $imageProcessor = Mockery::mock(ImagineInterface::class);

        $customCalled = false;

        $custom = function () use (&$customCalled) {
            $customCalled = true;
            return (new Imagine())->create(new Box(100, 100));
        };

        $options = $this->buildMockOptions($custom);

        $resizer = new Resizer($imageProcessor);

        static::assertTrue($resizer->resize($source, $options));
        static::assertTrue($customCalled, 'Custom callable spy not flagged');
    }


    // ------------------------------------------------------------------------------
    //      Helpers
    // ------------------------------------------------------------------------------

    protected function makeSourceFile(): SplFileInfo
    {
        $original = realpath(__DIR__ . '/../../../resources/empty.gif');
        $copy     = static::IMAGE_COPY_PATH;

        copy($original, $copy);

        return new SplFileInfo($copy);
    }

    /**
     * @param BoxInterface   $originalSize
     * @param BoxInterface   $expectedResize
     * @param PointInterface $expectedCropPoint
     * @param BoxInterface   $expectedCropBox
     * @return Mockery\MockInterface|ImageInterface
     */
    protected function getMockImage(
        BoxInterface $originalSize,
        BoxInterface $expectedResize,
        PointInterface $expectedCropPoint = null,
        BoxInterface $expectedCropBox = null
    ) {
        /** @var ImageInterface|Mockery\Mock|Mockery\MockInterface $image */
        $image = Mockery::mock(ImageInterface::class);

        $resizeComparison = function ($resize) use ($expectedResize) {
            if (! ($resize instanceof BoxInterface)) {
                return false;
            }

            return $resize->getWidth() == $expectedResize->getWidth()
                && $resize->getHeight() == $expectedResize->getHeight();
        };

        $cropPointComparison = function ($cropPoint) use ($expectedCropPoint) {
            if (! ($cropPoint instanceof PointInterface)) {
                return false;
            }

            return round($cropPoint->getX(), 2) == round($expectedCropPoint->getX(), 2)
                && round($cropPoint->getY(), 2) == round($expectedCropPoint->getY(), 2);
        };

        $image->shouldReceive('getSize')->andReturn($originalSize);
        $image->shouldReceive('resize')->once()->with(Mockery::on($resizeComparison))->andReturn($image);

        if (null !== $expectedCropPoint) {

            $cropBoxComparison = function ($cropBox) use ($expectedCropBox) {

                if (! ($cropBox instanceof BoxInterface)) {
                    return false;
                }

                return $cropBox->getWidth() == $expectedCropBox->getWidth()
                    && $cropBox->getHeight() == $expectedCropBox->getHeight();
            };

            $image->shouldReceive('crop')
                ->once()
                ->with(Mockery::on($cropPointComparison), Mockery::on($cropBoxComparison))
                ->andReturn($image);
        }

        $image->shouldReceive('save')->once();

        return $image;
    }

    /**
     * @param ImageInterface $image
     * @return Mockery\MockInterface|ImagineInterface
     */
    protected function getMockImageProcessor(ImageInterface $image)
    {
        /** @var ImagineInterface|Mockery\Mock|Mockery\MockInterface $imageProcessor */
        $imageProcessor = Mockery::mock(ImagineInterface::class);
        $imageProcessor->shouldReceive('open')->once()->andReturn($image);

        return $imageProcessor;
    }

    /**
     * @param string|callable $value
     * @param array           $convertOptions
     * @return array
     */
    protected function buildMockOptions($value, array $convertOptions = []): array
    {
        return [
            'dimensions'     => $value,
            'convertOptions' => $convertOptions,
        ];
    }

    protected function cleanupTempFile(): void
    {
        if (file_exists(realpath(static::IMAGE_COPY_PATH))) {
            unlink(realpath(static::IMAGE_COPY_PATH));
        }
    }

}
