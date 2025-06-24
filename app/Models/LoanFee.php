<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanFee extends Model
{
    protected $fillable = ['loan_id','insurance_monthly_fee','insurance_annual_fee','monthly_amortization'];

    public $timestamps = false;
}
