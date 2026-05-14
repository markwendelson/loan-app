<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
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
        'created_at',
        'updated_at',
    ];
}
