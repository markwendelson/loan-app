<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $branch = [];

    public function __construct()
    {
        $this->branch  = Branch::orderBy('name', 'asc')->get();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {      
        $filterData = $request->all();
        // $users = User::currentBranch()->notsuper_admin()->paginate($filterData['entries'] ?? 10)->appends($filterData);

        // dd($users->toArray());
        $query = User::currentBranch()
            ->select('users.*', 'branch.name as branch_name')
            ->notsuper_admin()
            ->leftJoin('branch', 'branch.id', '=', 'users.current_branch_id')
            ->whereNotIn('users.id', [auth()->id()]);
        //branch_id
        if(isset($filterData['branch_id']) && $filterData['branch_id']) {
            $query->where('current_branch_id', $filterData['branch_id']);
        }

        //q
        if(isset($filterData['q']) && $filterData['q']) {
            $query->where(function ($query) use ($filterData) {
                $query->orWhere('users.name', 'like', '%' . $filterData['q'] . '%')
                    ->orWhere('users.email', 'like', '%' . $filterData['q'] . '%');
            });
        }

        $users = $query->paginate($filterData['entries'] ?? 10)->appends($filterData);

        $users->getCollection()->transform(function ($user) {
            $user->role = getUserRoles($user, null);
            return $user;
        });

        if($request->ajax()) {
            $search = $request->search['value'];

            $users = User::currentBranch()->notsuper_admin()->get();

            $totalRecords = $users->count();
            $mappedUsers = $users->map(function($user) {
                $user['role'] = getUserRoles($user, null);
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

        return view('user.index', compact(
            'roles',
            'branches',
            'filterData',
            'users'
        ));
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
            DB::beginTransaction();
            $exists = User::where('email', $request->email)->orWhere('name', $request->name)->first();
            if($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Account already exists.',
                    'id' => $exists->id
                ], 409);
            }

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->current_branch_id = $request->branch_id;
            $user->password = bcrypt('password');
            $user->save();

            BranchUser::create([
                'user_id' => $user->id,
                'branch_id' => $user->current_branch_id,
                'is_active' => $request->status,
                'created_at' => now()
            ]);

            $role = Role::withoutGlobalScopes()->find($request->role_id);
            $user->addNewRole($role->id);

            $permissions = $role->permissions->pluck('id')->toArray();
            $user->syncBranchPermissions($permissions);

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. '. $e->getMessage()
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
    public function show(Request $request, User $user)
    {
        if($request->ajax()) {
            if($user) {
                return response()->json($user);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $branches = $this->branch;
        $permissions = Permission::all();
        $roles = Role::where('name','<>','super_admin')->get(['id','name']);
        $branch_roles = [];
        foreach($branches as $key => $branch) {
            $branch_roles[$key] = [
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'branch' => $branch->name,
                'roles' => getUserRoles($user, $branch->id)
            ];
        }

        return view('user.edit', compact('user','branches','permissions','branch_roles','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->current_branch_id = $request->branch_id;
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

    public function getBranchPermission(User $user, Branch $branch ,Request $request)
    {
        if($request->ajax()) {
            $assignedPermissionNames = $user->getBranchPermissionName($branch->id)->toArray();
            return view('user.permissions', compact('assignedPermissionNames','user','branch'));
        }

        abort(404);
    }

    public function updateBranchPermission(User $user, Branch $branch ,Request $request)
    {
        $permissions = Permission::whereIn('name',$request->permissions)->pluck('id')->toArray();
        $user->syncBranchPermissions($permissions, $branch->id);

        return redirect()->back()->with('message', 'User permissions updated successfully');
    }

    public function assignRole(User $user, Request $request)
    {
        $branchId = $request->branch_id;
        $roleId = $request->role_id;

        $branchUser = BranchUser::where([
                            'user_id' => $user->id,
                            'branch_id' => $branchId
                        ])->first();

        if(!$branchUser) {
            BranchUser::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'is_active' => 1,
                'created_at' => now()
            ]);
        } else {
            // change role
            $current_role = DB::table('model_has_roles')
                                    ->where('model_type', User::class)
                                    ->where('model_id', $user->id)
                                    ->where('branch_id', $branchId)
                                    ->first();

            if (isset($current_role->role_id) && $current_role->role_id == $roleId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User already exists in branch.',
                ],405);
            }

            // update or insert role
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $user->id)
                ->where('branch_id', $branchId)
                ->update([
                    'role_id' => $roleId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User role updated successfully',
            ]);
        }

        $role = Role::withoutGlobalScopes()->find($roleId);
        $user->addNewRole($roleId, $branchId);

        $permissions = $role->permissions->pluck('id')->toArray();
        $user->syncBranchPermissions($permissions);

        return response()->json([
            'status' => 'success',
            'message' => 'User role added successfully',
        ]);
    }

    public function deleteBranchRole(User $user, int $branchId)
    {
        $branchUser = BranchUser::where([
            'user_id' => $user->id,
            'branch_id' => $branchId
        ])->first();

        if($branchUser->branch_id == $user->current_branch_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete user current branch and role.',
            ],500);
        }

        if($branchUser) {
            DB::beginTransaction();
            $branchUser->delete();

            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $user->id)
                ->where('branch_id', $branchId)
                ->delete();

            DB::table('model_has_permissions')
                            ->where('model_type', User::class)
                            ->where('model_id', $user->id)
                            ->where('branch_id', $branchId)
                            ->delete();

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            DB::commit();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User role deleted successfully',
        ]);
    }

    public function updateUserBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branch,id',
        ]);

        $user = auth()->user();

        $user->update([
            'current_branch_id' => $request->branch_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $response = [];

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ], [
            'confirm_password.same' => 'Passwords do not match!',
        ]);

        if($validator->fails()){
            $allErrors = implode(',', $validator->errors()->all());
            $response = [
                'status' => 'fail',
                'message' => 'Validation Failed!: ' . $allErrors,
            ];

            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            User::where('id', $request->id)
                ->update([
                    'password' => bcrypt($request->password)
                ]);
            DB::commit();

            $response = [
                'status' => 'success',
                'message' => 'Password reset successfully.',
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Error resetting password: ' . $e->getMessage()
            ], 500);
        }

        return response()->json($response);

    }
}
