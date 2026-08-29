<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Transactions Report</title>
    <style>
        @page { size: legal landscape; margin: 100px 10mm 35px 10mm; }
        body { font-family: sans-serif; font-size: 9px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        table { width: 100%; border-collapse: collapse; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 3px; word-wrap: break-word; }
        th { background-color: #f2f2f2; }
        thead th { text-align: center !important; }
    </style>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Particular</th>
            <th class="text-end">Amount Withdrawn</th>
            <th class="text-end">Cash Payment</th>
            <th class="text-end">Check</th>
            <th class="text-end">Interest</th>
            <th class="text-end">Principal</th>
            <th class="text-end">Insurance</th>
            <th class="text-end">Savings</th>
            <th class="text-end">Payment for Special Loan</th>
            <th class="text-end">Rebates</th>
            <th class="text-end">Others</th>
            <th class="text-end">Change</th>
            <th class="text-end">Balance</th>
            <th>Loan Class</th>
        </tr>
    </thead>
        <tbody>
        @php
            if (!function_exists('formatMoneyPayment')) {
                function formatMoneyPayment($amount) {
                    return (float)$amount == 0 ? '' : number_format($amount, 2);
                }
            }
            if (!function_exists('formatMoneyPaymentWeb')) {
                function formatMoneyPaymentWeb($amount) {
                    return (float)$amount == 0 ? '' : number_format($amount, 2);
                }
            }
            $sum_amt_withdrawn = 0;
            $sum_cash_payment = 0;
            $sum_check = 0;
            $sum_interest = 0;
            $sum_principal = 0;
            $sum_insurance = 0;
            $sum_savings = 0;
            $sum_special = 0;
            $sum_rebates = 0;
            $sum_others = 0;
            $sum_change = 0;
            $payments_by_branch = collect($payments)->groupBy(function($p) {
                return $p->client ? $p->client->branch_id : 0;
            });
        @endphp
        @foreach($payments_by_branch as $branch_id => $branch_payments)
            @php
                $b_sum_amt_withdrawn = 0;
                $b_sum_cash_payment = 0;
                $b_sum_check = 0;
                $b_sum_interest = 0;
                $b_sum_principal = 0;
                $b_sum_insurance = 0;
                $b_sum_savings = 0;
                $b_sum_special = 0;
                $b_sum_rebates = 0;
                $b_sum_others = 0;
                $b_sum_change = 0;
                
                $branch_name = 'UNKNOWN BRANCH';
                if ($branch_id && $branch_id !== 0) {
                    $branch = \App\Models\Branch::find($branch_id);
                    if ($branch) $branch_name = strtoupper($branch->name);
                }
            @endphp
            <tr>
                <td colspan="16" style="font-weight: bold; background-color: #f8f9fa;">BRANCH: {{ $branch_name }}</td>
            </tr>
            @foreach($branch_payments as $payment)
            @php
                $is_atm = (strtolower($payment->mode_of_payment) == 'atm withdraw' || strtolower($payment->mode_of_payment) == 'atm withdrawal');
                $is_cash = strtolower($payment->mode_of_payment) == 'cash';
                $is_check = strtolower($payment->mode_of_payment) == 'check';
                
                $amt_withdrawn = ($payment->transaction_type == 'deposit' && $is_atm) ? $payment->amount : 0;
                $cash_payment = $is_cash ? $payment->amount : 0;
                $check_payment = $is_check ? $payment->amount : 0;
                
                $interest = $payment->interest ?? 0;
                $insurance = $payment->insurance ?? 0;
                $savings = ($payment->transaction_type == 'deposit') ? $payment->amount : 0;
                
                $is_special = $payment->loan && $payment->loan->is_special;
                $principal = $is_special ? 0 : ($payment->principal ?? 0);
                $payment_special = $is_special ? ($payment->principal ?? 0) : 0;
                
                $rebates = 0;
                $others = ($payment->membership_fee ?? 0) + ($payment->principal_advance ?? 0);
                $change = $payment->due_change ?? 0;
                $balance = $payment->principal_balance ?? ($payment->loan ? $payment->loan->final_principal : 0);
                
                $sum_amt_withdrawn += $amt_withdrawn;
                $sum_cash_payment += $cash_payment;
                $sum_check += $check_payment;
                $sum_interest += $interest;
                $sum_principal += $principal;
                $sum_insurance += $insurance;
                $sum_savings += $savings;
                $sum_special += $payment_special;
                $sum_rebates += $rebates;
                $sum_others += $others;
                                                        $sum_change += $change;
                                        
                                        $b_sum_amt_withdrawn += $amt_withdrawn;
                                        $b_sum_cash_payment += $cash_payment;
                                        $b_sum_check += $check_payment;
                                        $b_sum_interest += $interest;
                                        $b_sum_principal += $principal;
                                        $b_sum_insurance += $insurance;
                                        $b_sum_savings += $savings;
                                        $b_sum_special += $payment_special;
                                        $b_sum_rebates += $rebates;
                                        $b_sum_others += $others;
                                        $b_sum_change += $change;
            @endphp
            <tr>
                <td>{{ date('d-M-Y', strtotime($payment->transaction_date)) }}</td>
                <td>{{ $payment->client && $payment->client->details ? $payment->client->details->fullname : 'N/A' }}</td>
                <td>{{ $payment->description }}</td>
                <td class="text-end">{{ formatMoneyPayment($amt_withdrawn) }}</td>
                <td class="text-end">{{ formatMoneyPayment($cash_payment) }}</td>
                <td class="text-end">{{ formatMoneyPayment($check_payment) }}</td>
                <td class="text-end">{{ formatMoneyPayment($interest) }}</td>
                <td class="text-end">{{ formatMoneyPayment($principal) }}</td>
                <td class="text-end">{{ formatMoneyPayment($insurance) }}</td>
                <td class="text-end">{{ formatMoneyPayment($savings) }}</td>
                <td class="text-end">{{ formatMoneyPayment($payment_special) }}</td>
                <td class="text-end">{{ formatMoneyPayment($rebates) }}</td>
                <td class="text-end">{{ formatMoneyPayment($others) }}</td>
                <td class="text-end">{{ formatMoneyPayment($change) }}</td>
                <td class="text-end">{{ formatMoneyPayment($balance) }}</td>
                <td>{{ $payment->loan ? $payment->loan->description : 'Savings' }}</td>
                                    </tr>
                                    @endforeach
                                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                                        <td colspan="3" class="text-end">Subtotal - {{ $branch_name }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_amt_withdrawn) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_cash_payment) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_check) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_interest) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_principal) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_insurance) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_savings) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_special) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_rebates) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_others) }}</td>
                                        <td class="text-end">{{ formatMoneyPayment($b_sum_change) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                @endforeach
                            <tr>
            <th colspan="3" class="text-end">Total</th>
            <th class="text-end">{{ formatMoneyPayment($sum_amt_withdrawn) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_cash_payment) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_check) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_interest) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_principal) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_insurance) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_savings) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_special) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_rebates) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_others) }}</th>
            <th class="text-end">{{ formatMoneyPayment($sum_change) }}</th>
            <th></th>
            <th></th>
        </tr>
    </tbody>
    </table>
    
    
</body>
</html>
