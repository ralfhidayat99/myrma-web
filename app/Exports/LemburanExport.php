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

    public function __construct($dataLemburan, $periode)
    {
        $this->dataLemburan = $dataLemburan;
        $this->periode = $periode;
    }

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
                $keys = array_keys($this->dataLemburan);
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



                $event->sheet->getDelegate()->mergeCells('A1:j1');
                $event->sheet->getDelegate()->mergeCells('f2:g2');
                $event->sheet->getDelegate()->mergeCells('a2:a3');
                $event->sheet->getDelegate()->mergeCells('b2:b3');
                $event->sheet->getDelegate()->mergeCells('c2:c3');
                $event->sheet->getDelegate()->mergeCells('d2:d3');
                $event->sheet->getDelegate()->mergeCells('e2:e3');
                $event->sheet->getDelegate()->mergeCells('h2:h3');
                $event->sheet->getDelegate()->mergeCells('i2:j3');

                $event->sheet->getStyle('A2:j3')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                $event->sheet->getStyle('A1:z1000')
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('c2')
                    ->getAlignment()
                    ->setWrapText(true);
                $event->sheet->getStyle('h2')
                    ->getAlignment()
                    ->setWrapText(true);

                $event->sheet->setCellValue('a2', 'NO');
                $event->sheet->setCellValue('b2', 'NAMA');
                $event->sheet->setCellValue('c2', 'DIVISI - JABATAN');
                $event->sheet->setCellValue('d2', 'ALASAN');
                $event->sheet->setCellValue('e2', 'TANGGAL');
                $event->sheet->setCellValue('f2', 'JAM LEMBUR');
                $event->sheet->setCellValue('h2', 'JUMLAH JAM');
                $event->sheet->setCellValue('i2', 'TOTAL LEMBUR');
                $event->sheet->setCellValue('f3', 'DARI');
                $event->sheet->setCellValue('g3', 'SAMPAI');

                $event->sheet->getColumnDimension('B')->setWidth(20);
                $event->sheet->getColumnDimension('c')->setWidth(15);
                $event->sheet->getColumnDimension('D')->setWidth(50);
                $event->sheet->getColumnDimension('e')->setWidth(17);
                $event->sheet->getColumnDimension('h')->setWidth(10);


                // Mengatur nilai pada sel tertentu
                // $event->sheet->setCellValue('b6', $this->data);
                $index = 4;


                foreach ($this->dataLemburan[$keys[0]] as $key => $lemburanPegawai) {
                    $event->sheet->setCellValue('a' . $index, $key + 1);
                    $event->sheet->getDelegate()->mergeCells('a' . $index . ':a' . (count($lemburanPegawai) - 1 + $index));
                    $event->sheet->getDelegate()->mergeCells('b' . $index . ':b' . (count($lemburanPegawai) - 1 + $index));
                    $event->sheet->getDelegate()->mergeCells('c' . $index . ':c' . (count($lemburanPegawai) - 1 + $index));
                    $event->sheet->getDelegate()->mergeCells('j' . $index . ':j' . (count($lemburanPegawai) - 1 + $index));

                    // array_push($dataRekap, $lemburanPegawai[0]['nama']);

                    $totalNominal = 0;
                    $jumlahJamLembur = 0;
                    $nama = '';
                    $jabatan = '';
                    foreach ($lemburanPegawai as $i => $item) {
                        $jamMulai = '17:00';
                        //menentukan jam
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
                            $absenPulang = $item['absen'][count($item['absen']) - 2];
                            $jamLembur = intval($absenPulang) - intval('17:00');
                        }

                        //mewarnai cell yang merupakan lebur di hari libur
                        if ($item['hari_libur']) {
                            $jamMulai = $absenMasuk;
                            // $jamLembur = intval($absenPulang) - intval($jamMulai);
                            $jamLembur = $this->hitungJamLembur($jamMulai, $absenPulang);
                            $cellStyle = $event->sheet->getStyle('e' . ($index + $i) . ':i' . ($index + $i));
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB(Color::COLOR_GREEN);
                        }
                        $event->sheet->setCellValue('b' . ($index + $i), $item['nama']);
                        $event->sheet->setCellValue('c' . ($index + $i), $item['jabatan']);
                        $event->sheet->setCellValue('d' . ($index + $i), $item['alasan']);
                        $event->sheet->setCellValue('e' . ($index + $i), $item['tanggal']);

                        $event->sheet->getStyle('d' . ($index + $i))
                            ->getAlignment()
                            ->setWrapText(true);

                        $event->sheet->getStyle('a' . ($index + $i) . ':j' . ($index + $i))
                            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('a' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('b' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('c' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('d' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('e' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('f' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('g' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('h' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('i' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                        $event->sheet->getStyle('j' . ($index + $i))
                            ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);



                        $event->sheet->setCellValue('f' . ($index + $i), $jamMulai);
                        $event->sheet->setCellValue('g' . ($index + $i), $absenPulang);
                        $event->sheet->setCellValue('h' . ($index + $i), $jamLembur);

                        $nominal = $item['hari_libur'] ? ($jamLembur * 15000) : ($jamLembur * 10000);
                        $totalNominal += $nominal;
                        $jumlahJamLembur += $jamLembur;
                        $nama = $item['nama'];
                        $jabatan = $item['jabatan'];
                        $event->sheet->setCellValue('i' . ($index + $i), $nominal);
                    }
                    $event->sheet->setCellValue('j' . ($index), $totalNominal);
                    $event->sheet->getStyle('A' . ($index + count($lemburanPegawai) - 1) . ':j' . ($index + count($lemburanPegawai) - 1))
                        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);


                    $event->sheet->getStyle('a4' . ':c' . $index)->getFont()->setBold(true);
                    $event->sheet->getStyle('i4' . ':j' . $index)->getFont()->setBold(true);
                    $index += count($lemburanPegawai);

                    // data rekap pada sheet ke 2
                    array_push($dataRekap, [
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'jumlahJamLembur' => $jumlahJamLembur,
                        'totalNominal' => $totalNominal,
                    ]);
                }


                $event->sheet->getStyle('i4:j' . $index)->getNumberFormat()->setFormatCode('#,##0');

                // Membuat bentuk (shape)
                $kotak1 = new Drawing();
                $kotak1->setName('Shape 1');
                $kotak1->setPath('assets/images/kotak1.png'); // Path gambar bentuk
                $kotak1->setCoordinates('A' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
                $kotak1->setWorksheet($event->sheet->getParent()->getActiveSheet());

                // Menentukan ukuran dan posisi
                $kotak1->setWidth(200); // Lebar bentuk dalam piksel
                $kotak1->setHeight(100); // Tinggi bentuk dalam piksel
                $kotak1->setOffsetX(100); // Posisi horizontal bentuk dalam piksel
                $kotak1->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

                // Membuat bentuk (shape)
                $kotak2 = new Drawing();
                $kotak2->setName('Shape 1');
                $kotak2->setPath('assets/images/kotak2.png'); // Path gambar bentuk
                $kotak2->setCoordinates('d' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
                $kotak2->setWorksheet($event->sheet->getParent()->getActiveSheet());

                // Menentukan ukuran dan posisi
                $kotak2->setWidth(200); // Lebar bentuk dalam piksel
                $kotak2->setHeight(100); // Tinggi bentuk dalam piksel
                $kotak2->setOffsetX(100); // Posisi horizontal bentuk dalam piksel
                $kotak2->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

                // Membuat bentuk (shape)
                $kotak3 = new Drawing();
                $kotak3->setName('Shape 1');
                $kotak3->setPath('assets/images/kotak3.png'); // Path gambar bentuk
                $kotak3->setCoordinates('f' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
                $kotak3->setWorksheet($event->sheet->getParent()->getActiveSheet());

                // Menentukan ukuran dan posisi
                $kotak3->setWidth(200); // Lebar bentuk dalam piksel
                $kotak3->setHeight(100); // Tinggi bentuk dalam piksel
                $kotak3->setOffsetX(100); // Posisi horizontal bentuk dalam piksel
                $kotak3->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

                // dd($dataRekap);


                // $event->sheet->getStyle('A4:j' . $index)
                //     ->getBorders() 
                //     ->getAllBorders()
                //     ->setBorderStyle(Border::BORDER_THIN);

                // Mengatur style pada sel tertentu

                // Menyimpan file
                $event->sheet->getParent()->getActiveSheet()->setTitle($keys[0]);


                //===================================================================================================================
                //
                //                              sheet kedua produksi
                //
                //====================================================================================================================
                // $dataRekap = [];
                // Mendapatkan range sel heading
                for ($i = 1; $i < count($this->dataLemburan); $i++) {
                    $headingRange = 'A2:j3';
                    $event->sheet->getParent()->createSheet();

                    $secondSheet = $event->sheet->getParent()->getSheet(1);

                    // Memanipulasi sel heading
                    $secondSheet->getStyle($headingRange)->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
                        ],
                    ]);
                    $secondSheet->getStyle('a1')->getFont()->setBold(true);
                    $secondSheet->getRowDimension('1')->setRowHeight(30);



                    $secondSheet->mergeCells('A1:j1');
                    $secondSheet->mergeCells('f2:g2');
                    $secondSheet->mergeCells('a2:a3');
                    $secondSheet->mergeCells('b2:b3');
                    $secondSheet->mergeCells('c2:c3');
                    $secondSheet->mergeCells('d2:d3');
                    $secondSheet->mergeCells('e2:e3');
                    $secondSheet->mergeCells('h2:h3');
                    $secondSheet->mergeCells('i2:j3');

                    $secondSheet->getStyle('A2:j3')
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_MEDIUM);

                    $secondSheet->getStyle('A1:z1000')
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $secondSheet->getStyle('c2')
                        ->getAlignment()
                        ->setWrapText(true);
                    $secondSheet->getStyle('h2')
                        ->getAlignment()
                        ->setWrapText(true);

                    $secondSheet->setCellValue('a2', 'NO');
                    $secondSheet->setCellValue('b2', 'NAMA');
                    $secondSheet->setCellValue('c2', 'DIVISI - JABATAN');
                    $secondSheet->setCellValue('d2', 'ALASAN');
                    $secondSheet->setCellValue('e2', 'TANGGAL');
                    $secondSheet->setCellValue('f2', 'JAM LEMBUR');
                    $secondSheet->setCellValue('h2', 'JUMLAH JAM');
                    $secondSheet->setCellValue('i2', 'TOTAL LEMBUR');
                    $secondSheet->setCellValue('f3', 'DARI');
                    $secondSheet->setCellValue('g3', 'SAMPAI');

                    $secondSheet->getColumnDimension('B')->setWidth(20);
                    $secondSheet->getColumnDimension('c')->setWidth(15);
                    $secondSheet->getColumnDimension('D')->setWidth(50);
                    $secondSheet->getColumnDimension('e')->setWidth(17);
                    $secondSheet->getColumnDimension('h')->setWidth(10);


                    // Mengatur nilai pada sel tertentu
                    // $secondSheet->setCellValue('b6', $this->data);
                    $index = 4;
                    foreach ($this->dataLemburan[$keys[$i]] as $key => $lemburanPegawai) {
                        $secondSheet->setCellValue('a' . $index, $key + 1);
                        $secondSheet->mergeCells('a' . $index . ':a' . (count($lemburanPegawai) - 1 + $index));
                        $secondSheet->mergeCells('b' . $index . ':b' . (count($lemburanPegawai) - 1 + $index));
                        $secondSheet->mergeCells('c' . $index . ':c' . (count($lemburanPegawai) - 1 + $index));
                        $secondSheet->mergeCells('j' . $index . ':j' . (count($lemburanPegawai) - 1 + $index));

                        // array_push($dataRekap, $lemburanPegawai[0]['nama']);

                        $totalNominal = 0;
                        $jumlahJamLembur = 0;
                        $nama = '';
                        $jabatan = '';
                        foreach ($lemburanPegawai as $i => $item) {
                            $jamMulai = '17:00';
                            //menentukan jam
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
                                $absenPulang = $item['absen'][count($item['absen']) - 2];
                                $jamLembur = intval($absenPulang) - intval('17:00');
                            }

                            //mewarnai cell yang merupakan lebur di hari libur
                            if ($item['hari_libur']) {
                                $jamMulai = $absenMasuk;
                                // $jamLembur = intval($absenPulang) - intval($jamMulai);
                                $jamLembur = $this->hitungJamLembur($jamMulai, $absenPulang);
                                $cellStyle = $secondSheet->getStyle('e' . ($index + $i) . ':i' . ($index + $i));
                                $cellStyle->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setARGB(Color::COLOR_GREEN);
                            }
                            $secondSheet->setCellValue('b' . ($index + $i), $item['nama']);
                            $secondSheet->setCellValue('c' . ($index + $i), $item['jabatan']);
                            $secondSheet->setCellValue('d' . ($index + $i), $item['alasan']);
                            $secondSheet->setCellValue('e' . ($index + $i), $item['tanggal']);

                            $secondSheet->getStyle('d' . ($index + $i))
                                ->getAlignment()
                                ->setWrapText(true);

                            $secondSheet->getStyle('a' . ($index + $i) . ':j' . ($index + $i))
                                ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('a' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('b' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('c' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('d' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('e' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('f' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('g' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('h' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('i' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                            $secondSheet->getStyle('j' . ($index + $i))
                                ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);



                            $secondSheet->setCellValue('f' . ($index + $i), $jamMulai);
                            $secondSheet->setCellValue('g' . ($index + $i), $absenPulang);
                            $secondSheet->setCellValue('h' . ($index + $i), $jamLembur);

                            $nominal = $item['hari_libur'] ? ($jamLembur * 15000) : ($jamLembur * 10000);
                            $totalNominal += $nominal;
                            $jumlahJamLembur += $jamLembur;
                            $nama = $item['nama'];
                            $jabatan = $item['jabatan'];
                            $secondSheet->setCellValue('i' . ($index + $i), $nominal);
                        }
                        $secondSheet->setCellValue('j' . ($index), $totalNominal);
                        $secondSheet->getStyle('A' . ($index + count($lemburanPegawai) - 1) . ':j' . ($index + count($lemburanPegawai) - 1))
                            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);


                        $secondSheet->getStyle('a4' . ':c' . $index)->getFont()->setBold(true);
                        $secondSheet->getStyle('i4' . ':j' . $index)->getFont()->setBold(true);
                        $index += count($lemburanPegawai);

                        // data rekap pada sheet ke 2
                        array_push($dataRekap, [
                            'nama' => $nama,
                            'jabatan' => $jabatan,
                            'jumlahJamLembur' => $jumlahJamLembur,
                            'totalNominal' => $totalNominal,
                        ]);
                    }

                    $secondSheet->getStyle('i4:j' . $index)->getNumberFormat()->setFormatCode('#,##0');
                    $secondSheet->getParent()->getActiveSheet()->setTitle($keys[$i]);
                }


                // dd($dataRekap);


                // $thirdSheet->getStyle('A4:j' . $index)
                //     ->getBorders() 
                //     ->getAllBorders()
                //     ->setBorderStyle(Border::BORDER_THIN);

                // Mengatur style pada sel tertentu

                // Menyimpan file


                //=============================================================================================================================================
                //
                //                          sheet ketiga ringkasan
                //
                //=============================================================================================================================================
                // Create a new sheet
                $event->sheet->getParent()->createSheet();



                // Set the title of the new sheet
                $event->sheet->getParent()->getSheet(2)->setTitle('RINGKASAN');

                $thirdSheet = $event->sheet->getParent()->getSheet(2);
                $periode = Carbon::parse(date('Y-m', strtotime($this->periode . ' -1 month')) . '-25')->isoFormat('D MMMM Y') . ' - ' . Carbon::parse($this->periode . '-24')->isoFormat('D MMMM Y');

                $thirdSheet->setCellValue('a1', 'DATA LEMBUR KARYAWAN KANTOR & UMUM');
                $thirdSheet->setCellValue('a2', 'PERIODE ' . strtoupper($periode));
                $thirdSheet->mergeCells('A1:e1');
                $thirdSheet->mergeCells('A2:e2');

                $thirdSheet->getColumnDimension('B')->setWidth(20);
                $thirdSheet->getColumnDimension('c')->setWidth(20);
                $thirdSheet->getColumnDimension('D')->setWidth(15);
                $thirdSheet->getColumnDimension('e')->setWidth(20);

                $thirdSheet->getStyle('a1:e2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'], // Misalnya, mengubah latar belakang menjadi kuning
                    ],
                ]);

                $thirdSheet->getStyle('A3:E3')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                $thirdSheet->getStyle('A1:z1000')
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $thirdSheet->getStyle('C3')
                    ->getAlignment()
                    ->setWrapText(true);
                $thirdSheet->getStyle('d3')
                    ->getAlignment()
                    ->setWrapText(true);
                $thirdSheet->getStyle('a3:e3')->getFont()->setBold(true);


                $thirdSheet->setCellValue('a3', 'NO');
                $thirdSheet->setCellValue('b3', 'NAMA');
                $thirdSheet->setCellValue('c3', 'DIVISI - JABATAN');
                $thirdSheet->setCellValue('D3', 'JUMLAH JAM LEMBUR');
                $thirdSheet->setCellValue('E3', 'TOTAL');

                $totalKeseluruhan = 0;
                $index = 4;


                // dd($dataRekap[0]['nama']);

                // Mengurutkan array menggunakan fungsi compareNames
                usort($dataRekap, function ($a, $b) {
                    return strcmp($a['nama'], $b['nama']);
                });
                foreach ($dataRekap as $rekap) {
                    $thirdSheet->setCellValue('a' . $index, $index - 3);
                    $thirdSheet->setCellValue('b' . $index, $rekap['nama']);
                    $thirdSheet->setCellValue('c' . $index, $rekap['jabatan']);
                    $thirdSheet->setCellValue('d' . $index, $rekap['jumlahJamLembur']);
                    $thirdSheet->setCellValue('e' . $index, $rekap['totalNominal']);

                    $thirdSheet->getStyle('A' . $index . ':E' . $index)
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    $totalKeseluruhan += $rekap['totalNominal'];
                    $index++;
                }

                $thirdSheet->setCellValue('a' . $index, 'TOTAL KESELURUHAN');
                $thirdSheet->setCellValue('e' . $index, $totalKeseluruhan);
                $thirdSheet->getStyle('a' . $index . ':e' . $index)->getFont()->setBold(true);
                $thirdSheet->mergeCells('A' . $index . ':d' . $index);

                $thirdSheet->getStyle('A4:e' . $index)->getNumberFormat()->setFormatCode('#,##0');
                $thirdSheet->getStyle('A' . $index . ':e' . $index)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM);

                // Membuat bentuk (shape)
                $kotak1 = new Drawing();
                $kotak1->setName('Shape 1');
                $kotak1->setPath('assets/images/kotak1.png'); // Path gambar bentuk
                $kotak1->setCoordinates('A' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
                $kotak1->setWorksheet($thirdSheet);

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
                $kotak2->setWorksheet($thirdSheet);

                // Menentukan ukuran dan posisi
                $kotak2->setWidth(200); // Lebar bentuk dalam piksel
                $kotak2->setHeight(100); // Tinggi bentuk dalam piksel
                $kotak2->setOffsetY(100); // Posisi vertikal bentuk dalam piksel

                // Membuat bentuk (shape)
                $kotak3 = new Drawing();
                $kotak3->setName('Shape 3');
                $kotak3->setPath('assets/images/kotak3.png'); // Path gambar bentuk
                $kotak3->setCoordinates('d' . ($index + 1)); // Koordinat sel tempat bentuk ditempatkan
                $kotak3->setWorksheet($thirdSheet);

                // Menentukan ukuran dan posisi
                $kotak3->setWidth(200); // Lebar bentuk dalam piksel
                $kotak3->setHeight(100); // Tinggi bentuk dalam piksel
                $kotak3->setOffsetX(50); // Posisi horizontal bentuk dalam piksel
                $kotak3->setOffsetY(100); // Posisi vertikal bentuk dalam piksel
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
