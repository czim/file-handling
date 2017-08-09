<?php
namespace Czim\FileHandling\Variant\Strategies;

abstract class AbstractVideoStrategy extends AbstractVariantStrategy
{

    /**
     * Returns whether the variant strategy should be applied.
     *
     * @return bool
     */
    protected function shouldBeApplied()
    {
        return 'video/' == substr($this->file->mimeType(), 0, 6);
    }

}
