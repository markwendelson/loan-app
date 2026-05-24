<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchUser extends Model
{
    protected $fillable = [
        'user_id',
        'branch_id',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
