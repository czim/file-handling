<?php
namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Storage\File\DecoratorStoredFile;
use Czim\FileHandling\Test\TestCase;
use Mockery;

class DecoratorStoredFileTest extends TestCase
{

    /**
     * @test
     */
    function it_wraps_a_storable_file_instance()
    {
        $mock = $this->getMockStorableFile();
        $mock->shouldReceive('content')->once()->with()->andReturn('content');
        $mock->shouldReceive('extension')->once()->with()->andReturn('gif');
        $mock->shouldReceive('isUploaded')->once()->with()->andReturn(false);
        $mock->shouldReceive('name')->once()->with()->andReturn('test.gif');
        $mock->shouldReceive('mimeType')->once()->with()->andReturn('test/type');
        $mock->shouldReceive('path')->once()->with()->andReturn('test/path');
        $mock->shouldReceive('size')->once()->with()->andReturn(100);
        $mock->shouldReceive('copy')->once()->with('/tmp/testing.txt')->andReturn(true);

        $file = new DecoratorStoredFile($mock);

        static::assertEquals('content', $file->content());
        static::assertEquals('gif', $file->extension());
        static::assertFalse($file->isUploaded());
        static::assertEquals('test/type', $file->mimeType());
        static::assertEquals('test.gif', $file->name());
        static::assertEquals('test/path', $file->path());
        static::assertEquals(100, $file->size());
        static::assertEquals(true, $file->copy('/tmp/testing.txt'));
    }

    /**
     * @test
     */
    function it_sets_and_returns_a_url()
    {
        $mock = $this->getMockStorableFile();

        $file = new DecoratorStoredFile($mock);

        static::assertSame($file, $file->setUrl('http://test/url'));
        static::assertEquals('http://test/url', $file->url());
    }


    /**
     * @return Mockery\MockInterface|StorableFileInterface
     */
    protected function getMockStorableFile()
    {
        return Mockery::mock(StorableFileInterface::class);
    }

}
