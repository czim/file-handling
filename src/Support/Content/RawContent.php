<?php

namespace Czim\FileHandling\Support\Content;

use Czim\FileHandling\Contracts\Support\RawContentInterface;

/**
 * Class RawContent
 *
 * Wrapper for raw file content to allow for by-reference
 * passing of potentially large data
 */
class RawContent implements RawContentInterface
{
    /**
     * @var string
     */
    protected $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }


    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function size(): int
    {
        return strlen($this->content);
    }

    /**
     * @param int $start
     * @param int $length
     * @return string|null
     */
    public function chunk(int $start = 0, int $length = 512): ?string
    {
        $chunk = substr($this->content, $start, max(1, $length));
        return $chunk === false ? null : $chunk;
    }
}
