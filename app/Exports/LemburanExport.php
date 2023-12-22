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

use function PHPUnit\Framework\isNull;

class LemburanExport implements ShouldAutoSize, WithHeadings, WithEvents
{
    // public function view(): View
    // {
    //     return view('export.view')->with([
    //         'data' => Lembur::all(),
    //     ]);
    // }

    protected $dataLemburan;
    protected $periode;
    public $dataRekap = [];


    public function __construct($dataLemburan, $periode)
    {
        $this->dataLemburan = $dataLemburan;
        $this->periode = $periode;
    }

    public function headings(): array
    {
        return [
            // 'DATA LEMBUR KARYAWAN KANTOR & UMUM',
            // Tambahkan kolom tambahan sesuai kebutuhan Anda
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $keys = array_keys($this->dataLemburan);

                for ($i = 0; $i < count($keys); $i++) {
                    $this->generateReport($event, $keys[$i], $this->dataLemburan[$keys[$i]], $i);
                    $event->sheet->getParent()->createSheet();
                }
                // $this->generateReport($event, $keys[1], $this->dataLemburan[$keys[1]], 0);

                $this->rekap($event, count($keys));
            }
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

    function generateReport($event, $title, $data, $i,)
    {

        // Set the title of the new sheet
        $event->sheet->getParent()->getSheet($i)->setTitle($title);

        $sheet = $event->sheet->getParent()->getSheet($i);

        // Mendapatkan range sel heading
        $headingRange = 'A2:j3';
        // Memanipulasi sel heading
        $sheet->getStyle($headingRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
            ],
        ]);
        $sheet->getStyle('a1')->getFont()->setBold(true);
        $sheet->getRowDimension('1')->setRowHeight(30);



        $sheet->mergeCells('A1:j1');
        $sheet->mergeCells('f2:g2');
        $sheet->mergeCells('a2:a3');
        $sheet->mergeCells('b2:b3');
        $sheet->mergeCells('c2:c3');
        $sheet->mergeCells('d2:d3');
        $sheet->mergeCells('e2:e3');
        $sheet->mergeCells('h2:h3');
        $sheet->mergeCells('i2:j3');

        $sheet->getStyle('A2:j3')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        $sheet->getStyle('A1:z1000')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('c2')
            ->getAlignment()
            ->setWrapText(true);
        $sheet->getStyle('h2')
            ->getAlignment()
            ->setWrapText(true);
        $sheet->setCellValue('a1', 'DATA LEMBUR KARYAWAN ' . strtoupper($title));

        $sheet->setCellValue('a2', 'NO');
        $sheet->setCellValue('b2', 'NAMA');
        $sheet->setCellValue('c2', 'DIVISI - JABATAN');
        $sheet->setCellValue('d2', 'ALASAN');
        $sheet->setCellValue('e2', 'TANGGAL');
        $sheet->setCellValue('f2', 'JAM LEMBUR');
        $sheet->setCellValue('h2', 'JUMLAH JAM');
        $sheet->setCellValue('i2', 'TOTAL LEMBUR');
        $sheet->setCellValue('f3', 'DARI');
        $sheet->setCellValue('g3', 'SAMPAI');

        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('c')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('e')->setWidth(17);
        $sheet->getColumnDimension('h')->setWidth(10);

