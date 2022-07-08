<?php

namespace Czim\FileHandling\Test\Unit\Handler;

use Czim\FileHandling\Contracts\Handler\ProcessResultInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorageInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Contracts\Storage\TargetInterface;
use Czim\FileHandling\Contracts\Variant\VariantProcessorInterface;
use Czim\FileHandling\Handler\FileHandler;
use Czim\FileHandling\Test\TestCase;
use Mockery;

class FileHandlerTest extends TestCase
{

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    function it_processes_a_storable_file_without_variants()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn('test/target/path/original/file.txt');

        $storedMock = $this->getMockStoredFile();
        $storage->shouldReceive('store')
            ->with(Mockery::type(StorableFileInterface::class), 'test/target/path/original/file.txt')
            ->once()
            ->andReturn($storedMock);

        $handler = new FileHandler($storage, $processor);

        $file = $this->getMockStorableFile();

        $result = $handler->process($file, $target, ['test' => true]);

        static::assertInstanceOf(ProcessResultInterface::class, $result);
        static::assertCount(1, $result->storedFiles());
        static::assertArrayHasKey(FileHandler::ORIGINAL, $result->storedFiles());

        $stored = $result->storedFiles()[FileHandler::ORIGINAL];

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertSame($storedMock, $stored);
    }

    /**
     * @test
     */
    function it_processes_a_storable_file_with_variants()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $file           = $this->getMockStorableFile();
        $storedMock     = $this->getMockStoredFile();
        $storedTinyMock = $this->getMockStoredFile();
        $temporaryMock  = $this->getMockStoredFile();

        $tinyVariantConfig = [
            'resize'     => ['dimensions' => '10x10'],
            'autoorient' => [],
        ];

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn('test/target/path/original/file.txt');
        $target->shouldReceive('variant')
            ->once()
            ->andReturn('test/target/path/tiny/file.txt');

        $processor->shouldReceive('process')
            ->with($file, 'tiny', $tinyVariantConfig)
            ->once()
            ->andReturn($storedTinyMock);

        $processor->shouldReceive('clearTemporaryFiles');
        $processor->shouldReceive('getTemporaryFiles')->andReturn([$temporaryMock]);

        $storage->shouldReceive('store')
            ->with(Mockery::type(StorableFileInterface::class), 'test/target/path/original/file.txt')
            ->once()
            ->andReturn($storedMock);

        $storage->shouldReceive('store')
            ->with(Mockery::type(StorableFileInterface::class), 'test/target/path/tiny/file.txt')
            ->once()
            ->andReturn($storedTinyMock);

        $handler = new FileHandler($storage, $processor);

        $result = $handler->process($file, $target, [
            FileHandler::CONFIG_VARIANTS => [
                'tiny' => $tinyVariantConfig,
            ],
        ]);

        static::assertInstanceOf(ProcessResultInterface::class, $result);

        $stored = $result->storedFiles();

        static::assertCount(2, $stored);
        static::assertArrayHasKey(FileHandler::ORIGINAL, $stored);
        static::assertArrayHasKey('tiny', $stored);

        static::assertCount(2, $stored);

        static::assertInstanceOf(StoredFileInterface::class, $stored[ FileHandler::ORIGINAL ]);
        static::assertSame($storedMock, $stored[ FileHandler::ORIGINAL ]);
        static::assertInstanceOf(StoredFileInterface::class, $stored['tiny']);
        static::assertSame($storedTinyMock, $stored['tiny']);

        static::assertCount(1, $result->temporaryFiles());
        static::assertSame($temporaryMock, $result->temporaryFiles()[0]);
    }

    /**
     * @test
     */
    function it_processes_a_single_file_variant()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $file       = $this->getMockStorableFile();
        $storedMock = $this->getMockStoredFile();

        $tinyVariantConfig = [
            'resize' => ['dimensions' => '10x10'],
        ];

        $target->shouldReceive('variant')
            ->once()
            ->andReturn('test/target/path/tiny/file.txt');

        $processor->shouldReceive('process')
            ->with($file, 'tiny', $tinyVariantConfig)
            ->once()
            ->andReturn($storedMock);

        $processor->shouldReceive('clearTemporaryFiles');
        $processor->shouldReceive('getTemporaryFiles')->andReturn([]);

        $storage->shouldReceive('store')
            ->with(Mockery::type(StorableFileInterface::class), 'test/target/path/tiny/file.txt')
            ->once()
            ->andReturn($storedMock);

        $handler = new FileHandler($storage, $processor);

        $result = $handler->processVariant($file, $target, 'tiny', $tinyVariantConfig);

        static::assertCount(1, $result->storedFiles());
        static::assertArrayHasKey('tiny', $result->storedFiles());

        $stored = $result->storedFiles()['tiny'];

        static::assertInstanceOf(StoredFileInterface::class, $stored);
        static::assertSame($storedMock, $stored);
    }

    /**
     * @test
     */
    function it_returns_variant_urls_for_a_target_and_list_of_variants()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn('test/target/path/original/test.gif');
        $target->shouldReceive('variant')
            ->with('tiny')
            ->once()
            ->andReturn('test/target/path/tiny/test.gif');

        $storage->shouldReceive('url')
            ->with('test/target/path/tiny/test.gif')
            ->andReturn('http://test.com/test/target/path/tiny/test.gif');

        $storage->shouldReceive('url')
            ->with('test/target/path/original/test.gif')
            ->andReturn('http://test.com/test/target/path/original/test.gif');

        $handler = new FileHandler($storage, $processor);

        $urls = $handler->variantUrlsForTarget($target, ['tiny', FileHandler::ORIGINAL]);

        static::assertEquals([
            FileHandler::ORIGINAL => 'http://test.com/test/target/path/original/test.gif',
            'tiny'                => 'http://test.com/test/target/path/tiny/test.gif',
        ], $urls);
    }

    /**
     * @test
     */
    function it_deletes_variants_and_the_original_for_a_file()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn('test/target/path/original/test.gif');
        $target->shouldReceive('variant')
            ->once()
            ->andReturn('test/target/path/tiny/test.gif');

        $storage->shouldReceive('exists')
            ->with('test/target/path/tiny/test.gif')
            ->once()
            ->andReturn(false);

        $storage->shouldReceive('exists')
            ->with('test/target/path/original/test.gif')
            ->once()
            ->andReturn(true);

        $storage->shouldReceive('delete')
            ->with('test/target/path/original/test.gif')
            ->once()
            ->andReturn(true);

        $handler = new FileHandler($storage, $processor);

        static::assertTrue($handler->delete($target, ['tiny']));
    }

    /**
     * @test
     */
    function it_deletes_a_single_variant()
    {
        $storage   = $this->getMockStorage();
        $processor = $this->getMockVariantProcessor();
        $target    = $this->getMockTarget();

        $target->shouldReceive('variant')
            ->once()
            ->andReturn('test/target/path/tiny/test.gif');

        $storage->shouldReceive('exists')
            ->with('test/target/path/tiny/test.gif')
            ->once()
            ->andReturn(true);

        $storage->shouldReceive('delete')
            ->with('test/target/path/tiny/test.gif')
            ->once()
            ->andReturn(true);

        $handler = new FileHandler($storage, $processor);

        static::assertTrue($handler->deleteVariant($target, 'tiny'));
    }

    /**
     * @test
     */
    function it_handles_filenames_with_invalid_characters()
    {
        $storage     = $this->getMockStorage();
        $processor   = $this->getMockVariantProcessor();
        $target      = $this->getMockTarget();
        $invalidPath = 'test/target/path/original/space another space testëïóöü€é.mp4';
        $cleanPath   = 'test/target/path/original/space%20another%20space%20test%C3%AB%C3%AF%C3%B3%C3%B6%C3%BC%E2%82%AC%C3%A9.mp4';

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn($invalidPath);

        $storage->shouldReceive('url')
            ->with($invalidPath)
            ->andReturn('http://test.com/' . $invalidPath);

        $handler = new FileHandler($storage, $processor);

        $urls = $handler->variantUrlsForTarget($target, [FileHandler::ORIGINAL]);

        static::assertEquals('http://test.com/' . $cleanPath, $urls[FileHandler::ORIGINAL]);

        $target->shouldReceive(FileHandler::ORIGINAL)
            ->once()
            ->andReturn($cleanPath);

        $storage->shouldReceive('url')
            ->with($cleanPath)
            ->andReturn('http://test.com/' . $cleanPath);

        $handler = new FileHandler($storage, $processor);

        $urls = $handler->variantUrlsForTarget($target, [FileHandler::ORIGINAL]);

        static::assertEquals('http://test.com/' . $cleanPath, $urls[FileHandler::ORIGINAL]);
    }


    /**
     * @return Mockery\Mock|Mockery\MockInterface|StorageInterface
     */
    protected function getMockStorage()
    {
        return Mockery::mock(StorageInterface::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|VariantProcessorInterface
     */
    protected function getMockVariantProcessor()
    {
        return Mockery::mock(VariantProcessorInterface::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|TargetInterface
     */
    protected function getMockTarget()
    {
        return Mockery::mock(TargetInterface::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|StorableFileInterface
     */
    protected function getMockStorableFile()
    {
        return Mockery::mock(StorableFileInterface::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|StoredFileInterface
     */
    protected function getMockStoredFile()
    {
        return Mockery::mock(StoredFileInterface::class);
    }

}
