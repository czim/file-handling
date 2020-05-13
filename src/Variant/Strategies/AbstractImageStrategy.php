<?php

namespace Czim\FileHandling\Variant\Strategies;

abstract class AbstractImageStrategy extends AbstractVariantStrategy
{
    /**
     * Returns whether the variant strategy should be applied.
     *
     * @return bool
     */
    protected function shouldBeApplied(): bool
    {
        return 'image/' == substr($this->file->mimeType(), 0, 6);
    }
}
