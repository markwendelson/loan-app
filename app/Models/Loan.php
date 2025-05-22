<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['loan_no','borrower_id','loan_product_id','loan_duration','interest_rate','applied_amount','principal'];

    public function scopePending($query)
    {
        return $query->where('status', LoanStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        // if(auth()->user()->can('approve-loan-request-approval')) {
            return $query->where('status', LoanStatus::APPROVE)
                            ->orWhere('status', LoanStatus::REQUEST_APPROVE);
        // }

        // return $query->where('status', LoanStatus::APPROVE);
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', LoanStatus::DECLINE);
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('status', LoanStatus::WITHDRAWN);
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function borrower()
    {
        return $this->belongsTo(Client::class,'borrower_id','id');
    }

    public function loanCharges()
    {
        return $this->hasOne(LoanCharge::class);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('loan_no', 'LIKE', '%'.$search.'%')
                ->orWhere('borrower_id', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }

    public static function generateLoanNumber() : string
    {
        $count = str_pad(Loan::count() + 1,5,'0', STR_PAD_LEFT);
        return 'LN-'.date('Y-m-') . $count;
    }
}
