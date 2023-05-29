<?php

namespace App\Interfaces;

use Intervention\Image\Image;

interface AsImage
{
    public function image() : Image;
}