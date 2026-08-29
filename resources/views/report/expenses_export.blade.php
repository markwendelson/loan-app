<!DOCTYPE html>
<html>
<head>
    <title>Expenses Report</title>
    <style>
        @page { size: legal landscape; margin: 100px 10mm 35px 10mm; }
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; }
        th { background-color: #f4f4f4; text-align: center; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name of Supplier</th>
                <th>Branch</th>
                <th>TIN #</th>
                <th>VT/NVT</th>
                <th>OR No./Invoice No.</th>
                <th>Classification</th>
                <th class="text-end">Amount</th>
                <th class="text-end">VAT Expense</th>
                <th class="text-end">Withholding Tax</th>
                <th class="text-end">VAT Input</th>
                <th>Particulars</th>
            </tr>
        </thead>
        <tbody>
            @php 
                if (!function_exists('formatMoney')) {
                    function formatMoney($amount) {
                        return (float)$amount == 0 ? '' : number_format($amount, 2);
                    }
                }
                
                $expenses_by_branch = collect($expenses)->groupBy('branch_name');
                $grand_amount = 0;
                $grand_vat_expense = 0;
                $grand_wht = 0;
                $grand_vat_input = 0;
            @endphp
            @foreach($expenses_by_branch as $branch_name => $branch_expenses)
                @php
                    $b_sum_amount = 0;
                    $b_sum_vat_expense = 0;
                    $b_sum_wht = 0;
                    $b_sum_vat_input = 0;
                @endphp
                <tr>
                    <td colspan="12" style="font-weight: bold; background-color: #f8f9fa;">BRANCH: {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                </tr>
                @foreach($branch_expenses as $expense)
                    @php
                        $amount = $expense->amount ?? 0;
                        
                        $vat_expense = 0; 
                        $wht = 0;
                        $vat_input = 0;
                        
                        $b_sum_amount += $amount;
                        $b_sum_vat_expense += $vat_expense;
                        $b_sum_wht += $wht;
                        $b_sum_vat_input += $vat_input;
                        
                        $grand_amount += $amount;
                        $grand_vat_expense += $vat_expense;
                        $grand_wht += $wht;
                        $grand_vat_input += $vat_input;
                    @endphp
                    <tr>
                        <td>{{ date('d-M-Y', strtotime($expense->expense_date)) }}</td>
                        <td>{{ $expense->vendor_name }}</td>
                        <td>{{ $expense->branch_name }}</td>
                        <td>{{ $expense->tin }}</td>
                        <td>{{ $expense->tax_classification }}</td>
                        <td>{{ $expense->ref_no }}</td>
                        <td>{{ $expense->category_name }}</td>
                        <td class="text-end">{{ formatMoney($amount) }}</td>
                        <td class="text-end">{{ formatMoney($vat_expense) }}</td>
                        <td class="text-end">{{ formatMoney($wht) }}</td>
                        <td class="text-end">{{ formatMoney($vat_input) }}</td>
                        <td>{{ $expense->description }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f1f1f1;">
                    <td colspan="7" class="text-end">Subtotal - {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                    <td class="text-end">{{ formatMoney($b_sum_amount) }}</td>
                    <td class="text-end">{{ formatMoney($b_sum_vat_expense) }}</td>
                    <td class="text-end">{{ formatMoney($b_sum_wht) }}</td>
                    <td class="text-end">{{ formatMoney($b_sum_vat_input) }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #e2e3e5;">
                <td colspan="7" class="text-end">Total</td>
                <td class="text-end">{{ formatMoney($grand_amount) }}</td>
                <td class="text-end">{{ formatMoney($grand_vat_expense) }}</td>
                <td class="text-end">{{ formatMoney($grand_wht) }}</td>
                <td class="text-end">{{ formatMoney($grand_vat_input) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    
</body>
</html>
