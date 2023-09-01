<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Atasan;
use App\Models\FcmToken;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        $usr = Supervisor::where('username', $request->username)->first();

        if (!empty($usr)) {
            if (Hash::check($request->password, $usr->password)) {
                $token = Str::random(100);

                $usr->update([
                    'api_token' => $token,
                    'device' => $request->device,
                ]);

                return response()->json(['message' => 'Welcome', 'userdata' => $usr]);
            } else {
                return response()->json(['message' => 'Invalid login credentials'], 401);
            }
        } else {
            return response()->json(['message' => 'Anda Belum Terdaftar'], 401);
        }
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nama' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        $validateData['password'] = bcrypt($validateData['password']);
        Atasan::create($validateData);

        return response()->json(['message' => 'Berhasil terdaftar']);
    }

    function inupToken($token)
    {
        $existingToken = FcmToken::where('token', $token)->first();

        if ($existingToken) {
            return response()->json(['message' => 'Token already exist'], 200);
        }
        FcmToken::create(['token' => $token]);

        return response()->json(['message' => 'Token berhasil disimpan.'], 201);
    }

    function deleteToken($token)
    {
        // Cari token dalam database
        $existingToken = FcmToken::where('token', $token)->first();

        if ($existingToken) {
            $existingToken->delete();

            return response()->json(['message' => 'Token berhasil dihapus.'], 200);
        }

        return response()->json(['message' => 'Token tidak ditemukan dalam database.'], 404);
    }

    public function updateUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'password' => 'required', // You can adjust the minimum password length as needed
        ]);

        // Update the password
        $user->update([
            'password' => bcrypt($request->input('password')),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}
