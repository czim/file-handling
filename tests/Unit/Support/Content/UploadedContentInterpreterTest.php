<?php

namespace Czim\FileHandling\Test\Unit\Support\Content;

use Czim\FileHandling\Enums\ContentTypes;
use Czim\FileHandling\Support\Content\RawContent;
use Czim\FileHandling\Support\Content\UploadedContentInterpreter;
use Czim\FileHandling\Test\TestCase;

/**
 * Class UploadedContentInterpreterTest
 *
 * @uses \Czim\FileHandling\Support\Content\RawContent
 */
class UploadedContentInterpreterTest extends TestCase
{
    protected const XML_TEST_FILE = 'tests/resources/test.xml';

    /**
     * @test
     */
    function it_returns_uri_for_uri_content()
    {
        $interpreter = new UploadedContentInterpreter();

        static::assertEquals(
            ContentTypes::URI,
            $interpreter->interpret(new RawContent('http://www.google.com'))
        );
    }

    /**
     * @test
     */
    function it_returns_datauri_for_datauri_content()
    {
        $interpreter = new UploadedContentInterpreter();

        static::assertEquals(
            ContentTypes::DATAURI,
            $interpreter->interpret(new RawContent($this->getExampleDataUri()))
        );
    }

    /**
     * @test
     */
    function it_returns_raw_for_non_url_non_datauri_content()
    {
        $interpreter = new UploadedContentInterpreter();

        static::assertEquals(
            ContentTypes::RAW,
            $interpreter->interpret(new RawContent($this->getExampleRawContent()))
        );
    }


    protected function getExampleDataUri(): string
    {
        return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    }

    protected function getExampleRawContent(): string
    {
        return 'Could be the contents of a text file, or something else, you never know.';
    }

}
