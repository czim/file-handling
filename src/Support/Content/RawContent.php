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


    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }


    /**
     * Sets the content.
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns full content.
     *
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Returns size of content.
     *
     * @return int
     */
    public function size()
    {
        return strlen($this->content);
    }

    /**
     * @param int $start
     * @param int $length
     * @return bool|string
     */
    public function chunk($start = 0, $length = 512)
    {
        return substr($this->content, $start, max(1, $length));
    }

}
