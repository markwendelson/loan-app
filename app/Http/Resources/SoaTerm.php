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
        $running_balance = (float)$loan->principal;

        foreach ($payments as $payment) {
            $running_balance = (float)$payment->principal_balance;
            $rows[] = [
                'month' => Carbon::parse($payment->transaction_date)->format('F Y'),
                'insurance' => (float)$payment->insurance,
                'principal_payment' => (float)$payment->principal,
                'principal_running_balance' => $running_balance,
            ];
        }

        $specialLoans = Loan::specialLoan($loan->borrower_id)->get();
        $special_loans_due = [];
        $total_special_balance = 0.0;

        foreach ($specialLoans as $specialLoan) {
            $special_balance = getLoanBalance($specialLoan);
            if ($special_balance > 0) {
                $release_date = Carbon::parse($specialLoan->disbursed_date ?? $specialLoan->loan_date)->startOfDay();
                $today = Carbon::now()->startOfDay();
                $special_days = $release_date->diffInDays($today);

                $special_loans_due[] = [
                    'type' => $specialLoan->description ?? $specialLoan->loanProduct->name,
                    'days' => $special_days,
                    'amount' => $specialLoan->principal,
                    'advance_interest' => 0.0,
                    'actual_interest' => 0.0,
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
