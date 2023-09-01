<?php

namespace App\Http\Controllers;

use App\Exports\LemburanExport;
use App\Exports\TestExprt;
use App\Models\Lembur;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data['menu'] = 'lembur';
        $data['bulanIni'] = $request->month;
        $data['months'] = [
            "2023-01",
            "2023-02",
            "2023-03",
            "2023-04",
            "2023-05",
            "2023-06",
            "2023-07",
            "2023-08",
            "2023-09",
            "2023-10",
            "2023-11",
            "2023-12",
        ];
        return view('pages.admin.laporan', $data);
    }

    public function cekFileAbsen(Request $request)
    {
        if (!$request->hasFile('absen1') && !$request->hasFile('absen2')) {
            return response()->json("file tidak ditemukan");
        }
        $validateData = $request->validate([
            'filter' => 'required',
            'first' => 'required',
        ]);

        $validateData['first'] = $validateData['first'] === 'true' ? true : false;


        // filter diambil dari form, priode diambil dari file
        if ($validateData['first']) {
            $file = $request->file('absen1');
            $filterA = '25/' . date('m/Y', strtotime($validateData['filter'] . ' -1 month')); // dikurangi 1 bulan karna file absen pertama
            $filterB = date('t/m/Y', strtotime($validateData['filter'] . ' -1 month')); // dikurangi 1 bulan karna file absen pertama

        } else {
            $file = $request->file('absen2');
            $filterA = '01/' . date('m/Y', strtotime($validateData['filter']));
            $filterB = '24/' . date('m/Y', strtotime($validateData['filter']));
        }

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $periode = $sheet->getCell('C2')->getValue();
        $exp = explode('~', $periode);
        $periodeAwal = str_replace(' ', '', $exp[0]);
        $periodeAkhir = str_replace(' ', '', $exp[1]);

        if ($filterA == $periodeAwal && $filterB == $periodeAkhir) {
            return $this->readExcel(DateTime::createFromFormat('d/m/Y', $filterA), DateTime::createFromFormat('d/m/Y', $filterB), $file, $validateData['first']);
        }
        return response()->json([
            'message' => 'file tidak sesuai',
            'periode' => $periode,
            'filterA' => $filterA,
            'filterB' => $filterB,
            'status' => 400
        ]);
    }


    public function readExcel($filterA, $filterB, $file, $first)
    {
        $leters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'aa', 'ab', 'ac', 'ad', 'ae', 'af', 'ag', 'ah', 'ai', 'aj', 'ak', 'al', 'am', 'an', 'ao', 'ap', 'aq', 'ar', 'as', 'at', 'au', 'av', 'aw', 'ax', 'ay', 'az'];
        $startDate = date_format($filterA, 'Y-m-d');
        $endDate = date_format($filterB, 'Y-m-d');

        // return response()->json([
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        // ]);

        $dataLemburan = Lembur::select('users.name', 'users.departemen', 'users.jabatan', 'users.divisi', 'lemburs.alasan', 'lemburs.is_hari_libur', 'lemburs.tanggal')
            ->join('users', 'lemburs.id_user', '=', 'users.id')
            // ->whereMonth('tanggal', '=', $periode)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('approve', 1)
            ->get()->toArray();

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $lastRow = $sheet->getHighestRow();
        $startRow = 5; // baris nama dimulai di baris ke 5


        // return response()->json($dataLemburan);
        //mengelompokkan data berdasarkan nama
        $groupedData = array_reduce($dataLemburan, function ($carry, $item) {
            $nama = $item['name'];

            if (!isset($carry[$nama])) {
                $carry[$nama] = [];
            }
            $carry[$nama][] = $item;
            return $carry;
        }, []);

        $lembur = [];
        // loop grup
        // dd($groupedData);
        foreach ($groupedData as $key => $lemburanPegawai) {
            $employee = [];
            $groupedData[$key] = [];
            $tempName = '';

            // dd($lemburanPegawai);
            // return ($lemburanPegawai);

            // loop pegawai
            foreach ($lemburanPegawai as $index => $value) {
                $tempName = $value['name'];
                // loop absensi mencari data absen 
                for ($index = 0; $index <= ($lastRow - $startRow); $index++) {

                    $tanggal = explode("-", $value['tanggal']); // tanggal kapan dia lembur sesuai yang diajukan
                    $name = $sheet->getCell('b' . ($startRow + $index))->getValue();

                    // cek cell mana yang tanggalnya sesuai
                    if (!$first) {
                        $absen = $sheet->getCell($leters[$tanggal[2] + 2] . ($startRow + $index))->getValue(); // +2 karna dimulai dari kolom d
                    } else {

                        // 22 berasal dari tgl cutoff dikurangi 3 (cell absensi ada di kolom ke 4 (D)
                        $absen = $sheet->getCell($leters[$tanggal[2] - 22] . ($startRow + $index))->getValue();
                    }
                    // for ($tanggal=0; $tanggal < 31; $tanggal++) { 
                    // return response()->json([
                    //     'absen' => $absen,
                    //     'fisrt' => $first,
                    //     'name' => $name,
                    //     'tanggal' => $tanggal,
                    // ]);

                    // }

                    // dd($value);
                    if (str_contains(strtoupper($name), strtoupper($value['name']))) {
                        $arrAbsen = explode("\n", $absen);

                        array_push($employee, [
                            "nama" => $name,
                            "absen" => $arrAbsen,
                            "tgl" => $value['tanggal'],
                            "alasan" => $value['alasan'],
                            "jabatan" => $value['jabatan'],
                            "divisi" => $value['divisi'],
                            "tanggal" => $this->formatTanggalIndonesia($value['tanggal']),
                            "hari_libur" => $value['is_hari_libur'] == 1 ? true : false
                        ]);

                        break;
                    }
                }
            }
            if (count($employee) > 0) {
                array_push($lembur, $employee);
            } else {
                return response()->json([
                    'message' => strtoupper($tempName) . ' tidak ada di dalam file absen',
                    'status' => 400
                ]);
            }
            // dd($lembur);
        }
        // return Excel::download(new LemburanExport($lembur), 'lemburan.xlsx');
        return response()->json([
            'first' => $first,
            'lembur' => $lembur,
            'message' => 'upload file berhasil',
            'status' => 200
        ]);
    }

    function formatTanggalIndonesia($tanggal)
    {
        $date = Carbon::parse($tanggal)->locale('id');

        $hari = $date->isoFormat('dddd');
        $tanggal = $date->isoFormat('D');
        $bulan = $date->isoFormat('MMMM');
        $tahun = $date->isoFormat('YYYY');

        $tanggalIndonesia = $hari . ', ' . $tanggal . ' ' . $bulan;

        return $tanggalIndonesia;
    }

    public function generateLaporan(Request $request)
    {
        $data = $request->only('laporan1', 'laporan2', 'month');
        $laporan1 = json_decode($data['laporan1'], true);
        $laporan2 = json_decode($data['laporan2'], true);

        $laporanAll = $this->dataMerger($laporan1, $laporan2);
        // $laporanAll = array_merge($laporan1, $laporan2);
        // dd($laporanAll);
        $laporanDivided = $this->employeeDivider($laporanAll);
        $periode = $data['month'];
        // dd($laporanDivided);

        return Excel::download(new LemburanExport($laporanDivided, $periode), 'lemburan.xlsx');


        // $laporan = array_merge($validateData['laporan1'], $validateData['laporan2']);
    }

    function dataMerger($data1, $data2)
    {
        $mergedData = [];

        // Menggabungkan data dari $data1
        foreach ($data1 as $items) {
            $nama = $items[0]['nama'];
            if (!isset($mergedData[$nama])) {
                $mergedData[$nama] = $items;
            } else {
                $mergedData[$nama] = array_merge($mergedData[$nama], $items);
            }
        }

        // Menggabungkan data dari $data2
        foreach ($data2 as $items) {
            $nama = $items[0]['nama'];
            if (!isset($mergedData[$nama])) {
                $mergedData[$nama] = $items;
            } else {
                $mergedData[$nama] = array_merge($mergedData[$nama], $items);
            }
        }


        return array_values($mergedData);
    }


    function employeeDivider($karyawan)
    {
        $divisiKaryawan = array();

        foreach ($karyawan as $index => $k) {
            // dd($k);
            $divisi = $k[0]['divisi']; // Ambil nilai divisi dari data karyawan
            if (!isset($divisiKaryawan[$divisi])) {
                $divisiKaryawan[$divisi] = array();
            }
            $divisiKaryawan[$divisi][] = $k;
        }


        return $divisiKaryawan;
    }

    function testGenerateLaporan()
    {
        return Excel::download(new TestExprt(), 'test_lemburan.xlsx');
    }

    // $data1 = [
    //     [
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 12,
    //         ],
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 3,
    //         ],
    //     ],
    //     [
    //         [
    //             'nama' => 'didu',
    //             'jumlah' = 4,
    //         ]
    //         [
    //             'nama' => 'didu',
    //             'jumlah' = 16,
    //         ]
    //     ]
    // ]

    // $data2 = [
    //     [
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 7,
    //         ],
    //     ],
    //     [
    //         [
    //             'nama' => 'fino',
    //             'jumlah' = 8,
    //         ]
    //         [
    //             'nama' => 'fino',
    //             'jumlah' = 10,
    //         ]
    //     ]
    // ]

    // $dataBaru = [
    //     [
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 12,
    //         ],
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 3,
    //         ],
    //         [
    //             'nama' => 'arif',
    //             'jumlah' = 7,
    //         ],
    //     ],
    //     [
    //         [
    //             'nama' => 'didu',
    //             'jumlah' = 4,
    //         ]
    //         [
    //             'nama' => 'didu',
    //             'jumlah' = 16,
    //         ]
    //     ],
    //     [
    //         [
    //             'nama' => 'fino',
    //             'jumlah' = 8,
    //         ]
    //         [
    //             'nama' => 'fino',
    //             'jumlah' = 10,
    //         ]
    //     ]

    // ]


}
