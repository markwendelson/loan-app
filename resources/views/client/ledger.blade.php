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
            <div class="d-flex justify-content-between">
                <div>
                    <h1 style="font-size: 3.5rem;">{{$client->details->fullname}}</h1>
                    <h3>{{$client->details->present_full_address}}</h3>
                </div>
                <a href="{{route('savings.transactions', $client->id)}}" class="btn btn-sm btn-primary">Savings</a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row project-cards">
        <div class="col-md-12 project-list">
            <div class="card">
            <div class="row">
                <div class="col-md-12">
                <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="principal-loan-tab" data-bs-toggle="tab" href="#principal-loan" role="tab" aria-controls="principal-loan" aria-selected="false"><i data-feather="info"></i>Principal Loan</a></li>
                    @isset($special_loan)
                    <li class="nav-item"><a class="nav-link" id="special-loan-tab" data-bs-toggle="tab" href="#special-loan" role="tab" aria-controls="special-loan" aria-selected="false"><i data-feather="star"></i>Special Loan</a></li>
                    @endisset
                    <li class="nav-item"><a class="nav-link" id="incentives-pension-tab" data-bs-toggle="tab" href="#incentives-pension" role="tab" aria-controls="incentives-pension" aria-selected="false"><i data-feather="star"></i>Incentives/Pension</a></li>
                    <li class="nav-item"><a class="nav-link" id="membership-tab" data-bs-toggle="tab" href="#membership" role="tab" aria-controls="membership" aria-selected="false"><i data-feather="star"></i>Membership</a></li>
                    <li class="nav-item"><a class="nav-link" id="client-as-comaker-tab" data-bs-toggle="tab" href="#client-as-comaker" role="tab" aria-controls="client-as-comaker" aria-selected="false"><i data-feather="star"></i>Client as Co-Maker</a></li>
                    <li class="nav-item"><a class="nav-link" id="attachments-tab" data-bs-toggle="tab" href="#attachments" role="tab" aria-controls="attachments" aria-selected="false"><i data-feather="star"></i>Attachments</a></li>
                </ul>
                </div>
            </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
            <div class="card-body">
                <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}"/>
                <div class="tab-content" id="top-tabContent">
                <div class="tab-pane fade show active" id="principal-loan" role="tabpanel" aria-labelledby="principal-loan-tab">
                    @isset($principal_loan)
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <div>Loan ID: {{$loan['principal_loan_no']}}</div>
                                    <div>Loan Product: {{$loan['principal_loan_product']}}</div>
                                    <div>Date Loan Granted: {{$loan['principal_date_granted'] ?? date('l, F j, Y, g:i A',strtotime($loan['principal_date_granted']))}}</div>
                                    <div>Remarks: <p></p></div>
                                </div>
                                <div class="col-md-4 float-end">
                                    <a href="{{route('loan.create', ['loan_no_reference' => $loan['principal_loan_no'], 'reloan'=>true])}}" class="btn btn-sm btn-success mx-auto">Reloan</a>
                                    {{-- @if(Str::slug($loan['principal_loan_product_type']) == 'open-term') --}}
                                    <a href="{{route('loan.create', ['loan_no_reference' => $loan['principal_loan_no'], 'additional_loan'=>true])}}" class="btn btn-sm btn-primary mx-auto">Add Loan</a>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tblLedger" style="font-size: smaller;">
                                    <thead class="bg-default">
                                        <tr>
                                            <th class="text-center" width="80" colspan="3" rowspan="2"></th>
                                            <th class="text-center" width="150" colspan="6">LOAN</th>
                                            <th class="text-center" width="80"></th>
                                            <th class="text-center" width="150" colspan="7">PAYMENT</th>
                                            <th class="text-center" width="80" colspan="3" rowspan="2"></th>
                                        </tr>
                                        <tr>
                                            <th rowspan="3">Principal</th>
                                            <th colspan="4">Deduction</th>
                                            <th rowspan="3">Net Proceed</th>
                                            <th></th>
                                            <th colspan="7" style="border-right-width:1px;">Applied to</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">Transaction Date</th>
                                            <th rowspan="2">Withdraw Date</th>
                                            <th rowspan="2">Salary Month</th>
                                            <th rowspan="2">Interest</th>
                                            <th rowspan="2">Service Fee</th>
                                            <th rowspan="2">Insurance Fee</th>
                                            <th rowspan="2">Others</th>
                                            <th rowspan="2"></th>
                                            <th rowspan="2">Transaction Date</th>
                                            <th colspan="2">Interest</th>
                                            <th colspan="2">Insurance</th>
                                            <th rowspan="2">Principal</th>
                                            <th rowspan="2">Change</th>
                                            <th rowspan="2">Balance</th>
                                            <th rowspan="2">Remarks</th>
                                            <th rowspan="2">Signature</th>
                                        </tr>
                                        <tr>
                                            <th>Amount</th>
                                            <th>Month</th>
                                            <th>Amount</th>
                                            <th>Month</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                        <div class="card card-body">No active loan.</div>
                    @endisset
                </div>
                @isset($special_loan)
                <div class="tab-pane fade" id="special-loan" role="tabpanel" aria-labelledby="special-loan-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <div>Loan ID: {{$loan['special_loan_no']}}</div>
                                    <div>Loan Product: {{$loan['special_loan_product']}}</div>
                                    <div>Date Loan Granted: {{$loan['special_date_granted'] ?? date('l, F j, Y, g:i A',strtotime($loan['special_date_granted']))}}</div>
                                    <div>Remarks: <p></p></div>
                                </div>
                                <div class="col-md-4 float-end">
                                    <a href="{{route('loan.create', ['loan_no_reference' => $loan['special_loan_no'], 'reloan'=>true])}}" class="btn btn-sm btn-success mx-auto">Reloan</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tblSpecialLedger" style="font-size: smaller;">
                                    <thead class="bg-default">
                                        <tr>
                                            <th class="text-center" width="80" colspan="2" rowspan="2"></th>
                                            <th class="text-center" width="150" colspan="6">LOAN</th>
                                            <th class="text-center" width="80"></th>
                                            <th class="text-center" width="150" colspan="5">PAYMENT</th>
                                            <th class="text-center" width="80" colspan="2" rowspan="2"></th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">Principal</th>
                                            <th colspan="4">Deduction</th>
                                            <th rowspan="2">Net Proceed</th>
                                            <th></th>
                                            <th></th>
                                            <th colspan="4" style="border-right-width:1px;">Applied to</th>
                                        </tr>
                                        <tr>
                                            <th>Transaction Date</th>
                                            <th>Loan Type</th>
                                            <th>Interest</th>
                                            <th>Service Fee</th>
                                            <th>Insurance Fee</th>
                                            <th>Others</th>
                                            <th></th>
                                            <th>Transaction Date</th>
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
                @endisset
                <div class="tab-pane fade" id="incentives-pension" role="tabpanel" aria-labelledby="incentives-pension-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="position-relative">
                                <span>Incentives/Pension</span>
                                <a href="javascript:void(0)" class="btn btn-primary position-absolute end-0 top-50 translate-middle-y me-2 btn-new-incentives">
                                    <i class="fa fa-plus"></i> New Incentives
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- Incentives/Pension Ledger Table Here -->
                                <table class="table table-bordered table-hover" id="tblIncentives">
                                    <thead class="bg-default">
                                        <tr>
                                            <th class="text-center" width="40">Id</th>
                                            <th class="text-center" width="80">Transaction Date</th>
                                            <th class="text-center" width="150">Description</th>
                                            <th class="text-center" width="80">Amount</th>
                                            <th class="text-center" width="80">Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="membership" role="tabpanel" aria-labelledby="membership-tab">
                    <div class="card">
                        <div class="card-header">
                            Membership
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- Membership Ledger Table Here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="client-as-comaker" role="tabpanel" aria-labelledby="client-as-comaker-tab">
                    @include('client.co-maker-list')
                </div>
                <div class="tab-pane fade" id="attachments" role="tabpanel" aria-labelledby="attachments-tab">
                    @include('client.attachments')
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    {{-- modal --}}
    @include('client.modal.incentives_modal')
