<?php

namespace App\Exports;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class TestExprt implements ShouldAutoSize, WithHeadings, WithEvents
{
    // public function view(): View
    // {
    //     return view('export.view')->with([
    //         'data' => Lembur::all(),
    //     ]);
    // }




    public function headings(): array
    {
        return [
            'DATA LEMBUR KARYAWAN KANTOR & UMUM',
            // Tambahkan kolom tambahan sesuai kebutuhan Anda
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $dataRekap = [];
                // Mendapatkan range sel heading
                $headingRange = 'A2:j3';
                // Memanipulasi sel heading
                $event->sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
                    ],
                ]);
                $event->sheet->getStyle('a1')->getFont()->setBold(true);
                $event->sheet->getRowDimension('1')->setRowHeight(30);




                // Menyimpan file
                $event->sheet->getParent()->getActiveSheet()->setTitle('KANTOR & UMUM');
            },
            AfterSheet::class => function (AfterSheet $event) {
                $dataRekap = [];
                // Mendapatkan range sel heading
                $headingRange = 'A2:j3';
                // Memanipulasi sel heading
                $event->sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
                    ],
                ]);
                $event->sheet->getStyle('a1')->getFont()->setBold(true);
                $event->sheet->getRowDimension('1')->setRowHeight(30);




                // Menyimpan file
                $event->sheet->getParent()->getActiveSheet()->setTitle('PRODUKSI');
            },

        ];
    }

    function hitungJamLembur($jamMulai, $jamSelesai)
    {
        $jamMulai = explode(':', $jamMulai);
        $jamSelesai = explode(':', $jamSelesai);

        $menitMulai = $jamMulai[0] * 60 + $jamMulai[1];
        $menitSelesai = $jamSelesai[0] * 60 + $jamSelesai[1];

        $selisihMenit = $menitSelesai - $menitMulai;
        return floor($selisihMenit / 60);
    }
}
