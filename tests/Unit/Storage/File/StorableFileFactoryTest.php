<?php

namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Contracts\Support\ContentInterpreterInterface;
use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use Czim\FileHandling\Contracts\Support\RawContentInterface;
use Czim\FileHandling\Contracts\Support\UrlDownloaderInterface;
use Czim\FileHandling\Enums\ContentTypes;
use Czim\FileHandling\Exceptions\CouldNotReadDataException;
use Czim\FileHandling\Exceptions\CouldNotRetrieveRemoteFileException;
use Czim\FileHandling\Storage\File\RawStorableFile;
use Czim\FileHandling\Storage\File\SplFileInfoStorableFile;
use Czim\FileHandling\Storage\File\StorableFileFactory;
use Czim\FileHandling\Support\Content\RawContent;
use Czim\FileHandling\Test\TestCase;
use ErrorException;
use Mockery;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UnexpectedValueException;

/**
 * Class StorableFileFactoryTest
 *
 * @uses \SplFileInfo
 * @uses \Czim\FileHandling\Support\Content\RawContent
 * @uses \Czim\FileHandling\Storage\File\RawStorableFile
 *
 */
class StorableFileFactoryTest extends TestCase
{
    protected const XML_TEST_FILE = 'tests/resources/test.xml';


    /**
     * @test
     */
    function it_returns_a_storable_file_intance_as_is()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $file = new RawStorableFile();
        $file->setData('random content');
        $file->setMimeType('text/plain');
        $file->setName('test.txt');

        $output = $factory->makeFromAny($file);

        static::assertSame($file, $output);
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_from_an_uploaded_file_instance()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $testPath = realpath(__DIR__ . '/../../../resources/test.txt');

        $upload = new UploadedFile($testPath, 'some_original_name.txt', 'text/plain');

