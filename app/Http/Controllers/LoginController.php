<?php

namespace App\Http\Controllers;

use App\Models\Master;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        // $data = User::all();
        return view('auth/login');
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            // Jika login berhasil
            // if (intval(Auth::user()->is_admin === 1)) {
            //     // Jika pengguna adalah admin
            //     $request->session()->put('id', auth()->user()->id);
            //     $request->session()->put('name', auth()->user()->username);
            //     return redirect('/admin/' . date('Y-m'));
            // } else {
            //     // Jika pengguna adalah pengguna biasa
            //     $spv = Supervisor::find(auth()->user()->id_atasan);
            //     $request->session()->put('id', auth()->user()->id);
            //     $request->session()->put('name', auth()->user()->username);
            //     $request->session()->put('spv', $spv->name);

            //     return redirect()->intended('/');
            // }
            $spv = Supervisor::find(auth()->user()->id_atasan);
            $cutoff = Master::first();
            $request->session()->put('id', auth()->user()->id);
            $request->session()->put('name', auth()->user()->username);
            $request->session()->put('divisi', auth()->user()->divisi);
            $request->session()->put('spv', $spv->name);
            $request->session()->put('cutoff', $cutoff->tgl_cutoff);
            return redirect()->intended('/');
        }

        // Jika login gagal
        return redirect()->back()->withInput()->withErrors(['username' => 'username atau password salah']);
    }

    public function register()
    {
        // $data['atasans'] = User::where('is_admin', '1')->get();
        $data['atasans'] = Supervisor::all();
        // dd($data);
        return view('auth/register', $data);
    }

    public function storeuser(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'jabatan' => 'required',
            'departemen' => 'required',
            'divisi' => 'required',
            'id_atasan' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
        ]);

        $validateData['is_admin'] = '0';
        $validateData['password'] = bcrypt($validateData['password']);
        User::create($validateData);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil!!, Silahkan Login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
