<?php

namespace App\Http\Controllers\api;
// app/Http/Controllers/JenisCutiController.php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisCuti;

class JenisCutiController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $jenisCutis = JenisCuti::all();
        return response()->json(['data' => $jenisCutis]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'jml_cuti' => 'required|integer',
        ]);
        $jenisCuti = JenisCuti::where('name', $request->name)->get();
        if (count($jenisCuti) <= 0) {
            $jenisCuti = JenisCuti::create($request->all());
            return response()->json(['ok' => '1',  'data' => $jenisCuti, 'message' => 'Jenis cuti created successfully']);
        } else {
            return response()->json(['ok' => '0', 'message' => 'Jenis cuti sudah ada']);
        }
    }

    // Display the specified resource.
    public function show($id)
    {
        $jenisCuti = JenisCuti::find($id);
        return response()->json(['data' => $jenisCuti]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'jml_cuti' => 'required|integer',
        ]);
        $jenisCuti = JenisCuti::find($id);

        $jenisCuti->update($request->all());

        return response()->json(['ok' => '1', 'data' => $jenisCuti, 'message' => 'JenisCuti updated successfully',]);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $jenisCuti = JenisCuti::find($id);

        $jenisCuti->delete();

        return response()->json(['ok' => '1', 'message' => 'JenisCuti deleted successfully']);
    }
}
