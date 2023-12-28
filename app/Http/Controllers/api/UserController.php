<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = User::latest()->take(30)->get();
        foreach ($users as $value) {
            $value->index_absen = intval($value->index_absen);
        }
        return response($users);
    }

    // Menampilkan formulir untuk membuat pengguna baru
    public function create()
    {
        $data = Supervisor::all();
        return response()->json($data);
    }

    // Menyimpan pengguna baru ke dalam database
    public function store(Request $request)
    {
        // Validasi input pengguna
        $validateData = $request->validate([
            'name' => 'required',
            'jabatan' => 'required',
            'departemen' => 'required',
            'divisi' => 'required',
            'id_atasan' => 'required',
            'username' => 'required|unique:users',
        ]);

        // Membuat pengguna baru
        $validateData['is_admin'] = '0';
        $validateData['password'] = bcrypt('123');
        $newUser = User::create($validateData);
        if ($newUser) {
            return response()->json([
                'ok' => 1,
                'message' => 'User berhasil ditambahkan',
                'newUser' => $newUser
            ]);
        } else {
            return response()->json([
                'ok' => 0,
                'message' => 'User gagal ditambahkan',
            ]);
        }
    }

    public function checkUsernameAvailability(Request $request)
    {
        $username = $request->input('username');

        // Periksa ketersediaan username di dalam tabel 'users'
        $isAvailable = !User::where('username', $username)->exists();

        return response()->json(['is_available' => $isAvailable]);
    }

    // Menampilkan detail pengguna
    public function show($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }
    // Menampilkan detail pengguna
    public function search(Request $request)
    {
        if ($request->name == '') {
            $user = User::latest()->take(30)->get();
        } else {
            $user = User::where('name', 'like', '%' . $request->name . '%')->get();
        }
        foreach ($user as $value) {
            $value->index_absen = intval($value->index_absen);
        }
        return response()->json($user);
    }


    // Memperbarui informasi pengguna
    public function update(Request $request, $id)
    {
        // Validasi input pengguna
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if (!empty($request->input('password'))) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return response($user);
    }
    public function changePassword(Request $request, $id)
    {
        // Validasi input pengguna
        $this->validate($request, [
            'password' => 'required',
        ]);

        $user = User::find($id);
        $user->password = bcrypt($request->input('password'));

        $user->save();

        return response()->json([
            'ok' => 1,
            'message' => 'password berhasil diubah'
        ]);
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'ok' => 0,
                'message' => 'user tidak ditemukan'
            ]);
        }
        $user->delete();

        return response()->json([
            'ok' => 1,
            'message' => 'user berhasil dihapus'
        ]);
    }

    function kalibrasiAbsen(Request $request)
    {
        if (!$request->hasFile('absen')) {
            return response()->json("file tidak ditemukan");
        }
        $file = $request->file('absen');
        $spreadsheet = IOFactory::load($file);

        $sheet = $spreadsheet->getActiveSheet();

        $lastRow = $sheet->getHighestRow();
        $startRow = 5; // baris nama dimulai di baris ke 5

        $unregisteredUser = [];
        for ($index = $startRow; $index <= ($lastRow); $index++) {
            $name = $sheet->getCell('b' . ($index))->getValue(); // nama didalam absen
            $user = User::where('name', $name)->first();
            // return response($user['name']);
            if ($user) {
                if (str_contains(strtoupper($name), strtoupper($user['name']))) {

                    $user->index_absen = $index;
                    $user->save();
                }
            } else {
                array_push($unregisteredUser, $name);
            }
        }

        return redirect()->back()->with('kalibrasi', 'Kalibari berhasil!!,' . count($unregisteredUser) . ' tidak terdaftar');
    }
    function apikalibrasiAbsen(Request $request)
    {
        if (!$request->hasFile('absen')) {
            return response()->json("file tidak ditemukan");
        }
        $file = $request->file('absen');
        $spreadsheet = IOFactory::load($file);

        $sheet = $spreadsheet->getActiveSheet();

        $lastRow = $sheet->getHighestRow();
        $startRow = 5; // baris nama dimulai di baris ke 5

        $unregisteredUser = [];
        for ($index = $startRow; $index <= ($lastRow); $index++) {
            $name = $sheet->getCell('b' . ($index))->getValue(); // nama didalam absen
            $user = User::where('name', $name)->first();
            // return response($user['name']);
            if ($user) {
                if (str_contains(strtoupper($name), strtoupper($user['name']))) {

                    $user->index_absen = $index;
                    $user->save();
                }
            } else {
                array_push($unregisteredUser, $name);
            }
        }

        return response()->json([
            'message' => 'Berhasil di kalibrasi',
            'ok' => 1,

        ]);
    }
}
