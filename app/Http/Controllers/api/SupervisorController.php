<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        return Supervisor::all();
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'departemen' => 'required',
            'lvl' => 'required',
            // Add any necessary password validation rules here
            // Add other fields validation rules here
        ]);

        // Hash the password
        $data['password'] = Hash::make($data['password']);

        // Create and store the Supervisor model
        return Supervisor::create($data);
    }

    // Display the specified resource.
    public function show($id)
    {
        return Supervisor::findOrFail($id);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $supervisor->update($request->all());
        return $supervisor;
    }

    public function updatePassword(Request $request, $id)
    {
        $supervisor = Supervisor::findOrFail($id);

        // Validate the input for password field
        $request->validate([
            'password' => 'required', // You can adjust the minimum password length as needed
        ]);

        // Update the password
        $supervisor->update([
            'password' => bcrypt($request->input('password')),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $supervisor->delete();
        return response()->json(['message' => 'Supervisor deleted successfully']);
    }
}
