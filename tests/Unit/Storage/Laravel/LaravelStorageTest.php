<?php

namespace Czim\FileHandling\Storage\Laravel;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Exceptions\FileStorageException;
use Czim\FileHandling\Test\TestCase;
use Mockery;

class LaravelStorageTest extends TestCase
{

    /**
     * @test
     */
    function it_returns_whether_a_path_exists()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('exists')->once()->with('relative/path')->andReturn(true);

        $storage = new LaravelStorage($files, true, 'http://testing');

        static::assertTrue($storage->exists('relative/path'));
    }

    /**
     * @test
     */
    function it_returns_a_full_url_for_a_relative_path()
    {
        $files = $this->getMockLaravelFilesystem();

        $storage = new LaravelStorage($files, true, 'http://testing');

        static::assertEquals('http://testing/relative/path', $storage->url('relative/path'));
    }

    /**
     * @test
     */
    function it_returns_the_contents_for_a_path()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('get')->once()->with('relative/path/test.txt')->andReturn('contents');

        $storage = new LaravelStorage($files, true, 'http://testing');

        $stored = $storage->get('relative/path/test.txt');

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertEquals('test.txt', $stored->name());
        static::assertEquals('contents', $stored->content());
    }

    /**
     * @test
     */
    function it_stores_a_file_without_streaming()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->once()->with('relative/path/test.txt', 'contents')->andReturn(true);
        $files->shouldReceive('writeStream')->never();

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
        $file->shouldReceive('openStream')->once()->andReturn(null);
        $file->shouldReceive('closeStream')->never();
        $file->shouldReceive('content')->andReturn('contents');

        $storage = new LaravelStorage($files, true, 'http://testing');

        $stored = $storage->store($file, 'relative/path/test.txt');

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertEquals('test.txt', $stored->name());
        static::assertEquals('contents', $stored->content());
    }

    /**
     * @test
     */
    function it_stores_a_file_with_streaming()
    {
        $stream = fopen(realpath(__DIR__ . '/../../../resources/test.txt'), 'r');

        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->never();
        $files->shouldReceive('writeStream')->once()->with('relative/path/test.txt', $stream)->andReturn(true);
        $files->shouldReceive('exists')->with('relative/path/test.txt')->andReturn(false);

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
        $file->shouldReceive('openStream')->once()->andReturn($stream);
        $file->shouldReceive('closeStream')->once()->with($stream);
        $file->shouldReceive('content')->never();

        $storage = new LaravelStorage($files, true, 'http://testing');

        $stored = $storage->store($file, 'relative/path/test.txt');

        fclose($stream);

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertEquals('test.txt', $stored->name());
        static::assertEquals('http://testing/relative/path/test.txt', $stored->url());
    }

    /**
     * @test
     */
    function it_stores_a_file_with_streaming_overwriting_existing_file()
    {
        $stream = fopen(realpath(__DIR__ . '/../../../resources/test.txt'), 'r');

        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->never();
        $files->shouldReceive('writeStream')->once()->with('relative/path/test.txt', $stream)->andReturn(true);
        $files->shouldReceive('exists')->with('relative/path/test.txt')->andReturn(true);
        $files->shouldReceive('delete')->once()->with('relative/path/test.txt')->andReturn(true);

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
        $file->shouldReceive('openStream')->once()->andReturn($stream);
        $file->shouldReceive('closeStream')->once()->with($stream);
        $file->shouldReceive('content')->never();

        $storage = new LaravelStorage($files, true, 'http://testing');

        $stored = $storage->store($file, 'relative/path/test.txt');

        fclose($stream);

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertEquals('test.txt', $stored->name());
        static::assertEquals('http://testing/relative/path/test.txt', $stored->url());
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_it_fails_to_store_a_file()
    {
        $this->expectException(FileStorageException::class);

        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->once()->with('relative/path/test.txt', 'contents')->andReturn(false);

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
        $file->shouldReceive('openStream')->once()->andReturn(null);
        $file->shouldReceive('content')->andReturn('contents');

        $storage = new LaravelStorage($files, true, 'http://testing');

        $storage->store($file, 'relative/path/test.txt');
    }

    /**
     * @test
     */
    function it_deletes_a_path()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('delete')->once()->with('relative/path')->andReturn(true);

        $storage = new LaravelStorage($files, true, 'http://testing');

        static::assertTrue($storage->delete('relative/path'));
    }


    /**
     * @return Mockery\Mock|Mockery\MockInterface|\Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getMockLaravelFilesystem()
    {
        return Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|StorableFileInterface
     */
    protected function getMockStorableFile()
    {
        return Mockery::mock(StorableFileInterface::class);
    }

}
