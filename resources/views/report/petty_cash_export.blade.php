<!DOCTYPE html>
<html>
<head>
    <title>Petty Cash Report</title>
    <style>
        @page { size: a4 portrait; margin: 100px 10mm 35px 10mm; }
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
                <th>Particulars</th>
                <th class="text-end">Cash Received (Cash In)</th>
                <th class="text-end">Amount Expense</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php 
                if (!function_exists('formatMoney')) {
                    function formatMoney($amount) {
                        return (float)$amount == 0 ? '' : number_format($amount, 2);
                    }
                }
                
                $records_by_branch = collect($records)->groupBy('branch_name');
                $grand_cash_in = 0;
                $grand_expense = 0;
            @endphp
            
            @if(count($records) > 0)
                @foreach($records_by_branch as $branch_name => $branch_records)
                    @php
                        $b_cash_in = 0;
                        $b_expense = 0;
                    @endphp
                    <tr>
                        <td colspan="5" style="font-weight: bold; background-color: #f8f9fa;">BRANCH: {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                    </tr>
                    @foreach($branch_records as $record)
                        @php
                            $b_cash_in += $record->cash_in ?? 0;
                            $b_expense += $record->expense ?? 0;
                            
                            $grand_cash_in += $record->cash_in ?? 0;
                            $grand_expense += $record->expense ?? 0;
                        @endphp
                        <tr>
                            <td>{{ date('d-M-Y', strtotime($record->date)) }}</td>
                            <td>{{ $record->particulars }}</td>
                            <td class="text-end">{{ formatMoney($record->cash_in) }}</td>
                            <td class="text-end">{{ formatMoney($record->expense) }}</td>
                            <td class="text-end">{{ formatMoney($record->balance) }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                        <td colspan="2" class="text-end">Subtotal - {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                        <td class="text-end">{{ formatMoney($b_cash_in) }}</td>
                        <td class="text-end">{{ formatMoney($b_expense) }}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #e2e3e5;">
                    <td colspan="2" class="text-end">Total</td>
                    <td class="text-end">{{ formatMoney($grand_cash_in) }}</td>
                    <td class="text-end">{{ formatMoney($grand_expense) }}</td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan="5" class="text-center">No records found.</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    
</body>
</html>
