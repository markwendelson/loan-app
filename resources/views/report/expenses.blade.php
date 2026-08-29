@extends('layouts.app')

@section('content')
<style>
    #expenses-report-table thead th { text-align: center !important; }
</style>
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Expenses Report</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"> <i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
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
                    <h5>Expenses ({{ $branchName }})</h5>
                    <span class="badge badge-primary">{{ $dateLabel }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expenses-report-table">
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
                                    if (!function_exists('formatMoneyWeb')) {
                                        function formatMoneyWeb($amount) {
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
                                            
                                            // Placeholders for VAT logic if needed
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
                                            <td style="white-space: nowrap;">{{ date('d-M-Y', strtotime($expense->expense_date)) }}</td>
                                            <td>{{ $expense->vendor_name }}</td>
                                            <td>{{ $expense->branch_name }}</td>
                                            <td>{{ $expense->tin }}</td>
                                            <td>{{ $expense->tax_classification }}</td>
                                            <td>{{ $expense->ref_no }}</td>
                                            <td>{{ $expense->category_name }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($amount) }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($vat_expense) }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($wht) }}</td>
                                            <td class="text-end">{{ formatMoneyWeb($vat_input) }}</td>
                                            <td>{{ $expense->description }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                                        <td colspan="7" class="text-end">Subtotal - {{ $branch_name ? strtoupper($branch_name) : 'UNKNOWN BRANCH' }}</td>
                                        <td class="text-end">{{ formatMoneyWeb($b_sum_amount) }}</td>
                                        <td class="text-end">{{ formatMoneyWeb($b_sum_vat_expense) }}</td>
                                        <td class="text-end">{{ formatMoneyWeb($b_sum_wht) }}</td>
                                        <td class="text-end">{{ formatMoneyWeb($b_sum_vat_input) }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Total</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_amount) }}</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_vat_expense) }}</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_wht) }}</th>
                                    <th class="text-end">{{ formatMoneyWeb($grand_vat_input) }}</th>
                                    <th></th>
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
<script src="{{url('assets/js/datatable/datatable-extension/dataTables.buttons.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/jszip.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/pdfmake.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/vfs_fonts.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/buttons.html5.min.js')}}"></script>
<script src="{{url('assets/js/datatable/datatable-extension/buttons.print.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#expenses-report-table').DataTable({
            dom: 'Bfrtip',
            paging: false,
            ordering: false,
            buttons: [] 
        });
    });
</script>
@endsection
