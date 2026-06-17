<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'current_branch_id',
        'email',
        'password',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeCurrentBranch($query)
    {
        $user = auth()->user();

        if (!$user || $user->hasAnyRole(['admin', 'super-admin'])) {
            return $query;
        }

        return $query->where('current_branch_id', $user->current_branch_id);
    }

    public function scopeNotsuper_admin($query)
    {
        return $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_admin');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('email', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function assignedBranch()
    {
        return $this->hasMany(BranchUser::class);
    }

    public function addNewRole(int $roleId, ?int $branchId = null)
    {
        $branchId ??= $this->current_branch_id;

        DB::table('model_has_roles')
            ->updateOrInsert([
                'role_id' => $roleId,
                'model_type' => self::class,
                'model_id' => $this->id,
                'branch_id' => $branchId,
            ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function removeRole(int $roleId, ?int $branchId = null)
    {
        $branchId ??= $this->current_branch_id;

        if ($branchId == $this->current_branch_id) {
            return false;
        }

        DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', self::class)
            ->where('model_id', $this->id)
            ->where('branch_id', $branchId)
            ->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        return true;
    }

    public function syncBranchPermissions(array $permissionIds, ?int $branchId = null)
    {
        $branchId ??= $this->current_branch_id;

        DB::table('model_has_permissions')
            ->where('model_id', $this->id)
            ->where('model_type', self::class)
            ->where('branch_id', $branchId)
            ->delete();

        foreach ($permissionIds as $permissionId) {

            DB::table('model_has_permissions')
                ->insert([
                    'permission_id' => $permissionId,
                    'model_type' => self::class,
                    'model_id' => $this->id,
                    'branch_id' => $branchId,
                ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function getBranchPermissionName(?int $branchId = null)
    {
        $branchId ??= $this->current_branch_id;

        return DB::table('model_has_permissions')
                    ->join(
                        'permissions', 'permissions.id', '=', 'model_has_permissions.permission_id'
                    )
                    ->where('model_has_permissions.model_id', $this->id)
                    ->where('model_has_permissions.model_type', self::class)
                    ->where('model_has_permissions.branch_id', $branchId)
                    ->pluck('permissions.name');

    }

}
