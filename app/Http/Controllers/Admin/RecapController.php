<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DelegatorsExport;
use App\Exports\ParticipantsExport;
use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecapController extends Controller
{
    public function participants()
    {
        activity()->log("Unduh rekap peserta");
        return Excel::download(new ParticipantsExport, $this->_safeName('Peserta ' . Carbon::now()->format('Y-m-s H:i:s')) . ".xlsx");
    }

    public function delegators()
    {
        activity()->log("Unduh rekap pimpinan");
        return Excel::download(new DelegatorsExport, $this->_safeName('Pimpinan ' . Carbon::now()->format('Y-m-s H:i:s')) . ".xlsx");
    }

    public function payments()
    {
        activity()->log("Unduh rekap pembayaran");
        return Excel::download(new PaymentsExport, $this->_safeName('Pembayaran ' . Carbon::now()->format('Y-m-s H:i:s')) . ".xlsx");
    }

    private function _safeName($val) : string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $val);
    }
}
