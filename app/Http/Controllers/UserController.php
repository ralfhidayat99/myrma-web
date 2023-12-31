<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function index()
    {
        $users = User::all();
        $data = [
            'users' => $users,
            'menu' => 'Karyawan'
        ];
        return view('pages.karyawan.index', $data);
    }

    // Menampilkan formulir untuk membuat pengguna baru
    public function create()
    {
        $data = [
            'menu' => 'Karyawan',
            'supervisor' => Supervisor::all()
        ];
        return view('pages.karyawan.create', $data);
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
        User::create($validateData);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat!');
    }

    // Menampilkan detail pengguna
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', ['user' => $user]);
    }

    // Menampilkan formulir untuk mengedit pengguna
    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit', ['user' => $user]);
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

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
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

        return response()->json($unregisteredUser);
    }
}
