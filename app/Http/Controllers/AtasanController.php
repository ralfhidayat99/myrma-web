<?php

namespace App\Http\Controllers;

use App\Exports\LemburanExport;
use App\Models\Atasan;
use App\Models\Lembur;
use App\Models\Supervisor;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;



class AtasanController extends Controller
{
    static function index()
    {
        $data['menu'] = 'dashboard';


        return view('pages/admin/dashboard', $data);
    }
    public function lemburan($month)
    {
        Carbon::setLocale('id');
        $data['menu'] = 'Daftar Lemburan';
        $data['bulanIni'] = $month;
        $periode = explode(' to ', $month);
        // $startDate =   date('Y-m', strtotime($month . " -1 month")) . '-25';
        // $endDate = $month  . '-24';
        $startDate = date("Y-m-d", strtotime($periode[0]));
        if (count($periode) > 1) {
            $endDate = date("Y-m-d", strtotime($periode[1]));
        } else {
            $endDate = $startDate;
        }
        $data['data'] = Lembur::select('users.name', 'lemburs.id', 'lemburs.alasan', 'lemburs.tanggal', 'lemburs.jam_mulai', 'lemburs.jam_selesai', 'lemburs.is_lewat_hari', 'lemburs.approve', 'lemburs.approved_by', 'lemburs.created_at', 'supervisors.name as spv')
            ->join('users', 'lemburs.id_user', '=', 'users.id')
            ->join('supervisors', 'supervisors.id', '=', 'users.id_atasan')
            // ->join('atasan', 'users.atasan_id', '=', 'atasan.id')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]->tanggal = Carbon::parse($value->tanggal)->isoFormat('D MMMM');
            $data['data'][$key]->tgl_dibuat = Carbon::parse($value->created_at)->isoFormat('D MMMM, HH:mm');
            // $data['data'][$key]->tgl_dibuat = Carbon::parse(explode(' ', $value->created_at)[0])->isoFormat('dddd, D MMMM, HH');
            // $data['data'][$key]->jam_mulai = Carbon::parse($value->jam_mulai)->format('H:i');

            if ($data['data'][$key]->approved_by != null) {
                $approver = Supervisor::find($data['data'][$key]->approved_by);
                $data['data'][$key]->approver = $approver->name;
            }
        }

        // dd($data['data']->toArray());
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

        return view('pages/admin/home', $data);
    }

    public function login()
    {
        return view('auth/loginAdmin');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $atasan = Atasan::where('username', $credentials['username'])->first();

        if (!$atasan || !Hash::check($credentials['password'], $atasan->password)) {
            // Jika kredensial tidak valid, tampilkan pesan error atau redirect ke halaman login
            return redirect()->route('atasan.login')->with('loginFailed', 'Kredensial tidak valid');
        }

        $request->session()->regenerate();
        session()->put('user', $atasan);
        // dd(session('user')->nama);

        return redirect('/');
    }

    public function exportToExcel()
    {
        // return Excel::download(new LemburanExport($data), 'lemburan.xlsx');
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
}
