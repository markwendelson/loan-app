@extends('layouts.app')

@section('title') Cash Request Report @endsection

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Cash Request Report</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Cash Request</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>Cash Request ({{ $branchName }})</h5>
                    <span class="badge badge-primary">{{ $dateLabel }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="cash-request-report-table">
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
                                    if (!function_exists('formatMoneyWeb')) {
                                        function formatMoneyWeb($amount) {
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
                                            <td style="white-space: nowrap;">{{ $request->created_at->format('d-M-Y h:i A') }}</td>
                                            <td>{{ $request->employee ? $request->employee->display_name : 'N/A' }}</td>
                                            <td>{{ $branch_name }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($amount) }}</td>
                                            <td>{{ ucfirst($request->status) }}</td>
                                            <td>{{ $request->approver ? $request->approver->name : '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                                        <td colspan="3" class="text-end">Subtotal - {{ strtoupper($branch_name) }}</td>
                                        <td class="text-end">{{ formatMoneyWeb($b_sum_amount) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_amount) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#cash-request-report-table').DataTable({
            paging: false,
            ordering: false,
            searching: false,
            info: false
        });
    });
</script>
@endsection
