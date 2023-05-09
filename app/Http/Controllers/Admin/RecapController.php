<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ParticipantsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecapController extends Controller
{
    public function participants()
    {
        return Excel::download(new ParticipantsExport, 'peserta.xlsx');
    }
}
