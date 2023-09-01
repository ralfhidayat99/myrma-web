<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\TglFormatter;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index(Request $request, $id)
    {
        $tglFormat = new TglFormatter();
        $perPage = $request->input('per_page', 10);

        $lembur = Lembur::select('lemburs.*', 'users.name', 'users.jabatan', 'users.id_atasan', 'supervisors.name as spv')
            ->join('users', 'users.id',  '=', 'lemburs.id_user')
            ->join('supervisors', 'supervisors.id',  '=', 'users.id_atasan')
            ->where('users.id_atasan', $id)->orderBy('id', 'desc')->paginate($perPage);
        foreach ($lembur as $key => $value) {
            $value->tanggal = $tglFormat->tgl_format($value->tanggal);
            $value->approve = $value->approve . '';
        }

        return response()->json($lembur);
    }
    public function toBeExpired(Request $request, $id)
    {
        $tglFormat = new TglFormatter();
        $twoDayAgo = Carbon::now()->subDay(1); // 2 hari yang lalu

        $lembur = Lembur::select('lemburs.*', 'users.name', 'users.jabatan', 'users.id_atasan', 'supervisors.name as spv')
            ->join('users', 'users.id',  '=', 'lemburs.id_user')
            ->join('supervisors', 'supervisors.id',  '=', 'users.id_atasan')
            ->where('users.id_atasan', '!=', $id)
            ->where('lemburs.approve', '0')
            ->where('lemburs.created_at', '<=', $twoDayAgo)
            ->get();
        foreach ($lembur as $key => $value) {
            $value->tanggal = $tglFormat->tgl_format($value->tanggal);
            $value->approve = $value->approve . '';
        }

        return response()->json($lembur);
    }

    public function show($id)
    {
        $lembur = Lembur::select(['lemburs.*', 'users.name', 'users.jabatan'])
            ->where('lemburs.id', $id)
            ->join('users', 'lemburs.id_user', '=', 'users.id')->get();
        return response()->json($lembur);
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'approve' => 'required',
            'id_user' => 'required',
        ]);

        // $lembur = Lembur::find($id);
        // $lembur->approve = $request->approve;
        // $lembur->save();

        $lembur = Lembur::where('id', $id)->update(['approve' => $request->approve, 'approved_by' => $request->id_user, 'declined_reason' => $request->reason]);

        return response($lembur);
    }
}
