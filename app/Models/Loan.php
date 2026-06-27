<?php

namespace App\Models;

use App\Enums\LoanStatus;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Loan extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'branch_id',
        'loan_date',
        'loan_no',
        'borrower_id',
        'loan_product_id',
        'loan_duration',
        'interest_rate',
        'applied_amount',
        'principal',
        'repayment_order',
        'is_special',
        'description',
        'is_reloan',
        'reference_loan_no',
        'is_additional_loan',
        'disbursement_reference'
    ];

    protected $appends = [
        'final_principal',
        'final_advance_interest',
        'final_monthly_amortization',
        'final_insurance_monthly',
        'final_insurance_yearly',
        'membership_fee'
    ];

    protected $casts = [
        'repayment_order' => 'array',
    ];

    public function scopePending($query)
    {
        if(auth()->user()->can('approve-secondary-approval')) {
            return $query->where('status', LoanStatus::REQUEST_APPROVE)
                    ->orWhere('status', LoanStatus::PENDING);
        }
        return $query->where('status', LoanStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        // if(auth()->user()->can('approve-loan-request-approval')) {
            return $query->where('status', LoanStatus::APPROVE);
                            // ->orWhere('status', LoanStatus::REQUEST_APPROVE);
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

    public function scopeClosed($query)
    {
        return $query->where('status', LoanStatus::CLOSED);
    }

    public function scopeSpecialLoan($query,$borrower_id)
    {
        return $query->where('borrower_id', $borrower_id)
                    ->where('is_special',1)
                    ->where('status', LoanStatus::APPROVE);;
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

    public function loanFees()
    {
        return $this->hasOne(LoanFee::class);
    }

    public function loanDisbursementDetails()
    {
        return $this->hasMany(LoanDisbursementDetails::class);
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

    public static function generateLoanNumber($loan_date) : string
    {
        $date = new DateTime($loan_date);
        $date_format = $date->format('Y-m');

        $count = str_pad(Loan::count() + 1,5,'0', STR_PAD_LEFT);
        return "LN-{$date_format}-{$count}";
    }

    public function payments()
    {
        return $this->hasMany(Savings::class);
    }

    public function finalPrincipal(): Attribute
    {
        return Attribute::get(function () {
            $referencingLoans = self::where('reference_loan_no', $this->loan_no)
                                ->whereNotIn('status',[
                                    LoanStatus::PENDING,
                                    LoanStatus::DECLINE,
                                    LoanStatus::WITHDRAWN,
                                    LoanStatus::REQUEST_APPROVE
                                ])->get();

            return $referencingLoans->sum('principal') + $this->principal;
        });
    }

    public function finalAdvanceInterest(): Attribute
    {
        return Attribute::get(function () {
            $referencingLoanIds = self::where('reference_loan_no', $this->loan_no)->where('is_special',0)->pluck('id')->toArray();
            $loanIds = array_merge($referencingLoanIds, [$this->id]);

            return LoanCharge::whereIn('loan_id', $loanIds)->sum('advance_interest');
        });
    }

    public function finalMonthlyAmortization(): Attribute
    {
        return Attribute::get(function () {
            $referencingLoanIds = self::where('reference_loan_no', $this->loan_no)->pluck('id')->toArray();
            $loanIds = array_merge($referencingLoanIds, [$this->id]);

            return LoanFee::whereIn('loan_id', $loanIds)->sum('monthly_amortization');
        });
    }

    public function finalInsuranceMonthly(): Attribute
    {
        return Attribute::get(function () {
            $amount = getLoanBalance($this);
            $insurance_fee =  InsuranceFeeSchedule::where('min_amount','<=',$amount)
                    ->where('max_amount','>=',$amount)->first();

            return $insurance_fee->monthly_fee ?? 0;
        });
    }

    public function finalInsuranceYearly(): Attribute
    {
        return Attribute::get(function () {
            $amount = getLoanBalance($this);
            $insurance_fee =  InsuranceFeeSchedule::where('min_amount','<=',$amount)
                    ->where('max_amount','>=',$amount)->first();

            return $insurance_fee->annual_fee ?? 0;
        });
    }

    public function membershipFee(): Attribute
    {
        return Attribute::get(function () {
            $membership_fee =  ClientMembershipFee::where([
                'reference_model' => Loan::class,
                'reference_id' => $this->id,
            ])->first();

            return $membership_fee->amount_paid ?? 0;
        });
    }

    public function loanWithdrawRequests()
    {
        return $this->hasMany(LoanWithdrawRequest::class, 'loan_id');
    }

}
