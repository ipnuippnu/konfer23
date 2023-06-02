<?php

namespace App\Generators;

use App\Interfaces\AsPdf;
use App\Models\Event;
use App\Traits\Generator;
use TCPDF;

class RecapEvent implements AsPdf
{
    use Generator;

    public function __construct(
        public Event $event
    )
    {
        
    }

    public function pdf(): TCPDF
    {
        $pdf = new CustomRecap('P', 'mm', 'F4');
        $pdf->setHeaderMargin(5);
        $pdf->setTopMargin(50);

        $pdf->AddPage();

        $pdf->SetFont('Times', 'B', 14);  
        $txt = <<<EOD
        DAFTAR PRESENSI
        KONFERENSI CABANG XX-XIX
        PC IPNU-IPPNU TRENGGALEK
        EOD;
        $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
        $pdf->setFontSize(12);
        $pdf->Ln();

        $pdf->SetFont('Times', '');  
        $pdf->write(0, "Kegiatan: ");

        $pdf->SetFont('Times', 'B');
        $pdf->write(0, "{$this->event->name}");

        $pdf->SetFont('Times', '');
        $pdf->Cell(0, txt: $this->event->event_start->isoFormat('dddd, D MMMM YYYY'), align: 'R');
        
        $pdf->Ln(6);

        // ==== CONTENT ===
        $tbl = <<<EOD
        <table cellspacing="0" cellpadding="2" border="1">
            <tr>
                <th width="7%" align="center"><b>No.</b></th>
                <th width="45%" align="center"><b>Nama</b></th>
                <th width="28%" align="center"><b>Asal</b></th>
                <th width="20%" align="center"><b>Status</b></th>
            </tr>
        EOD;

        if($this->event->members?->count())
        $this->event->members->each(function($data, $k) use(&$tbl){
            ++$k;
            $tbl .= <<<EOD
            <tr>
                <td>{$k}</td>
                <td>{$data->name}</td>
                <td>-</td>
                <td>H</td>
            </tr>
            EOD;
        });
        else
        $tbl .= <<<EOD
        <tr>
            <td colspan="4" align="center"><i>Data Kosong</i></td>
        </tr>
        EOD;

        $tbl .= <<<EOD
        </table>
        EOD;

        $pdf->writeHTML($tbl);

        return $pdf;
    }
}

class CustomRecap extends TCPDF
{

    public function Header()
    {
        $widthMargin = $this->getMargins()['left'] + $this->getMargins()['right'];
        $this->Image(resource_path('templates/KOP.jpg'), $this->getMargins()['left'], $this->getHeaderMargin(), ($this->getPageWidth() - $widthMargin));
    }

    // public function Footer()
    // {
    //     $this->Write(0, txt: 'Dokumen ini telah ditanda tangani secara digital oleh EDO');
    // }

}