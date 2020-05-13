<?php

namespace Czim\FileHandling\Contracts\Storage;

use Czim\FileHandling\Contracts\Support\RawContentInterface;
use SplFileInfo;

interface StorableFileFactoryInterface
{
    /**
     * Marks the next storable file instance as having been locally uploaded.
     *
     * @return $this|StorableFileFactoryInterface
     */
    public function uploaded(): StorableFileFactoryInterface;

    /**
     * Makes a storable file instance from an unknown source type.
     *
     * @param mixed       $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromAny($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface;

    /**
     * Makes a storable file instance from an SplFileInfo instance.
     *
     * @param SplFileInfo $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromFileInfo(SplFileInfo $data, ?string $name = null, ?string $mimeType = null): StorableFileInterface;

    /**
     * Makes a normalized storable file instance from a local file path.
     *
     * @param string      $path
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromLocalPath(string $path, ?string $name = null, ?string $mimeType = null): StorableFileInterface;

    /**
     * Makes a normalized storable file instance from a URI.
     *
     * @param string      $url
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromUrl(string $url, ?string $name = null, ?string $mimeType = null): StorableFileInterface;

    /**
     * Makes a normalized storable file instance from a data URI.
     *
     * @param string|RawContentInterface $data
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromDataUri($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface;

    /**
     * Makes a normalized storable file instance from raw content data.
     *
     * @param string|RawContentInterface $data
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromRawData($data, ?string $name = null, ?string $mimeType = null): StorableFileInterface;
}
