<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use Czim\FileHandling\Contracts\Support\RawContentInterface;
use Czim\FileHandling\Contracts\Support\UrlDownloaderInterface;
use Czim\FileHandling\Exceptions\CouldNotRetrieveRemoteFileException;
use Czim\FileHandling\Support\Content\RawContent;
use Exception;
use SplFileInfo;

class StorableFileFactory
{

    /**
     * @var MimeTypeHelperInterface
     */
    protected $mimeTypeHelper;

    /**
     * @var UrlDownloaderInterface
     */
    protected $downloader;

    /**
     * @var bool
     */
    protected $markNextUploaded = false;


    /**
     * @param MimeTypeHelperInterface $mimeTypeHelper
     * @param UrlDownloaderInterface  $downloader
     */
    public function __construct(MimeTypeHelperInterface $mimeTypeHelper, UrlDownloaderInterface $downloader)
    {
        $this->mimeTypeHelper = $mimeTypeHelper;
        $this->downloader     = $downloader;
    }

    /**
     * Marks the next storable file instance as having been locally uploaded.
     *
     * @return $this
     */
    public function uploaded()
    {
        $this->markNextUploaded = true;

        return $this;
    }

    /**
     * @param AbstractStorableFile $file
     * @return AbstractStorableFile
     */
    protected function getReturnPreparedFile(AbstractStorableFile $file)
    {
        if ($this->markNextUploaded) {
            $file->setUploaded();
            $this->markNextUploaded = false;
        }

        return $file;
    }

    /**
     * Makes a storable file interface from an SplFileInfo instance.
     *
     * @param SplFileInfo $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromFileInfo(SplFileInfo $data, $name = null, $mimeType = null)
    {
        $file = new SplFileInfoStorableFile;
        $file->setData($data);

        if (null !== $name) {
            $file->setName($name);
        }

        if (null !== $mimeType) {
            $file->setMimeType($mimeType);
        } else {
            $file->setMimeType(
                $this->mimeTypeHelper->guessMimeTypeForPath($data->getRealPath())
            );
        }

        return $this->getReturnPreparedFile($file);
    }

    /**
     * Makes a normalized storable file instance from a local file path.
     *
     * @param string      $path
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromLocalPath($path, $name = null, $mimeType = null)
    {
        $info = new SplFileInfo($path);

        return $this->makeFromFileInfo($info, $name, $mimeType);
    }

    /**
     * Makes a normalized storable file instance from a URI.
     *
     * @param string      $url
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     * @throws CouldNotRetrieveRemoteFileException
     */
    public function makeFromUrl($url, $name = null, $mimeType = null)
    {
        try {
            $localPath = $this->downloader->download($url);

        } catch (Exception $e) {

            throw new CouldNotRetrieveRemoteFileException(
                "Could not retrieve file from '{$url}'",
                $e->getcode(),
                $e
            );
        }

        if (null === $name) {
            $name = $this->getBaseNameFromUrl($url);
        }

        return $this->makeFromLocalPath($localPath, $name, $mimeType);
    }

    /**
     * Makes a normalized storable file instance from a data URI.
     *
     * @param string      $data
     * @param string      $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromDataUri($data, $name, $mimeType = null)
    {
        // todo
    }

    /**
     * Makes a normalized storable file instance from raw content data.
     *
     * @param string|RawContentInterface $data
     * @param string                     $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromRawData($data, $name, $mimeType = null)
    {
        $file = new RawStorableFile;

        if ($data instanceof RawContentInterface) {
            $data = $data->content();
        }

        $file->setName($name);
        $file->setData($data);

        // Guess the mimetype directly from the content
        if (null === $mimeType) {
            $mimeType = $this->mimeTypeHelper->guessMimeTypeForContent($data);
        }

        $file->setMimeType($mimeType);

        return $this->getReturnPreparedFile($file);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getBaseNameFromUrl($url)
    {
        if (false !== strpos($url, '?')) {
            $url = explode('?', $url)[0];
        }

        return pathinfo($url, PATHINFO_BASENAME);
    }

}
