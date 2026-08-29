<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Transactions Report</title>
    <style>
        @page { size: legal landscape; margin: 100px 10mm 35px 10mm; }
        body { font-family: sans-serif; font-size: 9px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        table { width: 100%; border-collapse: collapse; table-layout: auto; }
        th, td { border: 1px solid #000; padding: 3px; word-wrap: break-word; }
        th { background-color: #f2f2f2; }
        thead th, thead td { text-align: center !important; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th class="text-end">Interest Rate</th>
                <th class="text-end">No. of months for interest</th>
                <th class="text-end">Principal Loan</th>
                <th class="text-end">Special Loan</th>
                <th class="text-end">Interest</th>
                <th class="text-end">Service Fee</th>
                <th class="text-end">Notary Fee</th>
                <th class="text-end">Insurance Fee</th>
                <th class="text-end">Old Loan</th>
                <th class="text-end">Deficit in Interest</th>
                <th class="text-end">Savings</th>
                <th class="text-end">Payment for Special Loan</th>
                <th class="text-end">Rebates</th>
                <th class="text-end">Others</th>
                <th class="text-end">Redeem</th>
                <th class="text-end">Net Proceeds</th>
                <th>Loan Classes</th>
                <th>Loan Class Payment</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
                            @php 
                    $sum_principal = 0;
                    $sum_special = 0;
                    $sum_interest = 0;
                    $sum_service = 0;
                    $sum_notary = 0;
                    $sum_insurance = 0;
                    $sum_old_loan = 0;
                    $sum_deficit = 0;
                    $sum_savings = 0;
                    $sum_payment_special = 0;
                    $sum_rebates = 0;
                    $sum_others = 0;
                    $sum_redeem = 0;
                    $sum_net_proceeds = 0;

                    if (!function_exists('formatMoney')) {
                        function formatMoney($amount) {
                            return (float)$amount == 0 ? '' : number_format($amount, 2);
                        }
                    }

                    // Pre-process loans to merge special loans into their reference loans
                    $merged_loans = [];
                    $special_loans = [];

                    foreach($loans as $loan) {
                        if ($loan->is_special) {
                            $special_loans[] = $loan;
                        } else {
                            $merged_loans[$loan->id] = $loan;
                        }
                    }

                    foreach($special_loans as $s_loan) {
                        $ref_id = null;
                        if ($s_loan->reference_loan_no) {
                            foreach($merged_loans as $id => $m_loan) {
                                if ($m_loan->loan_no == $s_loan->reference_loan_no) {
                                    $ref_id = $id;
                                    break;
                                }
                            }
                        }
                        if (!$ref_id) {
                            foreach($merged_loans as $id => $m_loan) {
                                if ($m_loan->borrower_id == $s_loan->borrower_id) {
                                    $ref_id = $id;
                                    break;
                                }
                            }
                        }
                        
                        if ($ref_id) {
                            $merged_loans[$ref_id]->merged_s_loan = $s_loan;
                        } else {
                            $merged_loans['s'.$s_loan->id] = $s_loan;
                        }
                    }
                    $loans_by_branch = collect($merged_loans)->groupBy('branch_id');
                @endphp
                @foreach($loans_by_branch as $branch_id => $branch_loans)
                    @php
                        $b_sum_principal = 0;
                        $b_sum_special = 0;
                        $b_sum_interest = 0;
                        $b_sum_service = 0;
                        $b_sum_notary = 0;
                        $b_sum_insurance = 0;
                        $b_sum_old_loan = 0;
                        $b_sum_deficit = 0;
                        $b_sum_savings = 0;
                        $b_sum_payment_special = 0;
                        $b_sum_rebates = 0;
                        $b_sum_others = 0;
                        $b_sum_redeem = 0;
                        $b_sum_net_proceeds = 0;
                        
                        $branch_name = 'UNKNOWN BRANCH';
                        if ($branch_id && $branch_id !== 0) {
                            $branch = \App\Models\Branch::find($branch_id);
                            if ($branch) $branch_name = strtoupper($branch->name);
                        }
                    @endphp
                    <tr>
                        <td colspan="21" style="font-weight: bold; background-color: #f8f9fa;">BRANCH: {{ $branch_name }}</td>
                    </tr>
                    @foreach($branch_loans as $loan)
                    @php 
                        $charges = $loan->loanCharges;
                        
                        $principal_loan = $loan->is_special ? 0 : $loan->principal;
                        $special_loan = $loan->is_special ? $loan->principal : (isset($loan->merged_s_loan) ? $loan->merged_s_loan->principal : 0);
                        
                        $interest = $charges->advance_interest ?? 0;
                        $service_fee = $charges->service_fee ?? 0;
                        $notary_fee = $charges->notary_fee ?? 0;
                        $insurance_fee = $charges->insurance_fee ?? 0;
                        $payment_special = $charges->special_loan_balance ?? 0;
                        $old_loan = ($charges->reloan_deduction ?? 0) - $payment_special;
                        $deficit = 0;
                        $savings = $loan->payments->sum("amount");
                        $rebates = 0;
                        $others = ($charges->membership_fee_paid ?? 0) + ($charges->principal_advance ?? 0) + ($charges->paid_interest_advance ?? 0);
                        $redeem = $charges->redeem_charges ?? 0;

                        // Add merged special loan charges if any
                        if (isset($loan->merged_s_loan)) {
                            $s_charges = $loan->merged_s_loan->loanCharges;
                            if ($s_charges) {
                                $interest += $s_charges->advance_interest ?? 0;
                                $service_fee += $s_charges->service_fee ?? 0;
                                $notary_fee += $s_charges->notary_fee ?? 0;
                                $insurance_fee += $s_charges->insurance_fee ?? 0;
                                $old_loan += $s_charges->reloan_deduction ?? 0;
                                $payment_special += $s_charges->special_loan_balance ?? 0;
                                $others += ($s_charges->membership_fee_paid ?? 0) + ($s_charges->principal_advance ?? 0) + ($s_charges->paid_interest_advance ?? 0);
                                $redeem += $s_charges->redeem_charges ?? 0;
                            }
                        }
                        
                        $deductions = $interest + $service_fee + $notary_fee + $insurance_fee + $old_loan + $deficit + $payment_special + $rebates + $others + $redeem;
                        $net_proceeds = $loan->principal + (isset($loan->merged_s_loan) ? $loan->merged_s_loan->principal : 0) - $deductions;
                        
                        $remarks = $loan->description;
                        if ($loan->reference_loan_no) {
                            $remarks .= $remarks ? ' (Reloan REF: ' . $loan->reference_loan_no . ')' : 'Reloan REF: ' . $loan->reference_loan_no;
                        }
                        if (isset($loan->merged_s_loan)) {
                            $remarks .= $remarks ? ' (Merged Special Loan)' : '(Merged Special Loan)';
                        }

                        $sum_principal += $principal_loan;
                        $sum_special += $special_loan;
                        $sum_interest += $interest;
                        $sum_service += $service_fee;
                        $sum_notary += $notary_fee;
                        $sum_insurance += $insurance_fee;
                                                                $sum_old_loan += $old_loan;
                                        $sum_deficit += $deficit;
                                        $sum_savings += $savings;
                                        $sum_payment_special += $payment_special;
                                        $sum_rebates += $rebates;
                                                                $sum_others += $others;
                                        $sum_redeem += $redeem;
                                        $sum_net_proceeds += $net_proceeds;
                                        
                                        $b_sum_principal += $principal_loan;
                                        $b_sum_special += $special_loan;
                                        $b_sum_interest += $interest;
                                        $b_sum_service += $service_fee;
                                        $b_sum_notary += $notary_fee;
                                        $b_sum_insurance += $insurance_fee;
                                        $b_sum_old_loan += $old_loan;
                                        $b_sum_deficit += $deficit;
                                        $b_sum_savings += $savings;
                                        $b_sum_payment_special += $payment_special;
                                        $b_sum_rebates += $rebates;
                                        $b_sum_others += $others;
                                        $b_sum_redeem += $redeem;
                                        $b_sum_net_proceeds += $net_proceeds;
                    @endphp

<tr>
                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($loan->disbursed_date)->format('d-M-Y') }}</td>
                    <td style="white-space: nowrap;">{{ $loan->borrower && $loan->borrower->details ? $loan->borrower->details->fullname : 'N/A' }}</td>
                    <td class="text-end">{{ ($loan->interest_rate + 0) }}%</td>
                    <td class="text-end">{{ formatMoney($loan->loan_duration) }}</td>
                    <td class="text-end">{{ formatMoney($principal_loan) }}</td>
                    <td class="text-end">{{ formatMoney($special_loan) }}</td>
                    <td class="text-end">{{ formatMoney($interest) }}</td>
                    <td class="text-end">{{ formatMoney($service_fee) }}</td>
                    <td class="text-end">{{ formatMoney($notary_fee) }}</td>
                    <td class="text-end">{{ formatMoney($insurance_fee) }}</td>
                    <td class="text-end">{{ formatMoney($old_loan) }}</td>
                    <td class="text-end">{{ formatMoney($deficit) }}</td>
                    <td class="text-end">{{ formatMoney($savings) }}</td>
                    <td class="text-end">{{ formatMoney($payment_special) }}</td>
                    <td class="text-end">{{ formatMoney($rebates) }}</td>
                    <td class="text-end">{{ formatMoney($others) }}</td>
                    <td class="text-end">{{ formatMoney($redeem) }}</td>
                    <td class="text-end">{{ formatMoney($net_proceeds) }}</td>
                    <td>{{ $loan->description }}</td>
                    <td>{{ $loan->payments->pluck('description')->filter()->unique()->implode(', ') }}</td>
                    <td>{{ $remarks }}</td>
</tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                        <td colspan="4" class="text-end">Subtotal - {{ $branch_name }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_principal) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_special) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_interest) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_service) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_notary) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_insurance) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_old_loan) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_deficit) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_savings) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_payment_special) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_rebates) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_others) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_redeem) }}</td>
                        <td class="text-end">{{ formatMoney($b_sum_net_proceeds) }}</td>
                        <td colspan="3"></td>
                    </tr>
                @endforeach
        </tbody>
        <tfoot>

            <tr>
                <th colspan="4" class="text-end">TOTAL:</th>
                <th class="text-end">{{ formatMoney($sum_principal) }}</th>
                <th class="text-end">{{ formatMoney($sum_special) }}</th>
                <th class="text-end">{{ formatMoney($sum_interest) }}</th>
                <th class="text-end">{{ formatMoney($sum_service) }}</th>
                <th class="text-end">{{ formatMoney($sum_notary) }}</th>
                <th class="text-end">{{ formatMoney($sum_insurance) }}</th>
                <th class="text-end">{{ formatMoney($sum_old_loan) }}</th>
                <th class="text-end">{{ formatMoney($sum_deficit) }}</th>
                <th class="text-end">{{ formatMoney($sum_savings) }}</th>
                <th class="text-end">{{ formatMoney($sum_payment_special) }}</th>
                <th class="text-end">{{ formatMoney($sum_rebates) }}</th>
                <th class="text-end">{{ formatMoney($sum_others) }}</th>
                <th class="text-end">{{ formatMoney($sum_redeem) }}</th>
                <th class="text-end">{{ formatMoney($sum_net_proceeds) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
