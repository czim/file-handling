<?php

namespace Czim\FileHandling\Test\Unit\Storage\Path;

use Czim\FileHandling\Storage\Path\Target;
use Czim\FileHandling\Test\TestCase;

class TargetTest extends TestCase
{
    /**
     * @test
     */
    function it_instantiates_with_an_original_path()
    {
        $target = new Target('tmp/testing/original/file.txt');

        static::assertEquals('tmp/testing/original/file.txt', $target->original());
    }

    /**
     * @test
     */
    function it_bases_variant_path_on_original_path_if_none_is_set()
    {
        $target = new Target('tmp/testing/original/file.txt');

        static::assertEquals('tmp/testing/alternative/file.txt', $target->variant('alternative'));

        // Edge-case for a path with just one segment:

        $target = new Target('original/file.txt');

        static::assertEquals('alternative/file.txt', $target->variant('alternative'));
    }

    /**
     * @test
     */
    function it_uses_variant_path_with_placeholder_if_it_is_set()
    {
        $target = new Target('tmp/testing/original/file.txt', 'tmp/:variant/different.txt');

        static::assertEquals('tmp/alternative/different.txt', $target->variant('alternative'));
    }

    /**
     * @test
     */
    function it_uses_an_alternative_filename_for_a_variant_if_set()
    {
        $target = new Target('tmp/testing/original/file.txt');

        $target->setVariantFilename('test', 'alternative');

        static::assertEquals('tmp/testing/test/alternative.txt', $target->variant('test'));
    }

    /**
     * @test
     */
    function it_uses_an_alternative_extension_for_a_variant_if_set()
    {
        $target = new Target('tmp/testing/original/file.txt');

        $target->setVariantExtension('test', 'me');

        static::assertEquals('tmp/testing/test/file.me', $target->variant('test'));
    }

    /**
     * @test
     */
    function it_combines_an_alternative_name_with_an_extension_if_both_are_set_for_a_variant()
    {
        $target = new Target('tmp/testing/original/file.txt');

        $target->setVariantFilename('test', 'alternative');
        $target->setVariantExtension('test', 'me');

        static::assertEquals('tmp/testing/test/alternative.me', $target->variant('test'));
    }
}
