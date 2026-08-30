<!DOCTYPE html>
<html>
<head>
    <title>Cash Request Report</title>
    <style>
        @page { size: portrait; margin: 100px 10mm 35px 10mm; }
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
                <th>Employee</th>
                <th>Branch</th>
                <th class="text-end">Amount</th>
                <th>Status</th>
                <th>Approver</th>
            </tr>
        </thead>
        <tbody>
            @php 
                if (!function_exists('formatMoney')) {
                    function formatMoney($amount) {
                        return (float)$amount == 0 ? '' : number_format($amount, 2);
                    }
                }
                
                $requests_by_branch = collect($records)->groupBy(function($record) {
                    return $record->branch ? $record->branch->name : 'UNKNOWN BRANCH';
                });
                
                $grand_amount = 0;
            @endphp
            
            @foreach($requests_by_branch as $branch_name => $branch_requests)
                @php
                    $b_sum_amount = 0;
                @endphp
                <tr>
                    <td colspan="6" style="font-weight: bold; background-color: #f8f9fa;">BRANCH: {{ strtoupper($branch_name) }}</td>
                </tr>
                @foreach($branch_requests as $request)
                    @php
                        $amount = $request->amount ?? 0;
                        $b_sum_amount += $amount;
                        $grand_amount += $amount;
                    @endphp
                    <tr>
                        <td>{{ $request->created_at->format('d-M-Y h:i A') }}</td>
                        <td>{{ $request->employee ? $request->employee->display_name : 'N/A' }}</td>
                        <td>{{ $branch_name }}</td>
                        <td class="text-end">{{ formatMoney($amount) }}</td>
                        <td>{{ ucfirst($request->status) }}</td>
                        <td>{{ $request->approver ? $request->approver->name : '-' }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; background-color: #f1f1f1;">
                    <td colspan="3" class="text-end">Subtotal - {{ strtoupper($branch_name) }}</td>
                    <td class="text-end">{{ formatMoney($b_sum_amount) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #e2e3e5;">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">{{ formatMoney($grand_amount) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
