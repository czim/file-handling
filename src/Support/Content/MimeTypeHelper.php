<?php
namespace Czim\FileHandling\Support\Content;

use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use finfo;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 * Class MimeTypeHelper
 *
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
    public function guessMimeTypeForPath($path)
    {
        return MimeTypeGuesser::getInstance()->guess($path);
    }

    /**
     * Returns the mime type for given raw file content.
     *
     * @param string $content
     * @return string
     */
    public function guessMimeTypeForContent($content)
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
    public function guessExtensionForPath($path)
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
    public function guessExtensionForMimeType($type)
    {
        return static::getMimeTypeExtensionGuesserInstance()->guess($type);
    }

    /**
     * Return an instance of the Symfony MIME type extension guesser.
     *
     * @return ExtensionGuesserInterface
     */
    public static function getMimeTypeExtensionGuesserInstance()
    {
        if ( ! static::$mimeTypeExtensionGuesser) {
            static::$mimeTypeExtensionGuesser = new MimeTypeExtensionGuesser;
        }

        return static::$mimeTypeExtensionGuesser;
    }

}
