<?php

declare(strict_types=1);

namespace Czim\FileHandling\Support\Download;

use Czim\FileHandling\Contracts\Support\UriValidatorInterface;

/**
 * By default this validator will report ANY URI as invalid.
 * This is intentional to minimize security risks.
 *
 * The allowLocalUri() and allowRemoteUri() methods offer ways to deliberately open the package up to these risks.
 */
class UriValidator implements UriValidatorInterface
{
    /**
     * @var bool
     */
    protected $allowLocalFileUri = false;

    /**
     * @var bool
     */
    protected $allowRemoteUri = false;


    /**
     * This is a major security risk, so use with care.
     * Any local file path "file://..." will be valid, allowing file handling to download from the local filesystem.
     */
    public function allowLocalUri(): void
    {
        $this->allowLocalFileUri = true;
    }

    /**
     * This is a security risk, so use with care.
     * Any remote path "https://...", "ftp://..." will be valid, allowing file handling to download from remote systems.
     */
    public function allowRemoteUri(): void
    {
        $this->allowRemoteUri = true;
    }

    public function isValid(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return $this->isUrlWithAllowedProtocol($url);
    }

    protected function isUrlWithAllowedProtocol(string $url): bool
    {
        $allowedProtocols = $this->getAllowedUrlProtocols();

        if (empty($allowedProtocols)) {
            return false;
        }

        foreach ($allowedProtocols as $protocol) {
            if (strpos($url, $protocol . '://') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Do not allow 'file' here, unless you are *very* certain you know what you're doing.
     * Otherwise attacks are possible where secrets may be exposed because local files may be 'downloaded'.
     *
     * @return array<int, string>
     */
    protected function getAllowedUrlProtocols(): array
    {
        $allowed = [];

        if ($this->allowLocalFileUri) {
            $allowed[] = 'file';
        }

        if ($this->allowRemoteUri) {
            $allowed[] = 'http';
            $allowed[] = 'https';
            $allowed[] = 'ftp';
        }

        return $allowed;
    }
}
