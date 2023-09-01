<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Atasan;
use Dotenv\Store\File\Reader;
use Illuminate\Http\Request;

class AtasanController extends Controller
{
    public function index()
    {
        $atasan = Atasan::all()->makeHidden('password');
        return response()->json($atasan);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:atasan|max:255',
            'password' => 'required|string|max:255',
        ]);

        $atasan = new Atasan;
        $atasan->name = $validatedData['name'];
        $atasan->username = $validatedData['username'];
        $atasan->password = bcrypt($validatedData['password']);
        $atasan->save();

        return response()->json([
            'message' => 'atasan created successfully',
            'data' => $atasan
        ], 201);
    }

    public function updatePassword(Request $request, $id)
    {
        return response()->json($request);
        $validatedData = $request->validate([
            'password' => 'required|string|max:255',
        ]);

        $atasan = Atasan::find($id);
        if (!$atasan) {
            return response()->json([
                'message' => 'Atasan not found'
            ], 404);
        }

        $atasan->changePassword($validatedData['password']);

        return response()->json([
            'message' => 'atasan password updated successfully',
            'data' => $atasan
        ], 200);
    }

    public function delete($id)
    {
        $atasan = Atasan::find($id);
        if (!$atasan) {
            return response()->json(['Atasan tidak ditemukan'], 404);
        }

        $atasan->delete();
        return response()->json([''], 200);
    }

    public function absensiPertama(Request $request, $id)
    {
        return response()->json($request);

        // $this->validate($request, [
        //     'nama' => 'required',
        // ]);
        // $request->validate([
        //     'nama' => 'required',
        // ]);

    }
    function tes(Request $request, $id)
    {
        if ($request->password == '1') {
            return response('oke');
        } else {
            return response("fail");
        }
        // return response()->json($request->password);
    }
}
