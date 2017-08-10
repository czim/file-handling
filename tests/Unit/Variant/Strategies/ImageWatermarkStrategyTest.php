<?php
namespace Czim\FileHandling\Test\Unit\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\ImageWatermarkStrategy;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\PointInterface;
use Mockery;

class ImageWatermarkStrategyTest extends TestCase
{

    /**
     * @test
     * @expectedException \Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException
     */
    function it_should_throw_an_exception_if_it_is_applied_to_a_non_image()
    {
        $strategy = new ImageWatermarkStrategy($this->getMockImagine());

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('text/plain');

        $strategy->apply($file);
    }

    /**
     * @test
     */
    function it_watermarks_an_image_bottom_right_by_default()
    {
        $imagine   = $this->getMockImagine();
        $image     = $this->getMockImage();
        $watermark = $this->getMockImageForWatermark();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andReturn($watermark);

        $image->shouldReceive('paste')->once()
            ->with(Mockery::type(ImageInterface::class), Mockery::on(function ($position) {
                return $this->comparePosition($position, 500, 300);
            }))
            ->andReturnSelf();

        $image->shouldReceive('save')->once()->andReturnSelf();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

    /**
     * @test
     */
    function it_watermarks_an_image_top_left()
    {
        $imagine   = $this->getMockImagine();
        $image     = $this->getMockImage();
        $watermark = $this->getMockImageForWatermark();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andReturn($watermark);

        $image->shouldReceive('paste')->once()
            ->with(Mockery::type(ImageInterface::class), Mockery::on(function ($position) {
                return $this->comparePosition($position, 0, 0);
            }))
            ->andReturnSelf();

        $image->shouldReceive('save')->once()->andReturnSelf();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
            'position'  => 'top-left',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

    /**
     * @test
     */
    function it_watermarks_an_image_top_right()
    {
        $imagine   = $this->getMockImagine();
        $image     = $this->getMockImage();
        $watermark = $this->getMockImageForWatermark();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andReturn($watermark);

        $image->shouldReceive('paste')->once()
            ->with(Mockery::type(ImageInterface::class), Mockery::on(function ($position) {
                return $this->comparePosition($position, 500, 0);
            }))
            ->andReturnSelf();

        $image->shouldReceive('save')->once()->andReturnSelf();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
            'position'  => 'top-right',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

    /**
     * @test
     */
    function it_watermarks_an_image_bottom_left()
    {
        $imagine   = $this->getMockImagine();
        $image     = $this->getMockImage();
        $watermark = $this->getMockImageForWatermark();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andReturn($watermark);

        $image->shouldReceive('paste')->once()
            ->with(Mockery::type(ImageInterface::class), Mockery::on(function ($position) {
                return $this->comparePosition($position, 0, 300);
            }))
            ->andReturnSelf();

        $image->shouldReceive('save')->once()->andReturnSelf();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
            'position'  => 'bottom-left',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }

    /**
     * @test
     */
    function it_watermarks_an_image_center()
    {
        $imagine   = $this->getMockImagine();
        $image     = $this->getMockImage();
        $watermark = $this->getMockImageForWatermark();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andReturn($watermark);

        $image->shouldReceive('paste')->once()
            ->with(Mockery::type(ImageInterface::class), Mockery::on(function ($position) {
                return $this->comparePosition($position, 250, 150);
            }))
            ->andReturnSelf();

        $image->shouldReceive('save')->once()->andReturnSelf();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
            'position'  => 'center',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        static::assertSame($file, $strategy->apply($file));
    }


    /**
     * @test
     */
    function it_returns_false_if_no_watermark_image_is_configured()
    {
        $imagine = $this->getMockImagine();
        $image   = $this->getMockImage();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);

        $image->shouldReceive('paste')->never();
        $image->shouldReceive('save')->never();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions([]);

        static::assertFalse($strategy->apply($file));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function it_throws_a_runtime_exception_if_watermark_image_could_not_be_found()
    {
        $imagine = $this->getMockImagine();
        $image   = $this->getMockImage();

        $imagine->shouldReceive('open')->once()->with('tmp/test.jpg')->andReturn($image);
        $imagine->shouldReceive('open')->once()->with('tmp/mark.png')->andThrow(\ErrorException::class);

        $image->shouldReceive('paste')->never();
        $image->shouldReceive('save')->never();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');
        $file->shouldReceive('path')->andReturn('tmp/test.jpg');

        $options = [
            'watermark' => 'tmp/mark.png',
        ];

        $strategy = new ImageWatermarkStrategy($imagine);
        $strategy->setOptions($options);

        $strategy->apply($file);
    }


    /**
     * @return Mockery\MockInterface|ImagineInterface
     */
    protected function getMockImagine()
    {
        return Mockery::mock(ImagineInterface::class);
    }

    /**
     * @return Mockery\MockInterface|ImageInterface
     */
    protected function getMockImage()
    {
        $mock =  Mockery::mock(ImageInterface::class);
        $mock->shouldReceive('getSize')->once()->andReturn($this->getMockSize(600, 400));
        return $mock;
    }

    /**
     * @return Mockery\MockInterface|ImageInterface
     */
    protected function getMockImageForWatermark()
    {
        $mock = Mockery::mock(ImageInterface::class);
        $mock->shouldReceive('getSize')->once()->andReturn($this->getMockSize(100, 100));
        return $mock;
    }

    /**
     * @param int $width
     * @param int $height
     * @return Mockery\MockInterface
     */
    protected function getMockSize($width, $height)
    {
        $mock = Mockery::mock(BoxInterface::class);
        $mock->shouldReceive('getWidth')->andReturn($width);
        $mock->shouldReceive('getHeight')->andReturn($height);
        return $mock;
    }

    /**
     * @param mixed $position
     * @param int $x
     * @param int $y
     * @return bool
     */
    protected function comparePosition($position, $x, $y)
    {
        if ( ! ($position instanceof PointInterface)) {
            return $position;
        }

        return $position->getX() == $x && $position->getY() == $y;
    }

}
