<?php
namespace Czim\FileHandling\Storage\Laravel;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
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
    function it_stores_a_file()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->once()->with('relative/path/test.txt', 'contents')->andReturn(true);

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
        $file->shouldReceive('content')->andReturn('contents');

        $storage = new LaravelStorage($files, true, 'http://testing');

        $stored = $storage->store($file, 'relative/path/test.txt');

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertEquals('test.txt', $stored->name());
        static::assertEquals('contents', $stored->content());
    }

    /**
     * @test
     * @expectedException \Czim\FileHandling\Exceptions\FileStorageException
     */
    function it_throws_an_exception_if_it_fails_to_store_a_file()
    {
        $files = $this->getMockLaravelFilesystem();
        $files->shouldReceive('put')->once()->with('relative/path/test.txt', 'contents')->andReturn(false);

        $file = $this->getMockStorableFile();
        $file->shouldReceive('name')->andReturn('test.txt');
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
     * @return Mockery\MockInterface|\Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getMockLaravelFilesystem()
    {
        return Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
    }

    /**
     * @return Mockery\MockInterface|StorableFileInterface
     */
    protected function getMockStorableFile()
    {
        return Mockery::mock(StorableFileInterface::class);
    }

}
