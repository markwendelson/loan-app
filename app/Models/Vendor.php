<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    protected $table = 'vendors';
    protected $fillable = [
        'name',
        'contact_no',
        'email',
        'fax',
        'website',
        'billing_address',
        'shipping_address',
        'country_id',
        'vendor_type',
        'tin',
        'created_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
