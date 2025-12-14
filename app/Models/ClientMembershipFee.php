<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMembershipFee extends Model
{
    protected $fillable = [
        'client_id',
        'reference_model',
        'reference_id',
        'amount_paid',
        'paid_at',
        
    ];

    public $timestamps = false;
}
