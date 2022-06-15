<?php

namespace Czim\FileHandling\Test\Integration\Variant\Strategies;

use Czim\FileHandling\Contracts\Storage\ProcessableFileInterface;
use Czim\FileHandling\Exceptions\VariantStrategyShouldNotBeAppliedException;
use Czim\FileHandling\Storage\File\ProcessableFile;
use Czim\FileHandling\Test\TestCase;
use Czim\FileHandling\Variant\Strategies\VideoScreenshotStrategy;
use Mockery;

class VideoScreenshotStrategyTest extends TestCase
{
    protected const MOVIE_TEST_FILE = 'tests/resources/video.mov';

    public function setUp(): void
    {
        if (! file_exists('/usr/local/bin/ffmpeg')) {
            static::markTestSkipped('FFMpeg binary not available');
        }
    }

    /**
     * @test
     */
    function it_should_throw_an_exception_if_it_is_applied_to_a_non_video()
    {
        $this->expectException(VariantStrategyShouldNotBeAppliedException::class);

        $strategy = new VideoScreenshotStrategy();

        /** @var Mockery\MockInterface|ProcessableFileInterface $file */
        $file = Mockery::mock(ProcessableFileInterface::class);
        $file->shouldReceive('mimeType')->andReturn('image/jpeg');

        $strategy->apply($file);
    }

    /**
     * @test
     */
    function it_takes_a_screenshot()
    {
        $file = new ProcessableFile();
        $file->setName('video.mov');
        $file->setMimeType('video/mov');
        $file->setData($this->getExampleLocalPath());


        $options = [
            'ffmpeg'  => '/usr/local/bin/ffmpeg',
            'ffprobe' => '/usr/local/bin/ffprobe',
        ];

        $strategy = new VideoScreenshotStrategy();
        $strategy->setOptions($options);

        static::assertInstanceOf(ProcessableFileInterface::class, $strategy->apply($file));

        static::assertEquals('video.jpg', $file->name());
        static::assertEquals('tests/resources/video.jpg', substr($file->path(), -25));

        // Clean up
        if (file_exists($file->path())) {
            unlink($file->path());
        }
    }

    /**
     * @test
     */
    function it_takes_a_screenshot_at_a_specific_second_amount()
    {
        $file = new ProcessableFile();
        $file->setName('video.mov');
        $file->setMimeType('video/mov');
        $file->setData($this->getExampleLocalPath());


        $options = [
            'seconds' => 0.1,
            'ffmpeg'  => '/usr/local/bin/ffmpeg',
            'ffprobe' => '/usr/local/bin/ffprobe',
        ];

        $strategy = new VideoScreenshotStrategy();
        $strategy->setOptions($options);

        static::assertInstanceOf(ProcessableFileInterface::class, $strategy->apply($file));

        static::assertEquals('video.jpg', $file->name());
        static::assertEquals('tests/resources/video.jpg', substr($file->path(), -25));

        // Clean up
        if (file_exists($file->path())) {
            unlink($file->path());
        }
    }

    /**
     * @test
     */
    function it_takes_a_screenshot_from_a_percentage_of_duration()
    {
        $file = new ProcessableFile();
        $file->setName('video.mov');
        $file->setMimeType('video/mov');
        $file->setData($this->getExampleLocalPath());


        $options = [
            'percentage' => 75,
            'ffmpeg'     => '/usr/local/bin/ffmpeg',
            'ffprobe'    => '/usr/local/bin/ffprobe',
        ];

        $strategy = new VideoScreenshotStrategy();
        $strategy->setOptions($options);

        static::assertInstanceOf(ProcessableFileInterface::class, $strategy->apply($file));

        static::assertEquals('video.jpg', $file->name());
        static::assertEquals('tests/resources/video.jpg', substr($file->path(), -25));

        // Clean up
        if (file_exists($file->path())) {
            unlink($file->path());
        }
    }


    protected function getExampleLocalPath(): string
    {
        return realpath(dirname(__DIR__) . '/../../../' . static::MOVIE_TEST_FILE);
    }

}
