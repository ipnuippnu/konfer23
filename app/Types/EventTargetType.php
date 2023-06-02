<?php

namespace App\Types;

enum EventTargetType : string
{
    case PARTICIPANTS = 'Peserta';
    case DELEGATORS = 'Pimpinan';
    case PAYMENTS = 'Pembayaran';
    case GUESTS = 'Undangan';
}