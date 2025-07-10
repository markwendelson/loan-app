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
                <h4>Loan | View Loan - {{$loan->loan_no}}</h4>
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
    <div class="col-6">
        <div class="card">
            <div class="card-header">View Loan</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="loan_date">Loan Date</label>
                    <input type="date" class="form-control" name="loan_date" id="loan_date" value="{{$loan->loan_date}}" readonly/>
                </div>
                <div class="form-group">
                    <label for="borrower_id">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control" disabled>
                        <option value="" disabled>-Select-</option>
                        @foreach ($clients as $client)
                            <option value="{{$client->id}}" {{ $client->id == $loan->borrower_id ? 'selected' : ''}}>{{$client->details->fullname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id">Loan Product</label>
                    <select name="loan_product_id" id="loan_product_id" class="form-control" disabled>
                        <option value="" disabled selected>-Select-</option>
                        @foreach ($loan_products as $loan_product)
                            <option value="{{$loan_product->id}}" {{ $loan_product->id == $loan->loan_product_id ? 'selected' : '' }} data-loan-type={{Str::slug($loan_product->loanType->name)}} data-interest-rate="{{$loan_product->loanType->interest_rate}}">{{$loan_product->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group pricipal">
                    <label for="principal">Principal</label>
                    <input type="number" class="form-control" name="principal" id="principal" value="{{$loan->principal}}" readonly/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="number" class="form-control" name="term" id="term" value="{{$loan->loan_duration}}" readonly/>
                </div>
                <div class="form-group interest-rate">
                    <label for="interest_rate">Interest Rate</label>
                    <input type="number" class="form-control" name="interest_rate" id="interest_rate" value="{{$loan->interest_rate}}" readonly/>
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
    <div class="col-6">
        <div class="card">
            <div class="card-header">Loan Summary</div>
            <div class="card-body">
                <ul>
                    <li>Principal Loan <span class="principal"></span></li>
                    <li class="mt-3 mb-3">Deductions</li>
                    <ul style="margin-left: 1rem;">
                        <li>Advance Interest <span class="advance_interest">{{$loan->loanCharges->advance_interest}}</span></li>
                        <li>Service Fee <span class="service_fee">{{$loan->loanCharges->service_fee}}</span></li>
                        <li>Insurance Fee <span class="insurance_fee">{{$loan->loanCharges->insurance_fee}}</span></li>
                        <li>Notary Fee <span class="notary_fee">{{$loan->loanCharges->notary_fee}}</span></li>
                        <li>Reloan Deduction <span class="reloan_deduction">{{$loan->loanCharges->reloan_deduction}}</span></li>
                    </ul>
                    <li class="mt-3 mb-3">Net Proceeds <span class="net_proceeds"></span></li>
                    <li class="mt-3 mb-3">Monthly Amortization <span class="monthly_amortization"></span></li>
                </ul>
            </div>
            @if($loan->status == App\Enums\LoanStatus::PENDING->value)
            <div class="card-footer loan-status-button">
                <div class="col-12 float-end">
                    <a href="{{route('loan.edit',$loan->id)}}" class="btn btn-sm btn-primary mx-auto btn-edit-loan"><i class="fa fa-pencil"></i> Edit</a>
                    <button type="button" class="btn btn-sm btn-warning mx-auto" onclick="withdrawnLoan({{$loan->id}})"><i class="fa fa-undo"></i> Withdrawn</button>
                    <button type="button" class="btn btn-sm btn-success mx-auto" onclick="approveLoan({{$loan->id}})"><i class="fa fa-check"></i> Approve</button>
                    <button type="button" class="btn btn-sm btn-danger mx-auto" onclick="declineLoan({{$loan->id}})"><i class="fa fa-remove"></i> Decline</button>
                    <button type="button" class="btn btn-sm btn-success mx-auto mt-1" onclick="requestApproval({{$loan->id}})"><i class="fa fa-check"></i> Request Approval</button>
                </div>
            </div>
            @endif
        </div>
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
            var principal = parseFloat($("[name='principal']").val())
            interest_rate = parseFloat($("[name='interest_rate']").val())
            var term = parseInt($("[name='term']").val())


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
                service_fee = principal * (5/100);
                insurance_fee = 0;
                if(notaryFeeResponse.type == 'fixed') {
                    notary_fee = notaryFeeResponse.notary_fee;
                } else if(notaryFeeResponse.type == 'percentage') {
                    notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                }
            } else if(loan_type == 'open-term') {
                notaryFeeResponse = await getNotaryFee(principal);
                inuranceFeeResponse = await getInsuranceFee(principal);

                advance_interest = principal * (interest_rate/100);
                monthly_amortization = advance_interest;
                service_fee = principal * (3/100);
                insurance_fee = inuranceFeeResponse.annual;
                if(notaryFeeResponse.type == 'fixed') {
                    notary_fee = notaryFeeResponse.notary_fee;
                } else if(notaryFeeResponse.type == 'percentage') {
                    notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                }
            }

            var reloan_deduction = parseFloat($('#reloan_deduction').val()) || 0;

            var total_deductions = Math.floor(advance_interest) + Math.ceil(service_fee) + Math.floor(insurance_fee) + Math.floor(notary_fee) + Math.floor(reloan_deduction);

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
</script>
@endpush