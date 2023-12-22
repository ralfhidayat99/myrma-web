<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuti;

class CutiController extends Controller
{
    public function index()
    {
        // Mengambil daftar cuti
        $cuti['unresponded'] = Cuti::select('cutis.*', 'users.name')
            ->join('users', 'cutis.user_id', '=', 'users.id')
            ->where('approve', '0')
            ->get();
        $cuti['declined'] = Cuti::select('cutis.*', 'users.name')
            ->join('users', 'cutis.user_id', '=', 'users.id')
            ->where('approve', '2')
            ->get();
        $responded = Cuti::select('cutis.*', 'users.name')
            ->join('users', 'cutis.user_id', '=', 'users.id')
            ->where('approve', '1')
            ->get();
        $cuti['akanCuti'] = [];
        $cuti['sedangCuti'] = [];

        foreach ($responded as $key => $value) {
            $tglList = explode(',', $value->tgl_cuti);
            $isCuti = false;
            foreach ($tglList as $item) {
                if ($item == date('Y-m-d')) {
                    array_push($cuti['sedangCuti'], $value);
                    $isCuti = true;
                    break;
                }
            }
            if (!$isCuti) {
                array_push($cuti['akanCuti'], $value);
            }
        }

        // Mengembalikan data cuti dalam format JSON
        return response()->json($cuti);
        // return response()->json([
        //     date('Y-m-d')
        // ]);
    }
    public function getByUserId($userId)
    {
        // Mengambil daftar cuti
        $cuti = Cuti::where('user_id', $userId)
            ->get();
        // Mengembalikan data cuti dalam format JSON
        return response()->json($cuti);
        // return response()->json([
        //     date('Y-m-d')
        // ]);
    }


    public function show($id)
    {
        // Mengambil cuti berdasarkan ID
        $cuti = Cuti::find($id);

        // Jika cuti tidak ditemukan, mengembalikan response not found
        if (!$cuti) {
            return response()->json(['message' => 'Cuti not found'], 404);
        }

        // Mengembalikan data cuti dalam format JSON
        return response()->json($cuti);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required',
            'jenis_cuti' => 'required',
            'tgl_cuti' => 'required',
            'tgl_kembali' => 'required',
            'pengganti' => 'required',
            // tambahkan validasi sesuai kebutuhan
        ]);



        // Menyimpan data cuti baru
        $cuti = Cuti::create($request->all());

        // Mengembalikan data cuti yang baru disimpan dalam format JSON
        return response()->json([
            'ok' => 1,
            'message' => 'Pengajuan cuti berhasil dikirim',
            'data' => $cuti
        ]);
    }

    public function update(Request $request, $id)
    {
        // Mengambil cuti berdasarkan ID
        $cuti = Cuti::find($id);

        // Jika cuti tidak ditemukan, mengembalikan response not found
        if (!$cuti) {
            return response()->json(['message' => 'Cuti not found'], 404);
        }

        // Validasi input
        $request->validate([
            'user_id' => 'required',
            'jenis_cuti_id' => 'required',
            'tgl_cuti' => 'required',
            'tgl_kembali' => 'required',
            'keperluan' => 'required',
            'pengganti' => 'required',
            'approve' => 'required',
            'is_known' => 'required',
            // tambahkan validasi sesuai kebutuhan
        ]);

        // Memperbarui data cuti
        $cuti->update($request->all());

        // Mengembalikan data cuti yang diperbarui dalam format JSON
        return response()->json($cuti);
    }

    function takeAction(Request $request, $id)
    {
        $cuti = Cuti::find($id);

        // Jika cuti tidak ditemukan, mengembalikan response not found
        if (!$cuti) {
            return response()->json(['message' => 'Cuti not found'], 404);
        }
        $request->validate([
            'approve' => 'required',
            'user_id' => 'required',
        ]);

        $cuti->approve = $request->approve;
        $cuti->approved_by = $request->user_id;
        $cuti->decline_reason = $request->decline_reason;
        $cuti->save();

        return response()->json([
            'ok' => '1',
            'message' => 'Berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        // Mengambil cuti berdasarkan ID
        $cuti = Cuti::find($id);

        // Jika cuti tidak ditemukan, mengembalikan response not found
        if (!$cuti) {
            return response()->json(['message' => 'Cuti not found'], 404);
        }

        // Menghapus cuti
        $cuti->delete();

        // Mengembalikan response sukses
        return response()->json(['message' => 'Cuti deleted']);
    }
}
