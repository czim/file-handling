<?php
namespace Czim\FileHandling\Contracts\Support;

interface RawContentInterface
{

    /**
     * Sets the content.
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Returns full content.
     *
     * @return string
     */
    public function content();

    /**
     * Returns size of content.
     *
     * @return int
     */
    public function size();

    /**
     * @param int $start
     * @param int $length
     * @return bool|string
     */
    public function chunk($start = 0, $length = 512);

}
