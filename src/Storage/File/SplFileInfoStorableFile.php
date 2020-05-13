<?php
namespace Czim\FileHandling\Storage\File;

use Czim\FileHandling\Exceptions\StorableFileCouldNotBeDeletedException;
use RuntimeException;
use SplFileInfo;
use UnexpectedValueException;

class SplFileInfoStorableFile extends AbstractStorableFile
{

    /**
     * @var SplFileInfo
     */
    protected $file;


    /**
     * Initializes the storable file with mixed data.
     *
     * @param mixed $data
     */
    public function setData($data): void
    {
        if ( ! ($data instanceof SplFileInfo)) {
            throw new UnexpectedValueException('Expected SplFileInfo instance');
        }

        $this->file = $data;

        $this->setDerivedFileProperties();
    }

    /**
     * Sets properties based on the given data.
     */
    protected function setDerivedFileProperties(): void
    {
        if ( ! $this->file || ! file_exists($this->file->getRealPath())) {
            throw new RuntimeException("Local file not found at '{$this->file->getPath()}'");
        }

        $this->size = $this->file->getSize();

        if (null === $this->name) {
            $this->name = $this->file->getBasename();
        }
    }

    /**
     * Returns raw content of the file.
     *
     * @return string
     */
    public function content(): string
    {
        return file_get_contents($this->file->getRealPath());
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $path): bool
    {
        return copy($this->path(), $path);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        try {
            $success = unlink($this->path());

        } catch (\Exception $e) {

            throw new StorableFileCouldNotBeDeletedException(
                "Failed to unlink '{$this->path()}'",
                $e->getCode(),
                $e
            );
        }

        if ( ! $success) {
            // @codeCoverageIgnoreStart
            throw new StorableFileCouldNotBeDeletedException("Failed to unlink '{$this->path()}'");
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * {@inheritdoc}
     */
    public function path(): string
    {
        return $this->file->getRealPath();
    }

}
