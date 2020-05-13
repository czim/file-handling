<?php

namespace Czim\FileHandling\Contracts\Support;

interface UrlDownloaderInterface
{
    /**
     * Downloads from a URL and returns locally stored temporary file.
     *
     * @param string $url
     * @return string
     */
    public function download(string $url): string;
}
