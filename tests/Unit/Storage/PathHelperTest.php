<?php
namespace Czim\FileHandling\Test\Unit\Storage;

use Czim\FileHandling\Storage\PathHelper;
use Czim\FileHandling\Test\TestCase;

class PathHelperTest extends TestCase
{

    /**
     * @test
     */
    function it_adds_a_variant_to_a_base_path()
    {
        $helper = new PathHelper;

        static::assertEquals('test/target/path', $helper->addVariantToBasePath('test/target', 'path'));
        static::assertEquals('test/target/path', $helper->addVariantToBasePath('test/target/', '/path/'));
    }

    /**
     * @test
     */
    function it_replaces_a_variant_for_a_full_variant_path()
    {
        $helper = new PathHelper;

        static::assertEquals('test/target/path', $helper->replaceVariantInPath('test/target/old', 'path'));
        static::assertEquals('test/target/path', $helper->replaceVariantInPath('test/target/old/', '/path/'));
    }

    /**
     * @test
     */
    function it_returns_a_base_path_for_a_full_variant_path()
    {
        $helper = new PathHelper;

        static::assertEquals('test/target', $helper->basePath('test/target/path'));
        static::assertEquals('test/target', $helper->basePath('test/target/path/'));
    }

    /**
     * @test
     */
    function it_returns_empty_string_as_basepath_for_a_segmentless_path()
    {
        $helper = new PathHelper;

        static::assertEquals('', $helper->basePath('no-segments'));
    }

}
