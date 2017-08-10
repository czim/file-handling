<?php
namespace Czim\FileHandling\Contracts\Storage;

use Czim\FileHandling\Contracts\Support\RawContentInterface;
use SplFileInfo;

interface StorableFileFactoryInterface
{

    /**
     * Marks the next storable file instance as having been locally uploaded.
     *
     * @return $this
     */
    public function uploaded();

    /**
     * Makes a storable file instance from an unknown source type.
     *
     * @param mixed       $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromAny($data, $name = null, $mimeType = null);

    /**
     * Makes a storable file instance from an SplFileInfo instance.
     *
     * @param SplFileInfo $data
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromFileInfo(SplFileInfo $data, $name = null, $mimeType = null);

    /**
     * Makes a normalized storable file instance from a local file path.
     *
     * @param string      $path
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromLocalPath($path, $name = null, $mimeType = null);

    /**
     * Makes a normalized storable file instance from a URI.
     *
     * @param string      $url
     * @param string|null $name
     * @param string|null $mimeType
     * @return StorableFileInterface
     */
    public function makeFromUrl($url, $name = null, $mimeType = null);

    /**
     * Makes a normalized storable file instance from a data URI.
     *
     * @param string|RawContentInterface $data
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromDataUri($data, $name = null, $mimeType = null);

    /**
     * Makes a normalized storable file instance from raw content data.
     *
     * @param string|RawContentInterface $data
     * @param string|null                $name
     * @param string|null                $mimeType
     * @return StorableFileInterface
     */
    public function makeFromRawData($data, $name = null, $mimeType = null);

}
