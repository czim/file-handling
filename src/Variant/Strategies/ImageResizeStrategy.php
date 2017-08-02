<?php
namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\Resizer;
use Imagine\Gd\Imagine;

class ImageResizeStrategy extends AbstractImageStrategy
{

    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform()
    {
        $resizer = $this->getResizer();

        return (bool) $resizer->resize($this->file, $this->options);
    }

    /**
     * @return Resizer
     */
    protected function getResizer()
    {
        return new Resizer(new Imagine);
    }

}
