<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create($request->all());
        return response()->json($role, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->update($request->all());

        return response()->json($role, 200);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(null, 204);
    }
}
