<?php

namespace App\Traits;

use App\Interfaces\AsImage;
use App\Interfaces\AsPdf;
use Exception;

trait Generator
{    
    /**
     * Mengembalikan hasil generator
     * !! Jangan Lupa Sholat !!
     *
     * @param  mixed $args
     * @return mixed
     */
    public static function generate(...$args) : mixed
    {
        $obj = new static(...$args);
        if(in_array(AsImage::class, class_implements($obj)))
            return $obj->image();

        if(in_array(AsPdf::class, class_implements($obj)))
            return $obj->pdf();

        throw new Exception('Tidak mengimplementasikan interface');
    }
}