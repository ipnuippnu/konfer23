<?php

namespace App\Interfaces;

use TCPDF;

interface AsPdf
{
    public function pdf() : TCPDF;
}