<?php

namespace App\Types;

enum EventParams : string
{
    case INCLUDING_ABSENTS = "including_absents";
    case ALLOW_OFFLINE = "allow_offline";
}