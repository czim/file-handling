<?php

namespace Czim\FileHandling\Test\Unit\Storage\File;

use Czim\FileHandling\Exceptions\StorableFileCouldNotBeDeletedException;
use Czim\FileHandling\Storage\File\RawStorableFile;
use Czim\FileHandling\Test\TestCase;
use org\bovigo\vfs\vfsStream;
use UnexpectedValueException;

class RawStorableFileTest extends TestCase
{
    // ------------------------------------------------------------------------------
    //      Abstract
    // ------------------------------------------------------------------------------

    /**
     * @test
     */
    function it_can_set_and_retrieve_the_mime_type()
    {
        $file = new RawStorableFile();

        $file->setMimeType('type');

        static::assertEquals('type', $file->mimeType());
    }

    /**
     * @test
     */
    function it_can_set_and_retrieve_the_name()
    {
        $file = new RawStorableFile();

        $file->setName('test.txt');

        static::assertEquals('test.txt', $file->name());
    }

    /**
     * @test
     */
    function it_can_be_marked_as_uploaded()
    {
        $file = new RawStorableFile();

        static::assertFalse($file->isUploaded());

        $file->setUploaded();

        static::assertTrue($file->isUploaded());

        $file->setUploaded(false);

        static::assertFalse($file->isUploaded());
    }

    /**
     * @test
     */
    function it_returns_the_extension_for_its_name()
    {
        $file = new RawStorableFile();

        static::assertNull($file->extension(), 'Should return null without a name set');

        $file->setName('test.txt');

        static::assertEquals('txt', $file->extension());
    }

    /**
     * @test
     */
    function it_returns_content_size_as_null_by_default()
    {
        $file = new RawStorableFile();

        static::assertNull($file->size());
    }

    /**
     * @test
     */
    function it_returns_null_for_path()
    {
        $file = new RawStorableFile();

        static::assertNull($file->path());
    }

    /**
     * @test
     */
    function it_is_not_streamable()
    {
        $file = new RawStorableFile();

        static::assertNull($file->openStream());
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    function it_silently_ignores_closing_a_stream()
    {
        $file = new RawStorableFile();

        $file->closeStream(null);
    }

    /**
     * @test
     */
    function it_creates_a_copy()
    {
        $root = vfsStream::setup('tmp');

        $file = new RawStorableFile();
        $file->setData('contents to be copied');

        static::assertTrue($file->copy(vfsStream::url('tmp/copy.txt')));
        static::assertTrue($root->hasChild('copy.txt'));
        static::assertEquals('contents to be copied', $root->getChild('tmp/copy.txt')->getContent());
    }

    /**
     * @test
     */
    function it_throws_an_exception_on_delete()
    {
        $file = new RawStorableFile();
        $file->setData('contents to be deleted');

        $this->expectException(StorableFileCouldNotBeDeletedException::class);

        $file->delete();
    }


    // ------------------------------------------------------------------------------
    //      Specific
    // ------------------------------------------------------------------------------

    /**
     * @test
     */
    function it_can_set_and_retrieve_content_data()
    {
        $file = new RawStorableFile();

        $file->setData('testing content');

        static::assertEquals('testing content', $file->content());

        // And it can be overwritten
        $file->setData('new content');

        static::assertEquals('new content', $file->content());
    }

    /**
     * @test
     */
    function it_throws_an_exception_if_non_string_data_is_given()
    {
        $this->expectException(UnexpectedValueException::class);

        $file = new RawStorableFile();

        $file->setData(['not', 'a string']);
    }

    /**
     * @test
     */
    function it_returns_content_size_when_set()
    {
        $file = new RawStorableFile();

        $file->setData('testing content');

        static::assertEquals(15, $file->size());
    }
}
