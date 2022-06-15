<?php

namespace Czim\FileHandling\Support\Download;

use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use Czim\FileHandling\Contracts\Support\UrlDownloaderInterface;
use Czim\FileHandling\Exceptions\CouldNotRetrieveRemoteFileException;
use Exception;

class UrlDownloader implements UrlDownloaderInterface
{
    /**
     * @var MimeTypeHelperInterface
     */
    protected $mimeTypeHelper;


    /**
     * @param MimeTypeHelperInterface $mimeTypeHelper
     */
    public function __construct(MimeTypeHelperInterface $mimeTypeHelper)
    {
        $this->mimeTypeHelper = $mimeTypeHelper;
    }


    /**
     * Downloads from a URL and returns locally stored temporary file.
     *
     * @param string $url
     * @return string
     * @throws CouldNotRetrieveRemoteFileException
     */
    public function download(string $url): string
    {
        $localPath = $this->makeLocalTemporaryPath();

        $url = $this->normalizeUrl($url);

        $this->downloadToTempLocalPath($url, $localPath);

        // Remove the query string if it exists, to make sure the extension is valid
        if (false !== strpos($url, '?')) {
            $url = explode('?', $url)[0];
        }

        $pathinfo = pathinfo($url);

        // If the file has no extension, rename the local instance with a guessed extension added.
        if (empty($pathinfo['extension'])) {
            $localPath = $this->renameLocalTemporaryFileWithAddedExtension($localPath, $pathinfo['basename']);
        }

        return $localPath;
    }

    /**
     * Downloads raw file content from a URL to a local path.
     *
     * @param string $url
     * @param string $localPath
     * @throws CouldNotRetrieveRemoteFileException
     * @codeCoverageIgnore
     */
    protected function downloadToTempLocalPath(string $url, string $localPath): void
    {
        $curlError = 'unknown error';

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $rawFile = curl_exec($ch);

            if ($rawFile === false) {
                $curlError = curl_error($ch);
            }

            curl_close($ch);
        } catch (Exception $e) {
            throw new CouldNotRetrieveRemoteFileException(
                "Failed to download file content from '{$url}': {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }

        if (false === $rawFile) {
            throw new CouldNotRetrieveRemoteFileException(
                "curl_exec failed while downloading '{$url}': " . $curlError
            );
        }

        try {
            if (false === file_put_contents($localPath, $rawFile)) {
                throw new CouldNotRetrieveRemoteFileException('file_put_contents call failed');
            }
        } catch (Exception $e) {
            throw new CouldNotRetrieveRemoteFileException(
                'file_put_contents call threw an exception',
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $path
     * @param string $name
     * @return string
     * @throws CouldNotRetrieveRemoteFileException
     */
    protected function renameLocalTemporaryFileWithAddedExtension(string $path, string $name): string
    {
        try {
            $extension = $this->mimeTypeHelper->guessExtensionForPath($path);
        } catch (Exception $e) {
            throw new CouldNotRetrieveRemoteFileException(
                "Failed to fill in extension for local file: {$path}",
                $e->getCode(),
                $e
            );
        }

        return $this->renameFile($path, "{$name}.{$extension}");
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function makeLocalTemporaryPath(): string
    {
        return sys_get_temp_dir() . '/' . uniqid('filehandling-download-');
    }

    /**
     * Renames a local (temp) file and returns the new path to it.
     *
     * @param string $path
     * @param string $newName
     * @return string
     * @throws CouldNotRetrieveRemoteFileException
     */
    protected function renameFile(string $path, string $newName): string
    {
        $newPath = pathinfo($path, PATHINFO_DIRNAME) . '/' . $newName;

        try {
            $success = rename($path, $newPath);
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            throw new CouldNotRetrieveRemoteFileException("Failed to rename '{$path}' to '{$newName}'", $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }

        // @codeCoverageIgnoreStart
        if (! $success) {
            throw new CouldNotRetrieveRemoteFileException("Failed to rename '{$path}' to '{$newName}'");
        }
        // @codeCoverageIgnoreEnd

        return $newPath;
    }

    /**
     * Normalizes URL for safe cURL use.
     *
     * @param string $url
     * @return string
     */
    protected function normalizeUrl(string $url): string
    {
        return str_replace(' ', '%20', $url);
    }
}
