<?php
namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Storage\File\SplFileInfoStorableFile;
use Czim\FileHandling\Test\TestCase;
use SplFileInfo;

/**
 * Class SplFileInfoStorableFileTest
 *
 * Tests for abstract are in RawStorableFileTest.
 *
 * @uses \SplFileInfo
 */
class SplFileInfoStorableFileTest extends TestCase
{
    const XML_TEST_FILE = 'tests/resources/test.xml';

    /**
     * @test
     */
    function it_can_set_and_retrieve_content_data()
    {
        $file = new SplFileInfoStorableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals(file_get_contents($fileInfo->getRealPath()), $file->content());
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    function it_throws_an_exception_if_non_fileinfo_data_is_given()
    {
        $file = new SplFileInfoStorableFile;

        $file->setData('not a fileinfo instance');
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function it_throws_an_exception_if_the_referenced_path_is_not_found()
    {
        $file = new SplFileInfoStorableFile;

        $fileInfo = new SplFileInfo('/no/file/exists/here');

        $file->setData($fileInfo);
    }

    /**
     * @test
     */
    function it_returns_content_size_when_set()
    {
        $file = new SplFileInfoStorableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals($fileInfo->getSize(), $file->size());
    }
    
    /**
     * @test
     */
    function it_returns_the_path()
    {
        $file = new SplFileInfoStorableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals($fileInfo->getRealPath(), $file->path());
    }

    /**
     * @return string
     */
    protected function getExampleLocalPath()
    {
        return realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);
    }

}
