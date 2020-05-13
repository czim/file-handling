<?php

namespace Czim\FileHandling\Contracts\Support;

interface RawContentInterface
{
    /**
     * Sets the content.
     *
     * @param string $content
     */
    public function setContent(string $content): void;

    /**
     * Returns full content.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Returns size of content.
     *
     * @return int
     */
    public function size(): int;

    /**
     * @param int $start
     * @param int $length
     * @return string|null
     */
    public function chunk(int $start = 0, int $length = 512): ?string;
}
