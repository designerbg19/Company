<?php

namespace App\Service;

class Base64FileExtractor
{
    public function extractBase64String(string $base64Content)
    {

        $data = explode( ';base64,', $base64Content);
        return $data;

    }

}