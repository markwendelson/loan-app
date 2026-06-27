<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('role.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        try {
            $role = new Role;
            $role->name = $request->name;
            $role->guard_name = 'web';
            $role->save();

            $role->syncPermissions($request->input('permissions', []));

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate entry'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role = Role::with('permissions')->where('id', $role->id)->first();
        if($role) {
            return response()->json($role);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Role not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->input('permissions', []));

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('delete-role')) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to delete this role.'
            ], 403);
        }

        $role = Role::findById($id);

        // optional protection
        if (in_array($role->name, ['admin', 'super-admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'This role cannot be deleted.'
            ], 403);
        }

        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully.'
        ]);
    }
}
