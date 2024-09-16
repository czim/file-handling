<?php

namespace Czim\FileHandling\Contracts\Support;

interface UriValidatorInterface
{
    public function isValid(string $url): bool;
}
