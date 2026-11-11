<?php

namespace Czim\FileHandling\Enums;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
class ContentTypes extends Enum
{
    public const URI     = 'uri';
    public const DATAURI = 'datauri';
    public const RAW     = 'raw';
}
