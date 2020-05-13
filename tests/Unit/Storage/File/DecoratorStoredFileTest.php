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
        $mock->shouldReceive('openStream')->once()->with()->andReturn(null);
        $mock->shouldReceive('closeStream')->once()->with(null);
        $mock->shouldReceive('extension')->once()->with()->andReturn('gif');
        $mock->shouldReceive('isUploaded')->once()->with()->andReturn(false);
        $mock->shouldReceive('name')->once()->with()->andReturn('test.gif');
        $mock->shouldReceive('mimeType')->once()->with()->andReturn('test/type');
        $mock->shouldReceive('path')->once()->with()->andReturn('test/path');
        $mock->shouldReceive('size')->once()->with()->andReturn(100);
        $mock->shouldReceive('copy')->once()->with('/tmp/testing.txt')->andReturn(true);
        $mock->shouldReceive('delete')->once();

        $file = new DecoratorStoredFile($mock);

        static::assertEquals('content', $file->content());
        static::assertNull($file->openStream());
        $file->closeStream(null);
        static::assertEquals('gif', $file->extension());
        static::assertFalse($file->isUploaded());
        static::assertEquals('test/type', $file->mimeType());
        static::assertEquals('test.gif', $file->name());
        static::assertEquals('test/path', $file->path());
        static::assertEquals(100, $file->size());
        static::assertEquals(true, $file->copy('/tmp/testing.txt'));
        $file->delete();
    }

    /**
     * @test
     */
    function it_sets_and_returns_a_url()
    {
        $mock = $this->getMockStorableFile();

        $file = new DecoratorStoredFile($mock);

        $file->setUrl('http://test/url');

        static::assertEquals('http://test/url', $file->url());
    }


    /**
     * @return Mockery\Mock|Mockery\MockInterface|StorableFileInterface
     */
    protected function getMockStorableFile()
    {
        return Mockery::mock(StorableFileInterface::class);
    }
}
