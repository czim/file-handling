<?php

namespace Czim\FileHandling\Contracts\Support;

interface ContentInterpreterInterface
{
    /**
     * Returns which type the given content is deemed to be.
     *
     * @param RawContentInterface $content
     * @return string
     */
    public function interpret(RawContentInterface $content): string;
}
