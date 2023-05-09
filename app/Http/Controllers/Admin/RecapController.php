<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DelegatorsExport;
use App\Exports\ParticipantsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecapController extends Controller
{
    public function participants()
    {
        activity()->log("Unduh rekap peserta");
        return Excel::download(new ParticipantsExport, 'peserta.xlsx');
    }

    public function delegators()
    {
        activity()->log("Unduh rekap pimpinan");
        return Excel::download(new DelegatorsExport, 'pimpinan.xlsx');
    }
}
