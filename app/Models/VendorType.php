<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorType extends Model
{
    use SoftDeletes;

    protected $table='vendor_types';
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}