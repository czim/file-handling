<?php
namespace Czim\FileHandling\Variant\Strategies;

use Czim\FileHandling\Support\Image\Resizer;

class ImageResizeStrategy extends AbstractImageStrategy
{

    /**
     * @var Resizer
     */
    protected $resizer;


    /**
     * @param Resizer $resizer
     */
    public function __construct(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }


    /**
     * Performs manipulation of the file.
     *
     * @return bool|null
     */
    protected function perform()
    {
        return (bool) $this->resizer->resize($this->file, $this->options);
    }

}
