<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ReferenceNumberCounter extends Model
{
    protected $table = 'reference_no_counter';
    protected $fillable = [
        'model',
        'branch_id',
        'current_number',
        'prefix',
    ];
}
