<?php

namespace Czim\FileHandling\Support\Content;

use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use finfo;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Mime\MimeTypes;

/**
 * @codeCoverageIgnore
 */
class MimeTypeHelper implements MimeTypeHelperInterface
{

    /**
     * @var null|ExtensionGuesserInterface
     */
    protected static $mimeTypeExtensionGuesser;

    /**
     * Returns the mime type for a given local path.
     *
     * @param string $path
     * @return string
     */
    public function guessMimeTypeForPath(string $path): string
    {
        if (class_exists(MimeTypes::class)) {
            return (new MimeTypes())->guessMimeType($path);
        }

        // Deprecated, but kept as backwards compatibility fallback for now.
        return MimeTypeGuesser::getInstance()->guess($path);
    }

    /**
     * Returns the mime type for given raw file content.
     *
     * @param string $content
     * @return string
     */
    public function guessMimeTypeForContent(string $content): string
    {
        $finfo = new finfo(FILEINFO_MIME);

        // Strip charset and other potential data, keep only the base type
        $parts = explode(' ', $finfo->buffer($content));

        return trim($parts[0], '; ');
    }

    /**
     * Returns extension for file contents at a given local path.
     *
     * Does not include the '.'
     *
     * @param string $path
     * @return string
     */
    public function guessExtensionForPath(string $path): string
    {
        return $this->guessExtensionForMimeType(
            $this->guessMimeTypeForPath($path)
        );
    }

    /**
     * Returns extension for a given mime type.
     *
     * Does not include the '.'
     *
     * @param string $type
     * @return string
     */
    public function guessExtensionForMimeType(string $type): string
    {
        if (class_exists(MimeTypes::class)) {
            $extensions = (new MimeTypes())->getExtensions($type);

            if (count($extensions)) {
                return head($extensions);
            }

            return '';
        }

        if (static::$mimeTypeExtensionGuesser !== null) {
            return static::getMimeTypeExtensionGuesserInstance()->guess($type);
        }

        throw new RuntimeException('Unable to guess, no extension guessin strategy available');
    }

    /**
     * Return an instance of the Symfony MIME type extension guesser.
     *
     * @return ExtensionGuesserInterface
     */
    public static function getMimeTypeExtensionGuesserInstance()
    {
        if (! static::$mimeTypeExtensionGuesser && class_exists(MimeTypeExtensionGuesser::class)) {
            static::$mimeTypeExtensionGuesser = new MimeTypeExtensionGuesser();
        }

        return static::$mimeTypeExtensionGuesser;
    }
}
