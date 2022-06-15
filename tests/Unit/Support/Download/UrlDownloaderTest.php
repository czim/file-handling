<?php

namespace Czim\FileHandling\Test\Unit\Support\Download;

use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use Czim\FileHandling\Exceptions\CouldNotRetrieveRemoteFileException;
use Czim\FileHandling\Support\Download\UrlDownloader;
use Czim\FileHandling\Test\TestCase;
use Exception;
use Mockery;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class UrlDownloaderTest extends TestCase
{

    /**
     * @var vfsStreamDirectory
     */
    protected $vfsRoot;

    public function setUp(): void
    {
        parent::setUp();

        $this->vfsRoot = vfsStream::setup('tmp');
    }

    /**
     * @test
     */
    function it_downloads_a_file_from_a_url()
    {
        /** @var UrlDownloader|Mockery\MockInterface|Mockery\Mock $downloader */
        $downloader = Mockery::mock(
            UrlDownloader::class . '[downloadToTempLocalPath,makeLocalTemporaryPath]',
            [$this->getMockHelper()]
        )
            ->shouldAllowMockingProtectedMethods();

        $downloader->shouldReceive('makeLocalTemporaryPath')->andReturn('tmp/test.txt');

        $downloader->shouldReceive('downloadToTempLocalPath')
            ->with('http://www.something.com/file.txt?page=1', 'tmp/test.txt')
            ->andReturn('tmp/local');

        $path = $downloader->download('http://www.something.com/file.txt?page=1');

        static::assertEquals('tmp/test.txt', $path);
    }

    /**
     * @test
     */
    function it_downloads_a_file_from_a_url_and_adds_an_extension_if_omitted()
    {
        $helper = $this->getMockHelper();

        /** @var UrlDownloader|Mockery\MockInterface|Mockery\Mock $downloader */
        $downloader = Mockery::mock(
            UrlDownloader::class . '[downloadToTempLocalPath,makeLocalTemporaryPath]',
            [$helper]
        )
            ->shouldAllowMockingProtectedMethods();

        // Prepare file mocking
        vfsStream::newFile('file')->at($this->vfsRoot)->setContent('dummy contents');
        $tmpPath = $this->vfsRoot->url() . '/file';

        $downloader->shouldReceive('makeLocalTemporaryPath')->andReturn($tmpPath);

        $downloader->shouldReceive('downloadToTempLocalPath')
            ->with('http://www.something.com/file', $tmpPath)
            ->andReturn('tmp/local');

        $helper->shouldReceive('guessExtensionForPath')->andReturn('txt');

        $path = $downloader->download('http://www.something.com/file');

        static::assertEquals($this->vfsRoot->url() . '/file.txt', $path);
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_it_cannot_guess_an_extension_for_an_extensionless_file()
    {
        $this->expectException(CouldNotRetrieveRemoteFileException::class);

        $helper = $this->getMockHelper();

        /** @var UrlDownloader|Mockery\MockInterface|Mockery\Mock $downloader */
        $downloader = Mockery::mock(
            UrlDownloader::class . '[downloadToTempLocalPath,makeLocalTemporaryPath]',
            [$helper]
        )
            ->shouldAllowMockingProtectedMethods();

        // Prepare file mocking
        vfsStream::newFile('file')->at($this->vfsRoot)->setContent('dummy contents');
        $tmpPath = $this->vfsRoot->url() . '/file';

        $downloader->shouldReceive('makeLocalTemporaryPath')->andReturn($tmpPath);

        $downloader->shouldReceive('downloadToTempLocalPath')
            ->with('http://www.something.com/file', $tmpPath)
            ->andReturn('tmp/local');

        $helper->shouldReceive('guessExtensionForPath')->andThrow(new Exception('Failed to guess'));

        $downloader->download('http://www.something.com/file');
    }


    /**
     * @return Mockery\Mock|Mockery\MockInterface|MimeTypeHelperInterface
     */
    protected function getMockHelper()
    {
        return Mockery::mock(MimeTypeHelperInterface::class);
    }

}
