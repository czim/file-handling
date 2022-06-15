<?php

namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Exceptions\StorableFileCouldNotBeDeletedException;
use Czim\FileHandling\Storage\File\SplFileInfoStorableFile;
use Czim\FileHandling\Test\TestCase;
use org\bovigo\vfs\vfsStream;
use RuntimeException;
use SplFileInfo;
use UnexpectedValueException;

/**
 * Tests for abstract are in RawStorableFileTest.
 *
 * @uses \SplFileInfo
 */
class SplFileInfoStorableFileTest extends TestCase
{
    protected const XML_TEST_FILE = 'tests/resources/test.xml';

    /**
     * @test
     */
    function it_can_set_and_retrieve_content_data()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        static::assertEquals(file_get_contents($fileInfo->getRealPath()), $file->content());
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_non_fileinfo_data_is_given()
    {
        $this->expectException(UnexpectedValueException::class);

        $file = new SplFileInfoStorableFile();

        $file->setData('not a fileinfo instance');
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_the_referenced_path_is_not_found()
    {
        $this->expectException(RuntimeException::class);

        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo('/no/file/exists/here');

        $file->setData($fileInfo);
    }

    /**
     * @test
     */
    function it_returns_content_size_when_set()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        static::assertEquals($fileInfo->getSize(), $file->size());
    }

    /**
     * @test
     */
    function it_returns_the_path()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        static::assertEquals($fileInfo->getRealPath(), $file->path());
    }

    /**
     * @test
     */
    function it_opens_a_stream_for_reading()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        $resource = $file->openStream();

        static::assertIsResource($resource);
        static::assertEquals(file_get_contents($fileInfo->getRealPath()), fread($resource, 4096));

        fclose($resource);
    }

    /**
     * @test
     */
    function it_closes_an_open_stream()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        $resource = $file->openStream();

        static::assertIsResource($resource);

        $file->closeStream($resource);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    function it_silently_ignores_null_when_closing_a_stream()
    {
        $file = new SplFileInfoStorableFile();

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        $file->setData($fileInfo);

        $file->closeStream(null);
    }

    /**
     * @test
     */
    function it_creates_a_copy()
    {
        $root = vfsStream::setup('tmp');

        $file = new SplFileInfoStorableFile();
        $file->setData(new SplFileInfo($this->getExampleLocalPath()));

        static::assertTrue($file->copy(vfsStream::url('tmp/copy.txt')));

        static::assertTrue($root->hasChild('copy.txt'));
        static::assertEquals(
            file_get_contents($this->getExampleLocalPath()),
            $root->getChild('tmp/copy.txt')->getContent()
        );
    }

    /**
     * @test
     */
    function it_deletes_its_file()
    {
        // We cannot mock this with vfs, since the getRealPath() method on SplFileInfo is used.
        $deletablePath = $this->getDeletableLocalPath();

        copy($this->getExampleLocalPath(), $deletablePath);

        static::assertTrue(file_exists($deletablePath), 'Deletable file setup failed');


        $file = new SplFileInfoStorableFile();
        $file->setData(new SplFileInfo($this->getDeletableLocalPath()));

        $file->delete();

        static::assertFalse(file_exists($deletablePath), 'File was not deleted');
    }

    /**
     * @test
     */
    function it_throws_an_exception_attempting_to_delete_a_nonexistent_path()
    {
        // We cannot mock this with vfs, since the getRealPath() method on SplFileInfo is used.
        $deletablePath = $this->getDeletableLocalPath();

        copy($this->getExampleLocalPath(), $deletablePath);

        static::assertTrue(file_exists($deletablePath), 'Deletable file setup failed');

        $file = new SplFileInfoStorableFile();
        $file->setData(new SplFileInfo($this->getDeletableLocalPath()));

        // Delete the file so the delete call fails.
        unlink($deletablePath);

        $this->expectException(StorableFileCouldNotBeDeletedException::class);

        $file->delete();
    }


    protected function getExampleLocalPath(): string
    {
        return realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);
    }

    protected function getDeletableLocalPath(): string
    {
        return realpath(dirname(__DIR__) . '/../../../') . 'deletable.txt';
    }
}
