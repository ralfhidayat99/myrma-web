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
        $data['periode'] = $this->getPeriod($request->month);
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
        // dd($data['periode']);

        return view('pages.admin.laporan', $data);
    }

    function getPeriod($tgl)
    {
        // Tanggal awal dan akhir dalam format "Y-m-d"
        $bln = explode(' to ', $tgl);
        if (count($bln) > 1) {
            $tanggal_awal = $bln[0];
            $tanggal_akhir = $bln[1];
        } else {
            $tanggal_awal = $bln[0];
            $tanggal_akhir = $bln[0];
        }

        // Buat objek DateTime untuk tanggal awal dan akhir
        $datetime_awal = new DateTime($tanggal_awal);
        $datetime_akhir = new DateTime($tanggal_akhir);

        // Inisialisasi array untuk menyimpan periode tanggal
        $periode_tanggal = [];

        // Iterasi melalui setiap periode
        while ($datetime_awal <= $datetime_akhir) {
            // Tanggal awal periode
            $tanggal_awal_periode = $datetime_awal->format("Y-m-d");

            // Tanggal akhir periode
            $tanggal_akhir_periode = $datetime_awal->format("Y-m-t");

            // Jika tanggal akhir periode melebihi tanggal akhir rentang, atur tanggal akhir ke tanggal akhir rentang
            if ($datetime_akhir < new DateTime($tanggal_akhir_periode)) {
                $tanggal_akhir_periode = $tanggal_akhir;
            }

            // Tambahkan pasangan tanggal awal dan tanggal akhir ke dalam array
            $periode_tanggal[] = [$tanggal_awal_periode, $tanggal_akhir_periode];

            // Pindah ke bulan berikutnya
            $datetime_awal->modify('first day of next month');
        }



        // dd($periode_tanggal);
        return $periode_tanggal;
    }

    public function cekFileAbsen(Request $request)
    {
        if (!$request->hasFile('absen')) {
            return response()->json("file tidak ditemukan");
        }

        $validateData = $request->validate([
            'filter' => 'required',
            'key' => 'required|numeric'
        ]);
        // return response($request);
        $file = $request->file('absen');
        $filter = explode(',', $validateData['filter']);
        $filterA = $filter[0];
        $filterB = $filter[1];
        // $filterB = DateTime::createFromFormat('d/m/Y', $filter[1]);

        // filter diambil dari form, priode diambil dari file
        $key = intval($validateData['key']);


        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        $periode = $sheet->getCell('C2')->getValue();
        $exp = explode(' ~ ', $periode);
        $periodeAwal = str_replace('/', '-', $exp[0]);
        $periodeAkhir = str_replace('/', '-', $exp[1]);
        // return response($key <= 0 ? true : false);

        // cek di cell d tanggal berapa
        $firstDateCell = $sheet->getCell('d3')->getValue();
        // return response($firstDateCell);

        // return response()->json([
        //     'filterA' => $filterA,
        //     'filterB' => $filterB,
        // ]);
        // return response($periode);
        if (strtotime($filterA) >= strtotime($periodeAwal) && strtotime($filterB) <= strtotime($periodeAkhir)) {
            // return $this->readExcel($filterA, $filterB, $file, true);
            return $this->readExcel($filterA, $filterB, $file, $firstDateCell);
        }
        return response()->json([
            'message' => 'file tidak sesuai',
            'periode' => $periode,
            'filterA' => $filterA,
            'filterB' => $filterB,
            'status' => 400,
            'ok' => 0
        ]);
    }


    public function readExcel($filterA, $filterB, $file, $firstDateCell)
    {
        $leters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'aa', 'ab', 'ac', 'ad', 'ae', 'af', 'ag', 'ah', 'ai', 'aj', 'ak', 'al', 'am', 'an', 'ao', 'ap', 'aq', 'ar', 'as', 'at', 'au', 'av', 'aw', 'ax', 'ay', 'az'];
        // $startDate = DateTime::createFromFormat('Y-m-d', $filterA);
        $startDate = date("Y-m-d", strtotime($filterA));
        $endDate = date("Y-m-d", strtotime($filterB));

        // return response()->json([
        //     'startDate' => $startDate,
        //     'endDate' => $endDate,
        // ]);

        $dataLemburan = Lembur::select('users.name', 'users.departemen', 'users.jabatan', 'users.divisi', 'users.index_absen', 'lemburs.alasan', 'lemburs.is_hari_libur', 'lemburs.tanggal', 'lemburs.jam_mulai', 'lemburs.jam_selesai', 'lemburs.is_lewat_hari')
            ->join('users', 'lemburs.id_user', '=', 'users.id')
            // ->whereMonth('tanggal', '=', $periode)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('approve', 1)
            ->orderBy('tanggal', 'asc')
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
        $userNotFound = [];
        foreach ($groupedData as $key => $lemburanPegawai) {
            $employee = [];
            $groupedData[$key] = [];

            // dd($lemburanPegawai);
            $name = $sheet->getCell('b' . ($lemburanPegawai[0]['index_absen']))->getValue();

            if (str_contains(strtoupper($name), strtoupper($key))) {
                foreach ($lemburanPegawai as $index => $value) {
                    // return response($value['index_absen']);
                    $name = $sheet->getCell('b' . ($value['index_absen']))->getValue();

                    $tanggal = explode("-", $value['tanggal']); // tanggal kapan dia lembur sesuai yang diajukan
                    $absen = $sheet->getCell($leters[$tanggal[2] - intval($firstDateCell) + 3] . $value['index_absen'])->getValue(); // + 3 karna dimulai dari kolom ke 4 (d)

                    // return response($name);
                    $arrAbsen = explode("\n", $absen);
                    // return response($name);
                    array_push($employee, [
                        "nama" => $name,
                        "absen" => $arrAbsen,
                        "tgl" => $value['tanggal'],
                        "alasan" => $value['alasan'],
                        "jabatan" => $value['jabatan'],
                        "divisi" => $value['divisi'],
                        "tanggal" => $this->formatTanggalIndonesia($value['tanggal']),
                        "jam_mulai" => $value['jam_mulai'],
                        "jam_selesai" => $value['jam_selesai'],
                        "lewat_hari" => $value['is_lewat_hari'] == 1 ? true : false,
                        "hari_libur" => $value['is_hari_libur'] == 1 ? true : false
                    ]);
                }
            } else {
                array_push($userNotFound, $key);
            }
            // loop pegawai

            if (count($employee) > 0) {
                array_push($lembur, $employee);
            }
            // dd($lembur);
        }
        // return Excel::download(new LemburanExport($lembur), 'lemburan.xlsx');
        if (count($userNotFound) > 0) {
            return response()->json([
                'message' => 'pengguna ini tidak ada di dalam file absen',
                'userNotFound' => $userNotFound,
                'status' => 400,
                'ok' => 0

            ]);
        }
        return response()->json([
            'first' => $firstDateCell,
            'lembur' => $lembur,
            'message' => 'upload file berhasil',
            'status' => 200,
            'ok' => 1
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
        $jmlPeriode = intval($request->jmlPeriode);
        $laporans = [];
        for ($i = 0; $i < $jmlPeriode; $i++) {
            $laporan1 = json_decode($request['laporan' . $i + 1], true);
            array_push($laporans, $laporan1);
        }
        // array_push($laporans, 'month');
        // return response($laporans);
        // $data = $request->only($laporans);
        // $laporan1 = json_decode($data['laporan1'], true);
        // $laporan2 = json_decode($data['laporan2'], true);
        // dd($laporans);
        $laporanAll = $this->dataMerger($laporans);
        // $laporanAll = array_merge($laporan1, $laporan2);
        // dd($laporanAll);
        $laporanDivided = $this->employeeDivider($laporanAll);
        $periode = $request['month'];
        // dd($laporanDivided);

        return Excel::download(new LemburanExport($laporanDivided, $periode), 'lemburan.xlsx');


        // $laporan = array_merge($validateData['laporan1'], $validateData['laporan2']);
    }

    function dataMerger($laporans)
    {
        $mergedData = [];
        // dd($laporans);

        // Menggabungkan data dari $data1
        foreach ($laporans as $laporan) {
            // dd($laporan);
            foreach ($laporan as $users) {
                // dd(count($users));
                // dd($lembur);
                $nama = $users[0]['nama'];
                // dd($nama);
                if (!isset($mergedData[$nama])) {
                    $mergedData[$nama] = $users;
                } else {
                    $mergedData[$nama] = array_merge($mergedData[$nama], $users);
                }
            }
        }

        // Menggabungkan data dari $data2
        // foreach ($data2 as $items) {
        //     $nama = $items[0]['nama'];
        //     if (!isset($mergedData[$nama])) {
        //         $mergedData[$nama] = $items;
        //     } else {
        //         $mergedData[$nama] = array_merge($mergedData[$nama], $items);
        //     }
        // }


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
