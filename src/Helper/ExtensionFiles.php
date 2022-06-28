<?php

namespace App\Helper;

class ExtensionFiles
{
    public function extFiles($image, $files)
    {
        $ext = pathinfo($image->getName(), PATHINFO_EXTENSION);
        if ($ext === 'pdf') {
            $files->setPdf($image);
        } else {
            $files->setImage($image);
        }
    }

}