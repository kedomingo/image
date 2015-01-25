<?php

namespace Intervention\Image\Imagick\Commands;

use \Intervention\Image\Size;

class FitCommand extends \Intervention\Image\Commands\AbstractCommand
{
    /**
     * Crops and resized an image at the same time
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $width = $this->argument(0)->type('digit')->required()->value();
        $height = $this->argument(1)->type('digit')->value($width);
        $constraints = $this->argument(2)->type('closure')->value();
        $position = $this->argument(3)->type('string')->value('center');

        // calculate size
        $cropped = $image->getSize()->fit(new Size($width, $height), $position);
        $resized = clone $cropped;
        $resized = $resized->resize($width, $height, $constraints);

        foreach ($image as $frame) {

            // crop image
            $frame->getCore()->cropImage(
                $cropped->width,
                $cropped->height,
                $cropped->pivot->x,
                $cropped->pivot->y
            );

            // resize image
            $frame->getCore()->resizeImage($resized->getWidth(), $resized->getHeight(), \Imagick::FILTER_BOX, 1);
            $frame->getCore()->setImagePage(0,0,0,0);
        }

        return true;
    }
}
