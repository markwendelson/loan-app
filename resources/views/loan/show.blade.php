@extends('layouts.app')

@push('css')
<style>
    ul > li > span {
        float: right;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Loan | View Loan</h4>
                <h4>{{$loan->loan_no}}
                    @if($loan->status == \App\Enums\LoanStatus::APPROVE->value)
                        <span class="badge badge-sm badge-success">Approved</span>
                    @elseif($loan->status == \App\Enums\LoanStatus::DECLINE->value)
                        <span class="badge badge-sm badge-danger">Declined</span>
                    @elseif($loan->status == \App\Enums\LoanStatus::WITHDRAWN->value)
                        <span class="badge badge-sm badge-warning">Withdrawn</span>
                    @elseif($loan->status == \App\Enums\LoanStatus::CLOSED->value)
                        <span class="badge badge-sm badge-dark">Closed</span>
                    @else
                        <span class="badge badge-sm badge-primary">Pending</span>
                    @endif

                    @if($isForWithdraw)
                        <span class="badge bg-warning">For Withdraw Loan Application</span>
                    @endif
                </h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item">Loan</li>
                    <li class="breadcrumb-item active">View Loan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">Loan Information</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="loan_date">Loan Date</label>
                    <input type="date" class="form-control form-control-sm" name="loan_date" id="loan_date" value="{{$loan->loan_date}}" readonly/>
                </div>
                <div class="form-group">
                    <input type="hidden" name="is_additional_loan" id="is_additional_loan" value="{{$loan->is_additional_loan}}">
                    <label for="borrower_id">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control form-control-sm" disabled>
                        <option value="" disabled>-Select-</option>
                        @foreach ($clients as $client)
                            <option value="{{$client->id}}" {{ $client->id == $loan->borrower_id ? 'selected' : ''}}>{{$client->details->fullname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id">Loan Product</label>
                    <select name="loan_product_id" id="loan_product_id" class="form-control form-control-sm" disabled>
                        <option value="" disabled selected>-Select-</option>
                        @foreach ($loan_products as $loan_product)
                            <option value="{{$loan_product->id}}" {{ $loan_product->id == $loan->loan_product_id ? 'selected' : '' }} data-loan-type={{Str::slug($loan_product->loanType->name)}} data-interest-rate="{{$loan_product->loanType->interest_rate}}">{{$loan_product->name}}</option>
                        @endforeach
                    </select>
                </div>
                @if($loan->is_special)
                <div class="form-group special-description">
                    <label for="description">Description</label>
                    <input type="text" class="form-control form-control-sm" name="description" id="description" value="{{$loan->description}}" readonly/>
                </div>
                @endif
                <div class="form-group pricipal">
                    <label for="principal">Principal</label>
                    <input type="number" class="form-control form-control-sm" name="principal" id="principal" value="{{$loan->principal}}" readonly/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="text" class="form-control form-control-sm" name="term" id="term" value="{{$loan->loan_duration}}" readonly/>
                </div>
                <div class="form-group interest-rate">
                    <label for="interest_rate">Interest Rate</label>
                    <input type="number" class="form-control form-control-sm" name="interest_rate" id="interest_rate" value="{{$loan->interest_rate}}" readonly/>
                </div>
                {{-- <div class="form-group">
                    <label for="interest_method">Interest Method</label>
                    <select name="interest_method" id="interest_method" class="form-control">
                        <option value="">-Select-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" class="form-control" name="release_date"/>
                </div>
                <div class="form-group">
                    <label for="interest_start_date">Interest Start Date</label>
                    <input type="date" class="form-control" name="interest_start_date"/>
                </div>
                <div class="form-group">
                    <label for="first_payment_date">First Payment Date</label>
                    <input type="date" class="form-control" name="first_payment_date"/>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">Loan Summary</div>
            <div class="card-body">
                <ul>
                    <li>Principal Loan <span class="principal">{{number_format($loan->principal,2)}}</span></li>
                    <li class="mt-3 mb-3">Deductions</li>
                    <ul style="margin-left: 1rem;">
                        <li>Advance Interest <span class="advance_interest">{{number_format($loan->loanCharges->advance_interest,2)}}</span></li>
                        <li>Service Fee <span class="service_fee">{{number_format($loan->loanCharges->service_fee,2)}}</span></li>
                        @if(!$loan->is_additional_loan)
                        <li>Insurance Fee <span class="insurance_fee">{{number_format($loan->loanCharges->insurance_fee,2)}}</span></li>
                        @endif
                        <li>Notary Fee <span class="notary_fee">{{number_format($loan->loanCharges->notary_fee,2)}}</span></li>
                        <li>Membership Fee <span class="membership_fee">{{number_format($loan->loanCharges->membership_fee_paid,2)}}</span></li>
                        <li>Principal Advance <span class="principal_advance">{{number_format($loan->loanCharges->principal_advance,2)}}</span></li>
                        @if($loan->is_reloan)
                        <li>Special Loan Balance <span class="reloan_deduction">{{number_format($loan->loanCharges->special_loan_balance,2)}}</span></li>
                        <li>Reloan Deduction <span class="reloan_deduction">{{number_format($loan->loanCharges->reloan_deduction,2)}}</span></li>
                        @endif
                    </ul>
                    @php
                        $principal = $loan->principal;
                        $advance_interest = $loan->loanCharges->advance_interest ?? 0;
                        $service_fee = $loan->loanCharges->service_fee ?? 0;
                        $insurance_fee = $loan->loanCharges->insurance_fee ?? 0;
                        $notary_fee = $loan->loanCharges->notary_fee ?? 0;
                        $membership_fee = $loan->loanCharges->membership_fee_paid ?? 0;
                        $principal_advance = $loan->loanCharges->principal_advance ?? 0;
                        $reloan_deduction = $loan->loanCharges->reloan_deduction ?? 0;
                        $special_loan_balance = $loan->loanCharges->special_loan_balance ?? 0;
                        $total_deductions = $advance_interest + $service_fee + $insurance_fee + $notary_fee + $reloan_deduction + $special_loan_balance + $membership_fee + $principal_advance;
                        $net_proceeds = $principal - $total_deductions;
                    @endphp
                    <li class="mt-3 mb-3">Net Proceeds <span class="net_proceeds">{{ number_format($net_proceeds,2) }}</span></li>
                    <li class="mt-3 mb-3">Monthly Amortization <span class="monthly_amortization">{{ number_format($loan->loanFees->monthly_amortization,2) }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        @include('client.co-maker')
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        @include('client.documents')
    </div>
</div>

<div class="row loan-status-button">
    <div class="col-12"> {{-- Use a column to wrap content --}}
        @if($loan->status == App\Enums\LoanStatus::PENDING->value)
        <!-- Used flexbox for alignment -->
        <div class="d-flex justify-content-end gap-2">
            @can('edit-loan')
            <a href="{{route('loan.edit',$loan->id)}}" class="btn btn-sm btn-primary btn-edit-loan"><i class="fa fa-pencil"></i> Edit</a>
            @endcan
            @can('withdraw-loan')
            <button type="button" class="btn btn-sm btn-warning" onclick="withdrawnLoan({{$loan->id}})"><i class="fa fa-undo"></i> Withdrawn</button>
            @endcan
            @can('approve-loan')
            <button type="button" class="btn btn-sm btn-success" onclick="approveLoan({{$loan->id}})"><i class="fa fa-check"></i> Approve</button>
            @endcan
            @can('decline-loan')
            <button type="button" class="btn btn-sm btn-danger" onclick="declineLoan({{$loan->id}})"><i class="fa fa-remove"></i> Decline</button>
            @endcan
            @can('request-secondary-approval')
            <button type="button" class="btn btn-sm btn-success" onclick="requestApproval({{$loan->id}})"><i class="fa fa-check"></i> Request Approval</button>
            @endcan
        </div>
        @endif
        @if($loan->status == App\Enums\LoanStatus::REQUEST_APPROVE->value)
        <div class="d-flex justify-content-end gap-2">
            @can('approve-secondary-approval')
            <button type="button" class="btn btn-sm btn-success" onclick="approveLoan({{$loan->id}})"><i class="fa fa-check"></i> Approve</button>
            @endcan
            @can('approve-secondary-approval')
            <button type="button" class="btn btn-sm btn-danger" onclick="declineLoan({{$loan->id}})"><i class="fa fa-remove"></i> Decline</button>
            @endcan
        @endif
        @if($isForWithdraw)
        <div class="d-flex justify-content-end gap-2">
            @can('withdraw-loan')
                <button type="button" class="btn btn-sm btn-warning" onclick="withdrawnLoan({{$loan->id}})"><i class="fa fa-undo"></i> Withdrawn</button>
            @endcan
        </div>
        @endif
    </div>
</div>

@endsection

@push('js')
<script>
    $(document).ready(function () {

        var loan_type;
        var interest_rate;
        // $("[name='loan_product_id']").on('change', function() {
            loan_type = $("[name='loan_product_id']").find('option:selected').data('loan-type');

            interest_rate = $("[name='loan_product_id']").find('option:selected').data('interest-rate');
            // $("[name='interest_rate']").val(interest_rate)

            if(loan_type == 'loan-with-term' || loan_type == 'special-loan') {
                $(".term").show()
            } else {
                $(".term").hide()
            }

            calculateLoan();

        // })

        // $("[name='principal'],[name='term'],[name='interest_rate']").on('change', function() {
        //     calculateLoan();
        // })

        async function calculateLoan() {
            return;
            var principal = parseFloat($("[name='principal']").val())
            interest_rate = parseFloat($("[name='interest_rate']").val())
            var term = parseFloat($("[name='term']").val())


            if (isNaN(principal) || isNaN(interest_rate) || (loan_type === 'loan-with-term' && isNaN(term))) {
                return;
            }


            var percentage_rate = interest_rate / 100;
            var interest_term_value = 1 + (term * percentage_rate)

            var capital_amount = 0;
            var advance_interest = 0;
            var monthly_amortization = 0;
            var service_fee = 0;
            var insurance_fee = 0;
            var notary_fee = 0;

            if(loan_type == 'loan-with-term') {
                capital_amount = principal / interest_term_value;
                advance_interest = principal - capital_amount;
                monthly_amortization = principal / term;
                service_fee = capital_amount * (5/100);
                insurance_fee = principal * (2.5/100);
                notary_fee = principal * (3/100);
            } else if(loan_type == 'auto-debit') {
                notaryFeeResponse = await getNotaryFee(principal);
                inuranceFeeResponse = await getInsuranceFee(principal);

                advance_interest = principal * (interest_rate/100);
                monthly_amortization = advance_interest;
                service_fee = principal * (5/100);
                insurance_fee = inuranceFeeResponse.annual; // from insurance table
                if(notaryFeeResponse.type == 'fixed') {
                    notary_fee = notaryFeeResponse.notary_fee;
                } else if(notaryFeeResponse.type == 'percentage') {
                    notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                }
            } else if(loan_type == 'special-loan') {
                notaryFeeResponse = await getNotaryFee(principal);

                advance_interest = (interest_rate/100) * principal * term;
                monthly_amortization = advance_interest;
                if(principal<3000) {
                    service_fee = 200;
                } else if(principal==3000) {
                    service_fee = 300;
                } else {
                    service_fee = principal * (5/100);
                }
                insurance_fee = 0;
                if(notaryFeeResponse.type == 'fixed') {
                    notary_fee = notaryFeeResponse.notary_fee;
                } else if(notaryFeeResponse.type == 'percentage') {
                    notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                }
            } else if(loan_type == 'open-term') {
                var is_additional_loan = $("#is_additional_loan").val();
                if(is_additional_loan) {
                    insurance_fee = 0;
                } else {
                    inuranceFeeResponse = await getInsuranceFee(principal);
                    insurance_fee = inuranceFeeResponse.annual;
                }
                notaryFeeResponse = await getNotaryFee(principal);

                advance_interest = principal * (interest_rate/100);
                monthly_amortization = advance_interest;
                service_fee = principal * (3/100);
                if(notaryFeeResponse.type == 'fixed') {
                    notary_fee = notaryFeeResponse.notary_fee;
                } else if(notaryFeeResponse.type == 'percentage') {
                    notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                }
            }

            var reloan_deduction = parseFloat($('.reloan_deduction').text().replace(/,/g, '')) || 0;

            var membership_fee = parseFloat($('#membership_fee').val().replace(/,/g, '')) || 0;
            var principal_advance = parseFloat($('#principal_advance').val().replace(/,/g, '')) || 0;

            var total_deductions = Math.floor(advance_interest) + Math.ceil(service_fee) + Math.floor(insurance_fee) + Math.floor(notary_fee) + Math.floor(reloan_deduction) + Math.floor(membership_fee) + Math.floor(principal_advance);

            var net_proceeds = principal - total_deductions;


            $('.principal').text(currencyFormat(principal))
            $('.advance_interest').text(currencyFormat(Math.floor(advance_interest)))
            $('.service_fee').text(currencyFormat(Math.ceil(service_fee)))
            $('.insurance_fee').text(currencyFormat(Math.floor(insurance_fee)))
            $('.notary_fee').text(currencyFormat(Math.floor(notary_fee)))
            $('.monthly_amortization').text(currencyFormat(monthly_amortization))
            $('.net_proceeds').text(currencyFormat(Math.floor(net_proceeds)))

        }

        function getNotaryFee(principal) {
            return $.ajax({
                type: 'GET',
                url: '{{ route('get_notary_fee') }}',
                data: {amount:principal},
            })
        }
        function getInsuranceFee(principal) {
            return $.ajax({
                type: 'GET',
                url: '{{ route('get_insurance_fee') }}',
                data: {amount:principal},
            })
        }
    })

    function declineLoan(id) {
        swal({
            title: 'Are you sure?',
            text: "Do you want to decline this loan?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/loan/decline/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('.loan-status-button').remove()
                    }
                });

            }
        })
    }

    function withdrawnLoan(id) {
        swal({
            title: 'Are you sure?',
            text: "Do you want to withdraw this loan??",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/loan/withdrawn/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('.loan-status-button').remove()
                    }
                });

            }
        })
    }

    function approveLoan(id) {
        swal({
            title: 'Are you sure?',
            text: "Do you want to approve this loan?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/loan/approve/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('.loan-status-button').remove()
                    }
                });

            }
        })
    }

    function requestApproval(id) {
        swal({
            title: 'Are you sure?',
            text: "Do you want to request approval to this loan?.",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/loan/request-approve/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                    }
                });

            }
        })
    }

    hideElementsOnLoad();

    function hideElementsOnLoad() {
        $('.comaker-search-section').hide();

        $('#client_comaker thead tr th:last-child').remove();
        $('#client_comaker tbody tr td:last-child').remove();

        $('#documents_table thead tr th:last-child').remove();
        $('#documents_table tbody tr td:last-child').remove();
        $('#addFileRow').hide();
    }
</script>
@endpush