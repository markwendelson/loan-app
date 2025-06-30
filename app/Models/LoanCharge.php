<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCharge extends Model
{
    protected $fillable = ['loan_id','advance_interest','service_fee','insurance_fee','notary_fee','reloan_deduction'];

    public $timestamps = false;
}
