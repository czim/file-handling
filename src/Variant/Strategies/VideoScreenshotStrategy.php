<?php
namespace Czim\FileHandling\Variant\Strategies;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;

class VideoScreenshotStrategy extends AbstractVideoStrategy
{

    /**
     * Performs manipulation of the file.
     *
     * @return bool|null|void
     */
    protected function perform()
    {
        $path = $this->file->path();

        $imageName = pathinfo($path, PATHINFO_FILENAME) . '.jpg';
        $imagePath = pathinfo($path, PATHINFO_DIRNAME) . '/' . $imageName;

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => $this->getFfpmegBinaryPath(),
            'ffprobe.binaries' => $this->getFfprobeBinaryPath(),
        ]);

        $video = $ffmpeg->open($path);

        // Determine second at which to extract screenshot
        if (null !== ($percentage = $this->getPercentageConfigValue())) {
            // Percentage of full duration

            $ffprobe = FFProbe::create([
                'ffprobe.binaries' => $this->getFfprobeBinaryPath(),
            ]);

            $duration = (float) $ffprobe->format($path)->get('duration');

            $seconds = $percentage / 100 * $duration;

        } elseif (null === ($seconds = $this->getSecondsConfigValue())) {

            $seconds = 0;
        }

        $frame = $video->frame(TimeCode::fromSeconds($seconds));
        $frame->save($imagePath);

        $this->file->setName($imageName);
        $this->file->setMimeType('image/jpeg');
        $this->file->setData($imagePath);
    }

    /**
     * @return int|null
     */
    protected function getSecondsConfigValue()
    {
        if (array_key_exists('seconds', $this->options)) {
            return $this->options['seconds'];
        }

        return null;
    }

    /**
     * @return int|null
     */
    protected function getPercentageConfigValue()
    {
        if (array_key_exists('percentage', $this->options)) {
            return $this->options['percentage'];
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getFfpmegBinaryPath()
    {
        if (array_key_exists('ffmpeg', $this->options)) {
            return $this->options['ffmpeg'];
        }

        // @codeCoverageIgnoreStart
        return '/usr/bin/ffmpeg';
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return string
     */
    protected function getFfprobeBinaryPath()
    {
        if (array_key_exists('ffprobe', $this->options)) {
            return $this->options['ffprobe'];
        }

        // @codeCoverageIgnoreStart
        return '/usr/bin/ffprobe';
        // @codeCoverageIgnoreEnd
    }

}