        $file = $factory->makeFromAny($upload);

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('some_original_name.txt', $file->name());
        static::assertEquals(8, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_from_spl_file_info()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $info = new SplFileInfo($this->getExampleLocalPath());

        $file = $factory->makeFromFileInfo($info);

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('test.xml', $file->name());
        static::assertEquals(766, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_with_custom_name_and_mimetype()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $info = new SplFileInfo($this->getExampleLocalPath());

        $file = $factory->makeFromFileInfo($info, 'other_name.xml', 'image/gif');

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('other_name.xml', $file->name());
        static::assertEquals('image/gif', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_from_local_path()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $path = realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);

        $file = $factory->makeFromLocalPath($path);

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('test.xml', $file->name());
        static::assertEquals(766, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_from_a_url()
    {
        $downloader = $this->getMockDownloader();

        $path = realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);

        $downloader->shouldReceive('download')
            ->with('http://test.com/test.xml?page=23')
            ->andReturn($path);

        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $downloader);

        $file = $factory->makeFromUrl('http://test.com/test.xml?page=23');

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('test.xml', $file->name());
        static::assertEquals(766, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_url_could_not_be_downloaded_from()
    {
        $this->expectException(CouldNotRetrieveRemoteFileException::class);

        $downloader = $this->getMockDownloader();

        $downloader->shouldReceive('download')
            ->with('http://test.com/test.xml')
            ->andThrow(new ErrorException('testing'));

        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $downloader);

        $factory->makeFromUrl('http://test.com/test.xml');
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_from_a_datauri()
    {
        $mimeTypeHelper = $this->getMockMimeTypeHelper();
        $mimeTypeHelper->shouldReceive('guessExtensionForMimeType')->with('image/gif')->andReturn('gif');

        $factory = new StorableFileFactory($mimeTypeHelper, $this->getMockInterpreter(), $this->getMockDownloader());

        $rawData = $this->getExampleDataUri();

        $file = $factory->makeFromDataUri($rawData, 'helpful.gif');

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertEquals('helpful.gif', $file->name());
        static::assertEquals(37, $file->size());
        static::assertEquals('application/xml', $file->mimeType());

        // And from a raw content instance
        $file = $factory->makeFromDataUri(new RawContent($rawData));

        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);
        static::assertMatchesRegularExpression('#[a-z0-9]{16}\.gif#', $file->name());
        static::assertEquals(37, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_a_storable_file_instance_from_raw_data()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $rawData = $this->getExampleRawData();

        $file = $factory->makeFromRawData($rawData, 'testing.xml');

        static::assertInstanceOf(RawStorableFile::class, $file);
        static::assertEquals('testing.xml', $file->name());
        static::assertEquals(766, $file->size());
        static::assertEquals('application/xml', $file->mimeType());

        // And from a raw content instance
        $file = $factory->makeFromRawData(new RawContent($rawData), 'testing.xml');

        static::assertInstanceOf(RawStorableFile::class, $file);
        static::assertEquals('testing.xml', $file->name());
        static::assertEquals(766, $file->size());
        static::assertEquals('application/xml', $file->mimeType());
    }

    /**
     * @test
     */
    function it_makes_storable_file_instances_for_interpreted_source_data()
    {
        $mimeGuesser = $this->getMockMimeTypeHelper();
        $interpreter = $this->getMockInterpreter();
        $downloader  = $this->getMockDownloader();

        // Data URI expectation
        $interpreter->shouldReceive('interpret')
            ->with(Mockery::on(function ($argument) {
                return $argument instanceof RawContentInterface && $argument->chunk(0, 5) == 'data:';
            }))
            ->andReturn(ContentTypes::DATAURI);

        // URI expectation
        $interpreter->shouldReceive('interpret')
            ->with(Mockery::on(function ($argument) {
                return $argument instanceof RawContentInterface && $argument->chunk(0, 5) == 'http:';
            }))
            ->andReturn(ContentTypes::URI);

        // Raw data expectation
        $interpreter->shouldReceive('interpret')
            ->with(Mockery::on(function ($argument) {
                return $argument instanceof RawContentInterface && $argument->chunk(0, 5) == 'some ';
            }))
            ->andReturn(ContentTypes::RAW);

        // Expectations for download logic
        $downloader->shouldReceive('download')
            ->with('http://yourfilehere.com')
            ->andReturn($this->getExampleLocalPath());

        $mimeGuesser->shouldReceive('guessExtensionForMimeType')->with('image/gif')->andReturn('gif');
        $mimeGuesser->shouldReceive('guessExtensionForMimeType')->with('application/xml')->andReturn('xml');


        $factory = new StorableFileFactory($mimeGuesser, $interpreter, $downloader);

        $file = $factory->makeFromAny(new SplFileInfo($this->getExampleLocalPath()));
        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);

        $file = $factory->makeFromAny('http://yourfilehere.com');
        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);

        $file = $factory->makeFromAny($this->getExampleDataUri(), 'helpful.gif');
        static::assertInstanceOf(SplFileInfoStorableFile::class, $file);

        $file = $factory->makeFromAny($this->getExampleSimpleRawData());
        static::assertInstanceOf(RawStorableFile::class, $file);
    }

    /**
     * @test
     */
    function it_marks_a_file_uploaded_with_fluent_syntax()
    {
        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $info = new SplFileInfo($this->getExampleLocalPath());

        $file = $factory->makeFromFileInfo($info);
        static::assertFalse($file->isUploaded());

        $file = $factory->uploaded()->makeFromFileInfo($info);
        static::assertTrue($file->isUploaded());

        $file = $factory->makeFromFileInfo($info);
        static::assertFalse($file->isUploaded(), 'Should not be uploaded for next call');
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_any_data_is_not_a_string()
    {
        $this->expectException(UnexpectedValueException::class);

        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $factory->makeFromAny(['not', 'a string']);
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_data_uri_cannot_be_interpreted()
    {
        $this->expectException(CouldNotReadDataException::class);

        $factory = new StorableFileFactory($this->getMockMimeTypeHelper(), $this->getMockInterpreter(), $this->getMockDownloader());

        $factory->makeFromDataUri('_data://invalid/mimetype,base32brokencontent', 'name.txt');
    }


    /**
     * @return Mockery\Mock|Mockery\MockInterface|MimeTypeHelperInterface
     */
    protected function getMockMimeTypeHelper()
    {
        /** @var Mockery\Mock|Mockery\MockInterface|MimeTypeHelperInterface $mock */
        $mock = Mockery::mock(MimeTypeHelperInterface::class);

        $mock->shouldReceive('guessMimeTypeForPath')->andReturn('application/xml');
        $mock->shouldReceive('guessMimeTypeForContent')->andReturn('application/xml');

        return $mock;
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|ContentInterpreterInterface
     */
    protected function getMockInterpreter()
    {
        return Mockery::mock(ContentInterpreterInterface::class);
    }

    /**
     * @return Mockery\Mock|Mockery\MockInterface|UrlDownloaderInterface
     */
    protected function getMockDownloader()
    {
        return Mockery::mock(UrlDownloaderInterface::class);
    }

    protected function getExampleLocalPath(): string
    {
        return realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);
    }

    protected function getExampleDataUri(): string
    {
        return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    }

    protected function getExampleRawData(): string
    {
        return file_get_contents($this->getExampleLocalPath());
    }

    protected function getExampleSimpleRawData(): string
    {
        return 'some raw data that is just a line of text';
    }

}
