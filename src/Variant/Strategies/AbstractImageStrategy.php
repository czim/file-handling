<?php
namespace Czim\FileHandling\Variant\Strategies;

abstract class AbstractImageStrategy extends AbstractVariantStrategy
{

    /**
     * Returns whether this strategy can be applied to a file with a given mimeType.
     *
     * @param string $mimeType
     * @return bool
     */
    public function shouldApplyForMimeType($mimeType)
    {
        return 'image/' == substr($mimeType, 0, 6);
    }

}
