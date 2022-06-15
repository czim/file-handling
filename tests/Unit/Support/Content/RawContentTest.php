<?php

namespace Czim\FileHandling\Test\Unit\Support\Content;

use Czim\FileHandling\Support\Content\RawContent;
use Czim\FileHandling\Test\TestCase;

class RawContentTest extends TestCase
{

    /**
     * @test
     */
    function it_can_be_constructed_with_raw_data()
    {
        $content = new RawContent('testing data');

        static::assertEquals('testing data', $content->content());
    }

    /**
     * @test
     */
    function it_can_take_new_content()
    {
        $content = new RawContent('older data');

        $content->setContent('testing data');

        static::assertEquals('testing data', $content->content());
    }

    /**
     * @test
     */
    function it_returns_content_size()
    {
        $content = new RawContent('testing data');

        static::assertEquals(12, $content->size());
    }

    /**
     * @test
     */
    function it_returns_a_chunk_of_content()
    {
        $content = new RawContent('testing data');

        static::assertEquals('test', $content->chunk(0, 4));
        static::assertEquals('ing', $content->chunk(4, 3));
    }

}
