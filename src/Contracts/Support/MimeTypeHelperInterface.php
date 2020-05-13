<?php

namespace Czim\FileHandling\Contracts\Support;

interface MimeTypeHelperInterface
{
    /**
     * Returns the mime type for a given local path.
     *
     * @param string $path
     * @return string
     */
    public function guessMimeTypeForPath(string $path): string;

    /**
     * Returns the mime type for given raw file content.
     *
     * @param string $content
     * @return string
     */
    public function guessMimeTypeForContent(string $content): string;

    /**
     * Returns extension for file contents at a given local path.
     *
     * Does not include the '.'
     *
     * @param string $path
     * @return string
     */
    public function guessExtensionForPath(string $path): string;

    /**
     * Returns extension for a given mime type.
     *
     * Does not include the '.'
     *
     * @param string $type
     * @return string
     */
    public function guessExtensionForMimeType(string $type): string;
}
