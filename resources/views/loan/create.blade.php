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
                <h4>Loan | Add Loan</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item">Loan</li>
                    <li class="breadcrumb-item active">Add Loan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header">Add Loan</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="loan_date">Loan Date</label>
                    <input type="date" class="form-control" name="loan_date" id="loan_date" value="{{ date('Y-m-d') }}"/>
                </div>
                <div class="form-group">
                    <label for="borrower_id">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control">
                        <option value="" disabled>-Select-</option>
                        @foreach ($clients as $client)
                            <option value="{{$client->id}} {{$previous_loan?->borrower_id==$client->id ? 'selected' : ''}}">{{$client->details->fullname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id">Loan Product</label>
                    <select name="loan_product_id" id="loan_product_id" class="form-control" required>
                        <option value="" disabled selected>-Select-</option>
                        @foreach ($loan_products as $loan_product)
                            <option value="{{$loan_product->id}}" data-loan-type={{Str::slug($loan_product->loanType->name)}} data-interest-rate="{{$loan_product->loanType->interest_rate}}">{{$loan_product->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="principal">Principal</label>
                    <input type="number" class="form-control" name="principal" id="principal"/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="number" class="form-control" name="term" id="term"/>
                </div>
                <div class="form-group interest-rate">
                    <label for="interest_rate">Interest Rate</label>
                    <input type="number" class="form-control" name="interest_rate" id="interest_rate"/>
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
                    <li>
                        <div class="d-flex justify-content-between">
                            <label>Principal Loan</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end principal" readonly>
                        </div>
                    </li>
                    <li class="mt-3 mb-3">Deductions</li>
                    <ul style="margin-left: 1rem;">
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="advance_interest">Advance Interest</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="advance_interest" id="advance_interest">
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="service_fee">Service Fee</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="service_fee" id="service_fee">
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="insurance_fee">Insurance Fee</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="insurance_fee" id="insurance_fee">
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="notary_fee">Notary Fee</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="notary_fee" id="notary_fee">
                            </div>
                        </li>
                        @if(request()->has('reloan') && request()->has('loan_no_reference'))
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="notary_fee">Reloan Deduction</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="reloan_deduction" id="reloan_deduction" value="{{$reloan_deduction}}">
                            </div>
                        </li>
                        @endif
                    </ul>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="net_proceeds">Net Proceeds</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="net_proceeds" id="net_proceeds">
                        </div>
                    </li>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="monthly_amortization">Monthly Amortization</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" name="monthly_amortization" id="monthly_amortization">
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary float-end btn-add-loan"><i class="fa fa-save"></i> Add Loan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    $(document).ready(function () {

        var loan_type;
        var interest_rate;
        $("[name='loan_product_id']").on('change', function() {
            loan_type = $(this).find('option:selected').data('loan-type');

            interest_rate = $(this).find('option:selected').data('interest-rate');
            $("[name='interest_rate']").val(interest_rate)

            if(loan_type == 'loan-with-term' || loan_type == 'special-loan') {3
                $(".term").show().attr("required", true);
            } else {
                 $(".term").hide().removeAttr("required");
            }

            calculateLoan();

        })

        $("[name='principal'],[name='term'],[name='interest_rate']").on('change', function() {
            calculateLoan();
        })

        async function calculateLoan() {
            $('#btn-add-loan').attr('disabled', true)
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


            // $('.principal').text(currencyFormat(principal))
            // $('.advance_interest').text(currencyFormat(Math.floor(advance_interest)))
            // $('.service_fee').text(currencyFormat(Math.ceil(service_fee)))
            // $('.insurance_fee').text(currencyFormat(Math.floor(insurance_fee)))
            // $('.notary_fee').text(currencyFormat(Math.floor(notary_fee)))
            // $('.monthly_amortization').text(currencyFormat(monthly_amortization))
            // $('.net_proceeds').text(currencyFormat(Math.floor(net_proceeds)))
            $('.principal').val(currencyFormat(principal))
            $('#advance_interest').val(currencyFormat(Math.floor(advance_interest)))
            $('#service_fee').val(currencyFormat(Math.ceil(service_fee)))
            $('#insurance_fee').val(currencyFormat(Math.floor(insurance_fee)))
            $('#notary_fee').val(currencyFormat(Math.floor(notary_fee)))
            $('#monthly_amortization').val(currencyFormat(monthly_amortization))
            $('#net_proceeds').val(currencyFormat(Math.floor(net_proceeds)))

            console.log(net_proceeds)
            if(net_proceeds > 0) {
                // $('#btn-add-loan').attr('disabled', false)
            }

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


        $('.btn-add-loan').on('click', function() {
            $(this).attr("disabled", true);
            addLoan()
        })

        $("#loan_date").on("change", function() {
            const urlParams = new URLSearchParams(window.location.search);
            var loan_no_reference = urlParams.get('loan_no_reference')
            var reloan_date = $(this).val();
            getReloanDeductionFee(loan_no_reference, reloan_date).then(function(response) {
                var reloan_deduction_fee = response.amount
                $("#reloan_deduction").val(reloan_deduction_fee)
            })
        })

        function getReloanDeductionFee(loan_no_reference,reloan_date) {
            return $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `{{ route('loan.get-reloan-deduction') }}`,
                data: {loan_no_reference:loan_no_reference,reloan_date:reloan_date},
            })
        }

        function addLoan() {

            const urlParams = new URLSearchParams(window.location.search);

            let params = {
                loan_date: $('#loan_date').val(),
                borrower_id: $('#borrower_id').val(),
                loan_product_id: $('#loan_product_id').val(),
                loan_duration: $('#term').val(),
                interest_rate: $('#interest_rate').val(),
                applied_amount: $('#principal').val(),
                advance_interest: $('#advance_interest').val(),
                service_fee: $('#service_fee').val(),
                insurance_fee: $('#insurance_fee').val(),
                notary_fee: $('#notary_fee').val(),
                monthly_amortization: $('#monthly_amortization').val().replace(/,/g, ''),
                net_proceeds: $('#net_proceeds').val(),
                loan_no_reference: urlParams.get('loan_no_reference'),
                reloan: urlParams.get('reloan'),
            };

            $.ajax({
                type: 'POST',
                url: "{{ route('loan.store') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: params,
                success: function(data) {
                    console.log(data)
                    if (data.status == 'success') {
                        showToast.success(data.message)
                        window.location.href = "{{ route('loan.index') }}"
                    } else {
                        showToast.error(data.message)
                        $('.btn-add-loan').attr("disabled", false);
                    }
                },
                error: function(error) {
                    let response = error.responseJSON;

                    if (response.errors) {
                        let messages = Object.values(response.errors).flat().join('<br>');
                        showToast.error(messages);
                    } else if (response.message) {
                        showToast.error(response.message);
                    }
                    $('.btn-add-loan').attr("disabled", false);
                }
            })


        }

    })
</script>
@endpush