        $index = 4;
        foreach ($data as $key => $lemburanPegawai) {
            $sheet->setCellValue('a' . $index, $key + 1);
            $sheet->mergeCells('a' . $index . ':a' . (count($lemburanPegawai) - 1 + $index));
            $sheet->mergeCells('b' . $index . ':b' . (count($lemburanPegawai) - 1 + $index));
            $sheet->mergeCells('c' . $index . ':c' . (count($lemburanPegawai) - 1 + $index));
            $sheet->mergeCells('j' . $index . ':j' . (count($lemburanPegawai) - 1 + $index));

            // array_push($dataRekap, $lemburanPegawai[0]['nama']);

            $totalNominal = 0;
            $jumlahJamLembur = 0;
            $nama = '';
            $jabatan = '';
            // dd($lemburanPegawai);
            foreach ($lemburanPegawai as $i => $item) {
                $jamMulai = $item['jam_mulai'];
                //menentukan jam

                if ($item['jam_selesai'] != null) {
                    // dd($item);
                    $absenPulang = $item['jam_selesai'];
                    $jamLembur = intval($absenPulang) - intval($jamMulai);
                } else {
                    if (count($item['absen']) <= 1) {
                        $absenMasuk = 'TA';
                        $absenPulang = 'TA';
                        $jamLembur = 0;
                    } else if (count($item['absen']) == 2) {
                        $absenMasuk = $item['absen'][0];
                        $absenPulang = 'TA';
                        $jamLembur = 0;
                    } else {
                        $absenMasuk = $item['absen'][0];
                        if ($item['lewat_hari']) {
                            $absenPulang = $lemburanPegawai[$i + 1]['absen'][0];
                            $jamLembur1 = intval('24:00') - intval($jamMulai);
                            $jamLembur2 = intval($absenPulang);
                            $jamLembur = $jamLembur1 + $jamLembur2;
                            // $jamLembur = $this->hitungJamLembur($jamMulai, $absenPulang);
                            $cellStyle = $sheet->getStyle('e' . ($index + $i) . ':i' . ($index + $i));
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB(Color::COLOR_RED);
                        } else {
                            $absenPulang = $item['absen'][count($item['absen']) - 2];
                            $jamLembur = intval($absenPulang) - intval($jamMulai);
                        }
                    }
                }



                //mewarnai cell yang merupakan lebur di hari libur
                if ($item['hari_libur']) {
                    $jamMulai = $absenMasuk;
                    // $jamLembur = intval($absenPulang) - intval($jamMulai);
                    $jamLembur = $this->hitungJamLembur($jamMulai, $absenPulang);
                    $cellStyle = $sheet->getStyle('e' . ($index + $i) . ':i' . ($index + $i));
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB(Color::COLOR_GREEN);
                }
                $sheet->setCellValue('b' . ($index + $i), $item['nama']);
                $sheet->setCellValue('c' . ($index + $i), $item['jabatan']);
                $sheet->setCellValue('d' . ($index + $i), $item['alasan']);
                $sheet->setCellValue('e' . ($index + $i), $item['tanggal']);

                $sheet->getStyle('d' . ($index + $i))
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle('a' . ($index + $i) . ':j' . ($index + $i))
                    ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('a' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('b' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('c' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('d' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('e' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('f' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('g' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('h' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('i' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('j' . ($index + $i))
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);



                $sheet->setCellValue('f' . ($index + $i), $jamMulai);
                $sheet->setCellValue('g' . ($index + $i), $absenPulang);
                $sheet->setCellValue('h' . ($index + $i), $jamLembur);

                $nominal = $item['hari_libur'] ? ($jamLembur * 15000) : ($jamLembur * 10000);
                $totalNominal += $nominal;
                $jumlahJamLembur += $jamLembur;
                $nama = $item['nama'];
                $jabatan = $item['jabatan'];
                $sheet->setCellValue('i' . ($index + $i), $nominal);
            }
            $sheet->setCellValue('j' . ($index), $totalNominal);
            $sheet->getStyle('A' . ($index + count($lemburanPegawai) - 1) . ':j' . ($index + count($lemburanPegawai) - 1))
                ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);


            $sheet->getStyle('a4' . ':c' . $index)->getFont()->setBold(true);
            $sheet->getStyle('i4' . ':j' . $index)->getFont()->setBold(true);
            $index += count($lemburanPegawai);

            // data rekap pada sheet ke 2
            array_push($this->dataRekap, [
                'nama' => $nama,
                'jabatan' => $jabatan,
                'jumlahJamLembur' => $jumlahJamLembur,
                'totalNominal' => $totalNominal,
            ]);
        }
        $sheet->mergeCells('e' . $index . ':h' . $index);
        $sheet->mergeCells('i' . $index . ':j' . $index);
        $sheet->setCellValue('i' . ($index), '=SUM(' . 'j4:' . 'j' . ($index - 1) . ')');
        $sheet->setCellValue('e' . ($index), 'TOTAL');
        $sheet->getStyle('i' . $index . ':j' . $index)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('e' . $index . ':h' . $index)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THICK);
        $sheet->getStyle('e' . $index . ':j' . $index)->applyFromArray([
            'font' => [
                'bold' => true,
            ],

        ]);
    }

    function rekap($event, $i)
    {
        // Set the title of the new sheet
        $event->sheet->getParent()->getSheet($i)->setTitle('RINGKASAN');

        $lastSheet = $event->sheet->getParent()->getSheet($i);
        // $periode = Carbon::parse(date('Y-m', strtotime($this->periode . ' -1 month')) . '-25')->isoFormat('D MMMM Y') . ' - ' . Carbon::parse($this->periode . '-24')->isoFormat('D MMMM Y');

        $lastSheet->setCellValue('a1', 'DATA LEMBUR KARYAWAN KANTOR & UMUM');
        $lastSheet->setCellValue('a2', 'PERIODE ' . strtoupper($this->getPeriode()));
        $lastSheet->mergeCells('A1:e1');
        $lastSheet->mergeCells('A2:e2');

        $lastSheet->getColumnDimension('B')->setWidth(20);
        $lastSheet->getColumnDimension('c')->setWidth(20);
        $lastSheet->getColumnDimension('D')->setWidth(15);
        $lastSheet->getColumnDimension('e')->setWidth(20);

        $lastSheet->getStyle('a1:e2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
            ],
        ]);

        $lastSheet->getStyle('A3:E3')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        $lastSheet->getStyle('A1:z1000')
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $lastSheet->getStyle('C3')
            ->getAlignment()
            ->setWrapText(true);
        $lastSheet->getStyle('d3')
            ->getAlignment()
            ->setWrapText(true);
        $lastSheet->getStyle('a3:e3')->getFont()->setBold(true);


        $lastSheet->setCellValue('a3', 'NO');
        $lastSheet->setCellValue('b3', 'NAMA');
        $lastSheet->setCellValue('c3', 'DIVISI - JABATAN');
        $lastSheet->setCellValue('D3', 'JUMLAH JAM LEMBUR');
        $lastSheet->setCellValue('E3', 'TOTAL');

        $totalKeseluruhan = 0;
        $index = 4;


        // dd($dataRekap[0]['nama']);

        // Mengurutkan array menggunakan fungsi compareNames
        usort($this->dataRekap, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });
        foreach ($this->dataRekap as $rekap) {
            $lastSheet->setCellValue('a' . $index, $index - 3);
            $lastSheet->setCellValue('b' . $index, $rekap['nama']);
            $lastSheet->setCellValue('c' . $index, $rekap['jabatan']);
            $lastSheet->setCellValue('d' . $index, $rekap['jumlahJamLembur']);
            $lastSheet->setCellValue('e' . $index, $rekap['totalNominal']);

            $lastSheet->getStyle('A' . $index . ':E' . $index)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $totalKeseluruhan += $rekap['totalNominal'];
            $index++;
        }

        $lastSheet->setCellValue('a' . $index, 'TOTAL KESELURUHAN');
        $lastSheet->setCellValue('e' . $index, $totalKeseluruhan);
        $lastSheet->getStyle('a' . $index . ':e' . $index)->getFont()->setBold(true);
        $lastSheet->mergeCells('A' . $index . ':d' . $index);

        $lastSheet->getStyle('A4:e' . $index)->getNumberFormat()->setFormatCode('#,##0');
        $lastSheet->getStyle('A' . $index . ':e' . $index)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_MEDIUM);

        // Membuat bentuk (shape)
        $kotak1 = new Drawing();
        $kotak1->setName('Shape 1');
        $kotak1->setPath('assets/images/kotak1.png'); // Path gambar bentuk
        $kotak1->setCoordinates('A' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
        $kotak1->setWorksheet($lastSheet);

        // Menentukan ukuran dan posisi
        $kotak1->setWidth(200); // Lebar bentuk dalam piksel
        $kotak1->setHeight(100); // Tinggi bentuk dalam piksel
        $kotak1->setOffsetX(30); // Posisi horizontal bentuk dalam piksel
        $kotak1->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

        // Membuat bentuk (shape)
        $kotak2 = new Drawing();
        $kotak2->setName('Shape 2');
        $kotak2->setPath('assets/images/kotak2.png'); // Path gambar bentuk
        $kotak2->setCoordinates('c' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
        $kotak2->setWorksheet($lastSheet);

        // Menentukan ukuran dan posisi
        $kotak2->setWidth(200); // Lebar bentuk dalam piksel
        $kotak2->setHeight(100); // Tinggi bentuk dalam piksel
        $kotak2->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

        // Membuat bentuk (shape)
        $kotak3 = new Drawing();
        $kotak3->setName('Shape 3');
        $kotak3->setPath('assets/images/kotak3.png'); // Path gambar bentuk
        $kotak3->setCoordinates('d' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
        $kotak3->setWorksheet($lastSheet);

        // Menentukan ukuran dan posisi
        $kotak3->setWidth(200); // Lebar bentuk dalam piksel
        $kotak3->setHeight(100); // Tinggi bentuk dalam piksel
        $kotak3->setOffsetX(50); // Posisi horizontal bentuk dalam piksel
        $kotak3->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

    }

    function getPeriode()
    {
        Carbon::setLocale('id_ID');
        $periode = explode(' to ', $this->periode);
        $startDate = Carbon::createFromFormat('d-m-Y', $periode[0]);
        $endDate =  Carbon::createFromFormat('d-m-Y', count($periode) < 2  ?  $periode[0] : $periode[1]);

        // // Ubah format tanggal ke bahasa Indonesia
        // $startDateIndonesia = $startDate->format('d F Y');
        // $endDateIndonesia = $endDate->format('d F Y');

        // Format tanggal dalam bahasa Indonesia
        $startDateIndonesia = $startDate->isoFormat('D MMMM');
        $endDateIndonesia = $endDate->isoFormat('D MMMM YYYY');
        // Hasil
        $output = $startDateIndonesia . ' - ' . $endDateIndonesia;

        return  $output;
    }
}
