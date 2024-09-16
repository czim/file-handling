<?php

declare(strict_types=1);

namespace Unit\Support\Download;

use Czim\FileHandling\Support\Download\UriValidator;
use Czim\FileHandling\Test\TestCase;

class UriValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function it_considers_any_uri_invalid_by_default()
    {
        $validator = new UriValidator();

        static::assertFalse($validator->isValid('file://../local.file'));
        static::assertFalse($validator->isValid('http://anything.test'));
        static::assertFalse($validator->isValid('https://anything.test'));
        static::assertFalse($validator->isValid('ftp://anything.test'));
    }

    /**
     * @test
     */
    public function it_considers_any_remote_uri_valid_when_configured_to()
    {
        $validator = new UriValidator();

        $validator->allowRemoteUri();

        static::assertFalse($validator->isValid('file://../local.file'));
        static::assertTrue($validator->isValid('http://anything.test'));
        static::assertTrue($validator->isValid('https://anything.test'));
        static::assertTrue($validator->isValid('ftp://anything.test'));
    }

    /**
     * @test
     */
    public function it_considers_local_uri_valid_when_configured_to()
    {
        $validator = new UriValidator();

        $validator->allowLocalUri();

        static::assertTrue($validator->isValid('file://../local.file'));
        static::assertFalse($validator->isValid('http://anything.test'));
        static::assertFalse($validator->isValid('https://anything.test'));
        static::assertFalse($validator->isValid('ftp://anything.test'));
    }

    /**
     * @test
     */
    public function it_any_uri_valid_when_configured_to()
    {
        $validator = new UriValidator();

        $validator->allowRemoteUri();
        $validator->allowLocalUri();

        static::assertTrue($validator->isValid('file://../local.file'));
        static::assertTrue($validator->isValid('http://anything.test'));
        static::assertTrue($validator->isValid('https://anything.test'));
        static::assertTrue($validator->isValid('ftp://anything.test'));
    }
}
