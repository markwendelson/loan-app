<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanWithdrawRequest extends Model
{
    protected $fillable = ['loan_id','status','requested_by'];
}
