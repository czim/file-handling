<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Support\ContentInterpreterInterface;
use Czim\FileHandling\Contracts\Support\MimeTypeHelperInterface;
use Czim\FileHandling\Contracts\Support\RawContentInterface;
use Czim\FileHandling\Contracts\Support\UrlDownloaderInterface;
use Czim\FileHandling\Enums\ContentTypes;
use Czim\FileHandling\Exceptions\CouldNotReadDataException;
use Czim\FileHandling\Exceptions\CouldNotRetrieveRemoteFileException;
use Czim\FileHandling\Support\Content\RawContent;
use Exception;
use SplFileInfo;
use UnexpectedValueException;

class StorableFileFactory
{

    /**
     * @var MimeTypeHelperInterface
     */
    protected $mimeTypeHelper;

    /**
     * @var ContentInterpreterInterface
     */
    protected $interpreter;

    /**
     * @var UrlDownloaderInterface
     */
    protected $downloader;

    /**
     * @var bool
     */
    protected $markNextUploaded = false;


    /**
     * @param MimeTypeHelperInterface     $mimeTypeHelper
     * @param ContentInterpreterInterface $contentInterpreter
     * @param UrlDownloaderInterface      $downloader
     */
    public function __construct(
        MimeTypeHelperInterface $mimeTypeHelper,
        ContentInterpreterInterface $contentInterpreter,
        UrlDownloaderInterface $downloader
    ) {
        $this->mimeTypeHelper = $mimeTypeHelper;
        $this->interpreter    = $contentInterpreter;
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
     * Makes a storable file instance from an unknown source type.
     *
     * @param mixed       $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromAny($data, $name = null, $mimeType = null)
    {
        if ($data instanceof SplFileInfo) {
            return $this->makeFromFileInfo($data);
        }

        if ( ! is_string($data)) {
            throw new UnexpectedValueException('Could not interpret given data, string value expected');
        }

        if ( ! ($data instanceof RawContentInterface)) {
            $data = new RawContent($data);
        }

        switch ($this->interpreter->interpret($data)) {

            case ContentTypes::URI:
                return $this->makeFromUrl($data->content(), $name, $mimeType);

            case ContentTypes::DATAURI:
                return $this->makeFromDataUri($data, $name, $mimeType);

            case ContentTypes::RAW:
            default:
                return $this->makeFromRawData($data, $name, $mimeType);
        }
    }

    /**
     * Makes a storable file instance from an SplFileInfo instance.
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

        // Always flag as uploaded, since we downloaded to a local path
        return $this->uploaded()->makeFromLocalPath($localPath, $name, $mimeType);
    }

    /**
     * Makes a normalized storable file instance from a data URI.
     *
     * @param string|RawContentInterface $data
     * @param string                     $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     * @throws CouldNotReadDataException
     */
    public function makeFromDataUri($data, $name, $mimeType = null)
    {
        if ($data instanceof RawContentInterface) {
            $data = $data->content();
        }

        $resource = @fopen($data, 'r');

        if ( ! $resource) {
            throw new CouldNotReadDataException('Invalid data URI');
        }

        try {
            $meta = stream_get_meta_data($resource);

        } catch (Exception $e) {
            throw new CouldNotReadDataException('Failed to interpret Data URI as stream', $e->getCode(), $e);
        }

        if (null === $mimeType) {
            $mimeType = $meta['mediatype'];
        }

        $extension = $this->mimeTypeHelper->guessExtensionForMimeType($mimeType);
        $localPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($meta['uri']) . '.' . $extension;

        try {
            file_put_contents($localPath, stream_get_contents($resource));
        } catch (Exception $e) {
            throw new CouldNotReadDataException('Failed to make local file from Data URI', $e->getCode(), $e);
        }

        // Always flag as uploaded, since a temp file was created
        return $this->uploaded()->makeFromLocalPath($localPath, $name);
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
