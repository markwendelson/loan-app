<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Walkin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'walkins';
    protected $fillable = [
        'branch_id',
        'date',
        'reference_no',
        'last_name',
        'first_name',
        'middle_initial',
        'amount_withdrawn',
        'charge',
        'net_proceeds',
        'no_service_charge',
    ];

    protected $casts = [
        'date' => 'date',
        'no_service_charge' => 'boolean',
    ];
}
