@extends('layouts.app')

@push('css')
<style>
    .bg-default {
         background-color: #1f807f;
     }
     .bg-default th {
         color: white; /* Set white text for <th> elements */
         border: 1px solid white;
     }

     table > thead {
        text-align: center;
        vertical-align:middle !important;
     }
 </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client | Manage Ledger</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Manage Ledger</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <div>Name: <span>{{$client->details->fullname}}</span></div>
            <div>Address: <span>{{$client->details->present_full_address}}</div>
        </div>
    </div>

        <div class="container-fluid">
            <div class="row project-cards">
            <div class="col-md-12 project-list">
                <div class="card">
                <div class="row">
                    <div class="col-md-6">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="principal-loan-tab" data-bs-toggle="tab" href="#principal-loan" role="tab" aria-controls="principal-loan" aria-selected="false"><i data-feather="info"></i>Loan</a></li>
                    </ul>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-group mb-0 me-0"></div><a class="btn btn-primary" href="#"> <i data-feather="users"> </i>Co-Maker</a>
                    </div> --}}
                </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                <div class="card-body">
                    <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}"/>
                    <div class="tab-content" id="top-tabContent">
                    <div class="tab-pane fade show active" id="principal-loan" role="tabpanel" aria-labelledby="principal-loan-tab">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div>Loan ID: {{$loan['loan_no']}}</div>
                                        <div>Loan Product: {{$loan->loanProduct->name}}</div>
                                        <div>Date Loan Granted: {{$loan['approved_date'] ?? date('l, F j, Y, g:i A',strtotime($loan['approved_date']))}}</div>
                                        <div>Remarks: <p></p></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="tblLedger" style="font-size: smaller;">
                                        <thead class="bg-default">
                                            <tr>
                                                <th class="text-center" width="80" colspan="3" rowspan="2"></th>
                                                <th class="text-leftr" width="150" colspan="6">LOAN</th>
                                                <th class="text-center" width="80"></th>
                                                <th class="text-leftr" width="150" colspan="5">PAYMENT</th>
                                                <th class="text-center" width="80" colspan="2" rowspan="2"></th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th colspan="4">Deduction</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th colspan="4" style="border-right-width:1px;">Applied to</th>
                                            </tr>
                                            <tr>
                                                <th>Transaction Date</th>
                                                <th>Withdraw Date</th>
                                                <th>Salary Month</th>
                                                <th>Principal</th>
                                                <th>Interest</th>
                                                <th>Service Fee</th>
                                                <th>Insurance Fee</th>
                                                <th>Others</th>
                                                <th>Net Proceed</th>
                                                <th></th>
                                                <th>WDL/DR</th>
                                                <th>Interest</th>
                                                <th>Insurance</th>
                                                <th>Principal</th>
                                                <th>Change</th>
                                                <th>Balance</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
@endsection

@push('js')

<script>
    var loan = @json($loan);
    $(document).ready(function () {
        var client_id = $("#client_id").val();
        let pathParts = window.location.pathname.split('/');

        const principal = $('#tblLedger').DataTable({
            dom: 'rtip',
            autoWidth: true,
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('client.ledger.loan', [':client', ':loan_no']) }}`
                    .replace(':client', client_id)
                    .replace(':loan_no', pathParts[3]),
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'withdraw_date', name: 'withdraw_date' },
                { data: 'salary_month', name: 'salary_month' },
                { data: 'loan_principal', name: 'loan_principal' },
                { data: 'loan_interest', name: 'loan_interest' },
                { data: 'loan_service_fee', name: 'loan_service_fee' },
                { data: 'loan_insurance_fee', name: 'loan_insurance_fee' },
                { data: 'others', name: 'others' },
                { data: 'net_proceed', name: 'net_proceed' },
                { data: 'signature', name: 'signature' },
                { data: 'wdl', name: 'wdl' },
                { data: 'payment_interest', name: 'payment_interest' },
                { data: 'payment_insurance', name: 'payment_insurance' },
                { data: 'payment_principal', name: 'payment_principal' },
                { data: 'change', name: 'change' },
                { data: 'balance', name: 'balance' },
                { data: 'remarks', name: 'remarks' },
            ],
            columnDefs: [
                // { className: "text-center", targets: [4,5] },
            ]
        });

    });


    </script>

@endpush
