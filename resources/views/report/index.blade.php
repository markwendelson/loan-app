@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Reports</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"> <i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Generate Report</h5>
                </div>
                <div class="card-body">
                    <form id="report-form" action="{{ route('report.generate') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="report_type">Report Type</label>
                                <select class="form-control" id="report_type" name="report_type" required>
                                    @can('view-loan-report')
                                        <option value="loan">Loan (Disbursed)</option>
                                    @endcan
                                    @can('view-payment-report')
                                        <option value="payment">Payment (Received)</option>
                                    @endcan
                                    @can('view-expense-report')
                                        <option value="expenses">Expenses</option>
                                    @endcan
                                    @can('view-petty-cash-report')
                                        <option value="petty_cash">Petty Cash</option>
                                    @endcan
                                    @can('view-cash-request-report')
                                        <option value="cash_request">Cash Request</option>
                                    @endcan
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="frequency">Frequency</label>
                                <select class="form-control" id="frequency" name="frequency" required>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="branch_id">Branch</label>
                                <select class="form-control" id="branch_id" name="branch_id">
                                    <option value="all">ALL BRANCHES</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label id="date_label" for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" name="action" value="excel" class="btn btn-success"><i class="fa fa-file-excel"></i> Download Excel</button>
                            <button type="submit" name="action" value="pdf" class="btn btn-danger"><i class="fa fa-file-pdf"></i> Download PDF</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#frequency').change(function() {
            var freq = $(this).val();
            var $dateInput = $('#date');

            $('#date_div').removeClass('d-none');
            $('#quarter_div').addClass('d-none');

            if (freq === 'daily') {
                $dateInput.attr('type', 'date');
                $('#date_label').text('Date');
            } else if (freq === 'weekly') {
                $dateInput.attr('type', 'week');
                $('#date_label').text('Week');
            } else if (freq === 'monthly') {
                $dateInput.attr('type', 'month');
                $('#date_label').text('Month');
            } else if (freq === 'annually') {
                $dateInput.attr('type', 'number');
                $dateInput.attr('min', '2000');
                $dateInput.attr('max', '2100');
                $dateInput.attr('step', '1');
                $dateInput.val(new Date().getFullYear());
                $('#date_label').text('Year');
            } else if (freq === 'quarterly') {
                $('#date_div').addClass('d-none');
                $('#quarter_div').removeClass('d-none');
            }
        });

        $('form').on('submit', function() {
            if ($('#frequency').val() === 'quarterly') {
                var q = $('#quarter_q').val();
                var y = $('#quarter_y').val();
                $('#date').attr('type', 'text').val(y + '-Q' + q);
            }
        });
    });
</script>
@endsection
