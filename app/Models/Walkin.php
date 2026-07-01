<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Walkin extends Model
{
    use HasFactory;

    // Optional if your table name follows Laravel convention (plural form)
    protected $table = 'walkins';

    // Fields you can mass-assign (fillable)
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

     // Cast 'date' field to a Carbon instance
    protected $casts = [
        'date' => 'date',
        'no_service_charge' => 'boolean',
    ];
}
