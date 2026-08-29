@extends('layouts.app')

@section('content')
<style>
    #petty-cash-report-table thead th { text-align: center !important; }
</style>
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Petty Cash Report</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"> <i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Petty Cash</li>
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
                    <h5>Petty Cash ({{ $branchName }})</h5>
                    <span class="badge badge-primary">{{ $dateLabel }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="petty-cash-report-table">
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
                                    if (!function_exists('formatMoneyWeb')) {
                                        function formatMoneyWeb($amount) {
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
                                                <td style="white-space: nowrap;">{{ date('d-M-Y', strtotime($record->date)) }}</td>
                                                <td>{{ $record->particulars }}</td>
                                                <td class="text-end">{{ formatMoneyWeb($record->cash_in) }}</td>
                                                <td class="text-end">{{ formatMoneyWeb($record->expense) }}</td>
                                                <td class="text-end">{{ formatMoneyWeb($record->balance) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr style="font-weight: bold; background-color: #f1f1f1;">
                                            <td colspan="2" class="text-end">Subtotal - {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($b_cash_in) }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($b_expense) }}</td>
                                            <td class="text-end"></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">No records found.</td>
                                    </tr>
                                @endif
                            </tbody>
                            @if(count($records) > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_cash_in) }}</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_expense) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{url('assets/js/datatable/datatable-extension/dataTables.buttons.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/jszip.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/pdfmake.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/vfs_fonts.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/buttons.html5.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/buttons.print.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#petty-cash-report-table').DataTable({
            dom: 'Bfrtip',
            paging: false,
            ordering: false,
            buttons: [] 
        });
    });
</script>
@endsection
