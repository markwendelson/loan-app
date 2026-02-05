<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    private $branch;

    public function __construct()
    {
        $this->branch  = Branch::orderBy('name', 'asc')->get();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $search = $request->search['value'];

            $users = User::filter($search)->get();
            $totalRecords = $users->count();
            $mappedUsers = $users->map(function($user) {
                $user['role'] = $user->getRoleNames()->implode(",");
                return $user;
            });

            return response()->json([
                'draw' => intval($request->draw), // Keep track of the request number
                'recordsTotal' => $totalRecords,  // Total records in the database (no filter)
                'recordsFiltered' => $totalRecords, // Filtered records after applying the filters
                'data' => $mappedUsers  // The paginated and formatted data
            ]);
        }

        $branches = $this->branch;

        $roles = Role::where('name','<>','super_admin')->get(['id','name']);

        return view('user.index', compact('roles','branches'));
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
            'role_id' => 'required',
            'email' => 'required|email',
        ]);

        try {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->branch_id = $request->branch_id;
            $user->password = bcrypt('password');
            $user->save();

            $role = Role::findById($request->role_id);
            $user->assignRole($role);
            $user->syncPermissions($role->permissions);

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
            'message' => 'User created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if($user) {
            return response()->json($user);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $branches = $this->branch;
        $permissions = Permission::all();

        return view('user.edit', compact('user','branches','permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->branch_id = $request->branch_id;
        $user->email = $request->email;

        if (isset($request->user_photo)) {
            $path = $request->user_photo->store('client_uploads', 'public');
            $user->photo = $path;
        }

        $user->save();

        return redirect()->back()->with('message', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    public function updatePermission(User $user, Request $request)
    {
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('message', 'User permissions updated successfully');
    }
}
