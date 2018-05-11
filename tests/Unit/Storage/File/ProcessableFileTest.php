<?php
namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Storage\File\ProcessableFile;
use Czim\FileHandling\Test\TestCase;
use org\bovigo\vfs\vfsStream;
use SplFileInfo;

class ProcessableFileTest extends TestCase
{
    const XML_TEST_FILE = 'tests/resources/test.xml';

    /**
     * @test
     */
    function it_can_set_and_retrieve_content_data()
    {
        $file = new ProcessableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals(file_get_contents($fileInfo->getRealPath()), $file->content());
    }

    /**
     * @test
     */
    function it_can_set_data_as_a_path_string()
    {
        $file = new ProcessableFile;

        $path = $this->getExampleLocalPath();

        static::assertSame($file, $file->setData($path));

        static::assertEquals(file_get_contents($path), $file->content());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function it_throws_an_exception_if_the_referenced_path_is_not_found()
    {
        $file = new ProcessableFile;

        $fileInfo = new SplFileInfo('/no/file/exists/here');

        $file->setData($fileInfo);
    }

    /**
     * @test
     */
    function it_returns_content_size_when_set()
    {
        $file = new ProcessableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals($fileInfo->getSize(), $file->size());
    }
    
    /**
     * @test
     */
    function it_returns_the_path()
    {
        $file = new ProcessableFile;

        $fileInfo = new SplFileInfo($this->getExampleLocalPath());

        static::assertSame($file, $file->setData($fileInfo));

        static::assertEquals($fileInfo->getRealPath(), $file->path());
    }

    /**
     * @test
     */
    function it_creates_a_copy()
    {
        $root = vfsStream::setup('tmp');

        $file = new ProcessableFile();
        $file->setData(new SplFileInfo($this->getExampleLocalPath()));

        static::assertTrue($file->copy(vfsStream::url('tmp/copy.txt')));

        static::assertTrue($root->hasChild('copy.txt'));
        static::assertEquals(
            file_get_contents($this->getExampleLocalPath()),
            $root->getChild('tmp/copy.txt')->getContent()
        );
    }

    /**
     * @return string
     */
    protected function getExampleLocalPath()
    {
        return realpath(dirname(__DIR__) . '/../../../' . static::XML_TEST_FILE);
    }

}
