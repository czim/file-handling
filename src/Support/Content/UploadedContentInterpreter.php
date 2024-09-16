<?php

namespace Czim\FileHandling\Support\Content;

use Czim\FileHandling\Contracts\Support\ContentInterpreterInterface;
use Czim\FileHandling\Contracts\Support\RawContentInterface;
use Czim\FileHandling\Enums\ContentTypes;

class UploadedContentInterpreter implements ContentInterpreterInterface
{
    protected const FULL_DATA_THRESHOLD = 2048;

    protected const DATAURI_REGEX      = '#^data:[-\w]+/[-\w\+\.]+;base64#';
    protected const DATAURI_TEST_CHUNK = 100;

    /**
     * Returns which type the given content is deemed to be.
     *
     * @param RawContentInterface $content
     * @return string
     * @see ContentTypes
     */
    public function interpret(RawContentInterface $content): string
    {
        // Treat any string longer than 2048 characters as full data
        if (
            $content->size() <= static::FULL_DATA_THRESHOLD
            && filter_var($content->content(), FILTER_VALIDATE_URL)
        ) {
            return ContentTypes::URI;
        }

        $chunk = $content->chunk(0, static::DATAURI_TEST_CHUNK);

        // Detect data uri
        if ($chunk !== null && preg_match(static::DATAURI_REGEX, $chunk)) {
            return ContentTypes::DATAURI;
        }

        return ContentTypes::RAW;
    }
}
