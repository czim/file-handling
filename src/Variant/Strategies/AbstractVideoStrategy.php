<?php

namespace Czim\FileHandling\Variant\Strategies;

abstract class AbstractVideoStrategy extends AbstractVariantStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function shouldBeApplied(): bool
    {
        return 'video/' == substr($this->file->mimeType(), 0, 6);
    }
}
