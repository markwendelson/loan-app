<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Savings;
use App\Models\Loan;
use Carbon\Carbon;

class SoaTerm extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $loan = $this->resource;

        $payments = Savings::where('loan_id', $loan->id)
            ->where('transaction_type', 'withdraw')
            ->orderBy('transaction_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $rows = [];
        $running_balance = (float)$loan->final_principal;

        foreach ($payments as $payment) {
            $running_balance = (float)$payment->principal_balance;
            $rows[] = [
                'month' => Carbon::parse($payment->transaction_date)->format('F Y'),
                'insurance' => (float)$payment->insurance,
                'principal_payment' => (float)$payment->principal,
                'principal_running_balance' => $running_balance,
            ];
        }

        // last row — current balance as of today
        $today = Carbon::today();

        $rows[] = [
            'month' => $today->format('F Y'),
            'insurance' => 0.0,
            'principal_payment' => $running_balance,
            'principal_running_balance' => 0.0,
            'is_last_row' => true,
        ];

        $specialLoans = Loan::specialLoan($loan->borrower_id)->get();
        $special_loans_due = [];
        $total_special_balance = 0.0;

        foreach ($specialLoans as $specialLoan) {
            $special_balance = getLoanBalance($specialLoan);
            if ($special_balance > 0) {
                $release_date = Carbon::parse($specialLoan->disbursed_date ?? $specialLoan->loan_date)->startOfDay();
                $today = Carbon::now()->startOfDay();
                $special_days = $release_date->diffInDays($today);

                $advance_interest = $specialLoan->loanCharges->advance_interest ?? 0.0;
                $actual_interest = 0.0;

                if($specialLoan->interest_rate == 5) {
                    $actual_interest = calculateInterest($special_balance, $specialLoan->interest_rate, $special_days);
                    $special_balance += $actual_interest - $advance_interest;
                }

                $special_loans_due[] = [
                    'type' => $specialLoan->description ?? $specialLoan->loanProduct->name,
                    'days' => $special_days,
                    'amount' => $specialLoan->principal,
                    'advance_interest' => $advance_interest,
                    'actual_interest' => $actual_interest,
                    'rebates' => 0.0,
                    'still_due' => $special_balance
                ];
                $total_special_balance += $special_balance;
            }
        }

        $principal_due = getLoanBalance($loan);
        $total_due = $principal_due + $total_special_balance;

        $summary = [
            'principal_due' => $principal_due,
            'special_loans' => $special_loans_due,
            'total_special_balance' => $total_special_balance,
            'total_due' => $total_due
        ];

        return [
            'rows' => $rows,
            'summary' => $summary
        ];
    }
}
