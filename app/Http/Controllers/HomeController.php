<?php

namespace App\Http\Controllers;

use App\Jobs\TglFormatter;
use App\Models\Lembur;
use App\Models\Supervisor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function index(Request $request)
    {
        if (intval(Auth::user()->is_admin === 1)) {
            // return $this->adminDashboard(date('Y-m'));
            return AtasanController::index();
            // return $this->adminHome(date('Y-m'));
        } else {

            return $this->userHome($request);
        }
    }
    public function userHome(Request $request)
    {
        $tglFormat = new TglFormatter();
        $perPage = $request->input('per_page', 5);


        $data['menu'] = 'dashboard';
        $lemburan = Lembur::where('id_user', auth()->user()->id)->orderBy('id', 'desc')->paginate($perPage);
        foreach ($lemburan as $key => $value) {
            $lemburan[$key]->tanggal = $tglFormat->tgl_format($value->tanggal);
        }
        $data['lemburan'] = $lemburan;
        $links = $lemburan->links();

        return view('pages/home', $data);
    }
}
