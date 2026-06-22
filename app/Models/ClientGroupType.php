<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;



class ClientGroupType extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'client_group_types';
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
