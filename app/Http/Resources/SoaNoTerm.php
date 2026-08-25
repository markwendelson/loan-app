<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Savings;
use App\Models\Loan;
use Carbon\Carbon;

class SoaNoTerm extends JsonResource
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

        $advance_interest = (float)($loan->loanCharges->advance_interest ?? 0);
        $reloan_deduction = (float)($loan->loanCharges->reloan_deduction ?? 0);
        $insurance_fee = (float)($loan->loanCharges->insurance_fee ?? 0);
        $membership_fee = (float)($loan->loanCharges->membership_fee ?? 0);
        $service_fee = (float)($loan->loanCharges->service_fee ?? 0);

        $rows[] = [
            'date' => Carbon::parse($loan->disbursed_date ?? $loan->loan_date)->format('M d, Y'),
            'salary_date' => '',
            'number_of_days' => '',
            'principal_loan' => (float)$loan->principal,
            'interest_charge' => '',
            'advance_interest' => $advance_interest,
            'deducted_to_loan_renewal' => $reloan_deduction,
            'interest' => 0.0,
            'payment_principal' => 0.0,
            'ins' => $insurance_fee,
            'savings' => $membership_fee,
            'principal_balance' => (float)$loan->final_principal,
            'remarks' => 'RELEASE',
            'service_fee' => $service_fee
        ];

        // $prev_date = Carbon::parse($loan->disbursed_date ?? $loan->loan_date)->startOfDay();
        $prev_balance = (float)$loan->final_principal;
        $total_interest_charged = 0.0;
        $total_interest_collected = 0.0;
        $total_principal_payment = 0.0;
        $total_ins = $insurance_fee;
        $total_savings = $membership_fee;

        foreach ($payments as $k => $payment) {
            $start_date = Carbon::parse($payment->interest_start_date)->startOfDay();
            $end_date = Carbon::parse($payment->interest_end_date)->startOfDay();
            $days = $start_date->diffInDays($end_date);
            $interest_rate = (float)$loan->interest_rate / 100;
            $interest_charge_amount = round(($interest_rate / 30) * $days * $prev_balance);
            // $interest_rate_percent = ($loan->interest_rate / 30) * $days;
            // $interest_charge_amount = ($loan->interest_rate / 100 / 30) * $days * $prev_balance;
            $total_interest_charged += $interest_charge_amount;

            $interest_paid = (float)$payment->interest;
            $principal_paid = (float)$payment->principal;
            $ins_paid = (float)$payment->insurance;
            $savings_paid = (float)$payment->membership_fee;

            $total_interest_collected += $interest_paid;
            $total_principal_payment += $principal_paid;
            $total_ins += $ins_paid;
            $total_savings += $savings_paid;

            $curr_balance = (float)$payment->principal_balance;

            $rows[] = [
                'date' => '',
                'salary_date' => Carbon::parse($payment->transaction_date)->format('M d, Y'),
                'number_of_days' => $days,
                'principal_loan' => 0.0,
                'interest_charge' => number_format($interest_charge_amount, 2),
                'advance_interest' => 0.0,
                'deducted_to_loan_renewal' => 0.0,
                'interest' => $interest_paid,
                'payment_principal' => $principal_paid,
                'ins' => $ins_paid,
                'savings' => $savings_paid,
                'principal_balance' => $curr_balance,
                'remarks' => $payment->description ?? $payment->notes ?? '',
                'service_fee' => 0.0
            ];

            $prev_balance = $curr_balance;
        }


        // last row
        $today = Carbon::today();

        $last_payment = $payments->last();

        $last_date = $last_payment
            ? Carbon::parse($last_payment->interest_end_date)->addDay()
            : Carbon::parse($loan->disbursed_date ?? $loan->loan_date)->addDay();

        $days = $last_date->diffInDays($today);

        $interest_rate = (float)$loan->interest_rate / 100;
        $interest_charge_amount = round(($interest_rate / 30) * $days * $prev_balance);
        $total_interest_charged += $interest_charge_amount;

        $rows[] = [
            'date' => '',
            'salary_date' => $today->format('M d, Y'),
            'number_of_days' => $days,
            'principal_loan' => 0.0,
            'interest_charge' => number_format($interest_charge_amount, 2),
            'advance_interest' => 0.0,
            'deducted_to_loan_renewal' => 0.0,
            'interest' => $interest_charge_amount,
            'payment_principal' => $prev_balance,
            'ins' => 0.0,
            'savings' => 0.0,
            'principal_balance' => 0.0,
            'remarks' => 'Current Balance',
            'service_fee' => 0.0,
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
        $total_interest_collected_total = $total_interest_collected + $advance_interest;
        $total_interest_charged_total = $total_interest_charged + $advance_interest;
        $rebates = 0.0;

        $interest_deficit = max(0, $total_interest_charged_total - $total_interest_collected_total);
        $total_amount_due = $principal_due + $interest_deficit;
        $final_total_due = $total_amount_due + $total_special_balance;

        $summary = [
            'total_interest_charged' => $total_interest_charged_total,
            'less_advance_interest' => $advance_interest,
            'total_interest_collected' => $total_interest_collected_total,
            'rebates' => $rebates,
            'principal_due' => $principal_due,
            'interest_deficit' => $interest_deficit,
            'total_amount_due' => $total_amount_due,
            'special_loans' => $special_loans_due,
            'total_special_balance' => $total_special_balance,
            'final_total_due' => $final_total_due
        ];

        return [
            'rows' => $rows,
            'summary' => $summary
        ];
    }
}
