<?php

namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Contracts\Storage\StorableFileFactoryInterface;
use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\UploadedMarkableInterface;
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

class StorableFileFactory implements StorableFileFactoryInterface
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
     * @return $this|StorableFileFactoryInterface
     */
    public function uploaded(): StorableFileFactoryInterface
    {
        $this->markNextUploaded = true;

        return $this;
    }

    /**
     * Makes a storable file instance from an unknown source type.
     *
     * @param mixed       $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     * @throws CouldNotReadDataException
     * @throws CouldNotRetrieveRemoteFileException
     */
    public function makeFromAny($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface
    {
        // If the data is already a storable file, return it as-is.
        if ($data instanceof StorableFileInterface) {
            return $this->getReturnPreparedFile($data);
        }


        if (null === $name && is_a($data, \Symfony\Component\HttpFoundation\File\UploadedFile::class)) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $data */
            $name = $data->getClientOriginalName();
        }

        if ($data instanceof SplFileInfo) {
            return $this->makeFromFileInfo($data, $name, $mimeType);
        }


        // Fallback: expect raw or string data, and attempt to interpret it.
        if (is_string($data)) {
            $data = new RawContent($data);
        }

        if (! ($data instanceof RawContentInterface)) {
            throw new UnexpectedValueException('Could not interpret given data, string value expected');
        }

        return $this->interpretFromRawContent($data, $name, $mimeType);
    }

    /**
     * Makes a storable file instance from an SplFileInfo instance.
     *
     * @param SplFileInfo $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromFileInfo(SplFileInfo $data, ?string $name = null, ?string $mimeType = null): StorableFileInterface
    {
        $file = new SplFileInfoStorableFile();
        $file->setData($data);

        if (null !== $mimeType) {
            $file->setMimeType($mimeType);
        } else {
            $file->setMimeType(
                $this->mimeTypeHelper->guessMimeTypeForPath($data->getRealPath())
            );
        }

        if (empty($name)) {
            $name = pathinfo($data->getRealPath(), PATHINFO_BASENAME);
        }

        if (null !== $name) {
            $file->setName($name);
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
    public function makeFromLocalPath(string $path, ?string $name = null, ?string $mimeType = null): StorableFileInterface
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
    public function makeFromUrl(string $url, ?string $name = null, ?string $mimeType = null): StorableFileInterface
    {
        try {
            $localPath = $this->downloader->download($url);
        } catch (Exception $e) {
            throw new CouldNotRetrieveRemoteFileException(
                "Could not retrieve file from '{$url}'",
                $e->getCode(),
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
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     * @throws CouldNotReadDataException
     */
    public function makeFromDataUri($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface
    {
        if ($data instanceof RawContentInterface) {
            $data = $data->content();
        }

        $resource = @fopen($data, 'r');

        if (! $resource) {
            throw new CouldNotReadDataException('Invalid data URI');
        }

        try {
            $meta = stream_get_meta_data($resource);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new CouldNotReadDataException('Failed to interpret Data URI as stream', $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }

        if (null === $mimeType) {
            $mimeType = $meta['mediatype'];
        }

        $extension = $this->mimeTypeHelper->guessExtensionForMimeType($mimeType);
        $localPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($meta['uri']) . '.' . $extension;

        if (null === $name) {
            $name = pathinfo($localPath, PATHINFO_BASENAME);
        }

        try {
            file_put_contents($localPath, stream_get_contents($resource));
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new CouldNotReadDataException('Failed to make local file from Data URI', $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }

        // Always flag as uploaded, since a temp file was created
        return $this->uploaded()->makeFromLocalPath($localPath, $name);
    }

    /**
     * Makes a normalized storable file instance from raw content data.
     *
     * @param string|RawContentInterface $data
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromRawData($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface
    {
        $file = new RawStorableFile();

        if ($data instanceof RawContentInterface) {
            $data = $data->content();
        }

        // Guess the mimetype directly from the content
        if (null === $mimeType) {
            $mimeType = $this->mimeTypeHelper->guessMimeTypeForContent($data);
        }

        if (empty($name)) {
            $name = $this->makeRandomName(
                $this->mimeTypeHelper->guessExtensionForMimeType($mimeType)
            );
        }

        $file->setName($name);
        $file->setData($data);
        $file->setMimeType($mimeType);

        return $this->getReturnPreparedFile($file);
    }

    /**
     * Interprets given raw content as a storable file.
     *
     * @param RawContentInterface $data
     * @param string|null         $name
     * @param string|null         $mimeType
     * @return StorableFileInterface
     * @throws CouldNotReadDataException
     * @throws CouldNotRetrieveRemoteFileException
     */
    protected function interpretFromRawContent(RawContentInterface $data, ?string $name, ?string $mimeType): StorableFileInterface
    {
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
     * @param StorableFileInterface|UploadedMarkableInterface $file
     * @return StorableFileInterface
     */
    protected function getReturnPreparedFile(StorableFileInterface $file): StorableFileInterface
    {
        if ($this->markNextUploaded && $file instanceof UploadedMarkableInterface) {
            $file->setUploaded();
            $this->markNextUploaded = false;
        }

        return $file;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getBaseNameFromUrl(string $url): string
    {
        if (false !== strpos($url, '?')) {
            $url = explode('?', $url)[0];
        }

        return pathinfo($url, PATHINFO_BASENAME);
    }

    /**
     * Returns random name for a file.
     *
     * @param string $extension
     * @return string
     */
    protected function makeRandomName(string $extension): string
    {
        return substr(md5(microtime()), 0, 16)
            . ($extension ? '.' . $extension : '');
    }
}
