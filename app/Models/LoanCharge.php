<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanCharge extends Model
{
    protected $fillable = [
        'loan_id',
        'advance_interest',
        'service_fee',
        'insurance_fee',
        'notary_fee',
        'reloan_deduction',
        'special_loan_balance',
        'membership_fee',
        'membership_fee_paid',
        'redeem_charges',
        'principal_advance',
        'principal_advance_duration',
        'interest_advance',
        'paid_interest_advance'
    ];

    public $timestamps = false;
}
