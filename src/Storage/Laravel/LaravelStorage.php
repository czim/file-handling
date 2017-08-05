<?php
namespace Czim\FileHandling\Storage\Laravel;

use Czim\FileHandling\Contracts\Storage\StorableFileInterface;
use Czim\FileHandling\Contracts\Storage\StorageInterface;
use Czim\FileHandling\Contracts\Storage\StoredFileInterface;
use Czim\FileHandling\Exceptions\FileStorageException;
use Czim\FileHandling\Storage\File\DecoratorStoredFile;
use Czim\FileHandling\Storage\File\RawStorableFile;
use Illuminate\Contracts\Filesystem\Filesystem;

class LaravelStorage implements StorageInterface
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Config key path for filesystem
     *
     * @var string
     */
    protected $configPath;

    /**
     * Whether the filesystem (and thus the paths given) are local
     *
     * @var bool
     */
    protected $isLocal;

    /**
     * The URL to prepend to relative file paths.
     *
     * @var null|string
     */
    protected $baseUrl;

    /**
     * @param Filesystem  $filesystem
     * @param string      $config
     * @param bool        $isLocal
     * @param null|string $baseUrl
     */
    public function __construct(
        Filesystem $filesystem,
        $config = 'filesystems.disks.local',
        $isLocal = true,
        $baseUrl = null
    ) {
        $this->filesystem = $filesystem;
        $this->configPath = $config;
        $this->isLocal    = $isLocal;
        $this->baseUrl    = trim($baseUrl ?: '', '/');
    }
    
    /**
     * Returns whether a stored file exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists($path)
    {
        return $this->filesystem->exists($path);
    }

    /**
     * Returns a public URL to the stored file.
     *
     * @param string $path
     * @return string
     */
    public function url($path)
    {
        return $this->prefixBaseUrl($path);
    }

    /**
     * Returns the file from storage.
     *
     * @param string $path
     * @return StoredFileInterface
     */
    public function get($path)
    {
        $raw = new RawStorableFile;

        $raw->setName(pathinfo($path, PATHINFO_BASENAME));
        $raw->setData($this->filesystem->get($path));
        // todo: handle mimetype

        $stored = new DecoratorStoredFile($raw);
        $stored->setUrl($this->prefixBaseUrl($path));

        return $stored;
    }

    /**
     * Stores a file.
     *
     * @param StorableFileInterface $file mixed content to store
     * @param string                $path where the file should be stored
     * @return StoredFileInterface
     * @throws FileStorageException
     */
    public function store(StorableFileInterface $file, $path)
    {
        $fullPath = trim($path, '/') . '/' . $file->name();

        if ( ! $this->filesystem->put($fullPath, $file->content())) {
            throw new FileStorageException("Failed to store '{$file->name()}' to '{$fullPath}'");
        }

        $stored = new DecoratorStoredFile($file);
        $stored->setUrl($this->prefixBaseUrl($fullPath));

        return $stored;
    }

    /**
     * Deletes a stored media file.
     *
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        return $this->filesystem->delete($path);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function prefixBaseUrl($path)
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
    
}