@endsection

@push('js')

<script>
    $(document).ready(function () {
        const canEdit = {{ auth()->user()->can('edit-incentives') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-incentives') ? 1 : 0 }};

        var client_id = $("#client_id").val();

        const principal = $('#tblLedger').DataTable({
            dom: 'rtip',
            autoWidth: true,
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('client.ledger.principal',':client') }}`.replace(':client', client_id),
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
                { data: 'payment_date', name: 'payment_date' },
                { data: 'payment_interest', name: 'payment_interest' },
                { data: 'payment_interest_month', name: 'payment_interest_month' },
                { data: 'payment_insurance', name: 'payment_insurance' },
                { data: 'payment_insurance_month', name: 'payment_insurance_month' },
                { data: 'payment_principal', name: 'payment_principal' },
                { data: 'change', name: 'change' },
                { data: 'balance', name: 'balance' },
                { data: 'remarks', name: 'remarks' },
                { data: 'signature', name: 'signature' },
            ],
            columnDefs: [
                // { className: "text-center", targets: [4,5] },
            ]
        });

        const special = $('#tblSpecialLedger').DataTable({
            dom: 'rtip',
            autoWidth: true,
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('client.ledger.special',':client') }}`.replace(':client', client_id),
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'description', name: 'description' },
                { data: 'loan_principal', name: 'loan_principal' },
                { data: 'loan_interest', name: 'loan_interest' },
                { data: 'loan_service_fee', name: 'loan_service_fee' },
                { data: 'loan_insurance_fee', name: 'loan_insurance_fee' },
                { data: 'others', name: 'others' },
                { data: 'net_proceed', name: 'net_proceed' },
                { data: 'signature', name: 'signature' },
                { data: 'payment_date', name: 'payment_date' },
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

        const incentives = $('#tblIncentives').DataTable({
            dom: 'rtip',
            autoWidth: true,
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('client.incentives',':client') }}`.replace(':client', client_id),
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'description', name: 'description' },
                { data: 'amount', name: 'amount' },
                { data: 'actions', name: 'actions', render: function(data, type, row) {
                        let buttons = '';
                        if (canEdit) {
                            buttons += `<button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                        }
                        if (canDelete) {
                            buttons += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button> `;
                        }

                        buttons += `<button class="btn btn-primary btn-xs btnPrintReceipt" data-id="${row.id}" title="Print Receipt"><i class="fa fa-print"></i></button> `;

                        return buttons;
                    }
                },
            ],
            columnDefs: [
                // { className: "text-center", targets: [4,5] },
            ]
        });

        $('.btn-new-incentives').on('click', function() {
            $("#modalIncentives").modal("show");
        })

        $("#modalIncentives .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmIncentivesModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmIncentivesModal").serializeArray();

                var client = $("#modalIncentives [name='client_id']").val();
                var _url = `{{route('client.add-incentives',':client')}}`.replace(':client',client);

                $.ajax({
                    method: 'POST',
                    url: _url,
                    data: formData,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if(response.status == 'success'){
                            hideModal('modalIncentives');
                            showToast.success(response.message)
                            incentives.ajax.reload();
                            window.open('/incentives/' + response.id + '/print', '_blank');
                        }else{
                            showToast.error(response.message)
                        }
                    },
                    error: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        showToast.error(response.responseJSON.message)
                    }
                });
            }
        });

        $("#tblIncentives").on("click", ".btnPrintReceipt", function(){
            let id = $(this).attr("data-id");
            window.open('/incentives/' + id + '/print', '_blank');
        })

    });



    function hideIncentivesModal() {
        clearIncentivesModal();
        $("#modalIncentives").modal("hide");
    }

    function clearIncentivesModal() {
        $("#modalIncentives #id").val("");
        $("#modalIncentives #transaction_date").val("");
        $("#modalIncentives #description").val("");
        $("#modalIncentives #amount").val("");
    }

    function deleteData(id) {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/incentives/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblIncentives').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

    </script>

@endpush
