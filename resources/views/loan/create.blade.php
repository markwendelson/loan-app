@extends('layouts.app')

@section('title')New Loan @endsection

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
            <div class="card-header">Loan Information</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="loan_date">Loan Date</label>
                    <input type="date" class="form-control form-control-sm" name="loan_date" id="loan_date" value="{{ date('Y-m-d') }}"/>
                </div>
                <div class="form-group">
                    <label for="borrower_id">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control form-control-sm">
                        <option value="" disabled>-Select-</option>

                        @foreach ($clients as $client)
                            <option value="{{$client->id}}" data-bid="{{$previous_loan?->borrower_id}}" {{$previous_loan?->borrower_id==$client->id ? 'selected' : ''}}>{{$client->details->fullname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id">Loan Product</label>
                    <select name="loan_product_id" id="loan_product_id" class="form-control form-control-sm" required>
                        <option value="" disabled
                            @if(filter_var(request('additional_loan'), FILTER_VALIDATE_BOOLEAN)) selected @endif
                        >-Select-</option>
                        @foreach ($loan_products as $loan_product)
                            <option
                                value="{{$loan_product->id}}"
                                data-loan-type={{Str::slug($loan_product->loanType->name)}}
                                data-interest-rate="{{$loan_product->loanType->interest_rate}}"
                                @if(filter_var(request('additional_loan'), FILTER_VALIDATE_BOOLEAN)) selected @endif
                            >{{$loan_product->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group special-description">
                    <label for="description">Description</label>
                    <input type="text" class="form-control form-control-sm" name="description" id="description"/>
                </div>
                <div class="form-group">
                    <label for="principal">Principal</label>
                    <input type="text" class="form-control form-control-sm decimal-only text-end" name="principal" id="principal" min="1" placeholder="0" onblur="numberFormat(this)"/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="number" class="form-control form-control-sm text-end" name="term" id="term" step="0.5"/>
                </div>
                <div class="form-group interest-rate">
                    <label for="interest_rate">Interest Rate</label>
                    <input type="number" class="form-control form-control-sm text-end" name="interest_rate" id="interest_rate" min="0"/>
                </div>
                <!-- <div class="form-group interest-advance">
                    <label for="interest_advance">Interest Advance</label>
                    <input type="number" class="form-control form-control-sm text-end" name="interest_advance" id="interest_advance" min="0" placeholder="0"/>
                </div> -->
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
                        <div class="d-flex justify-content-between align-items-center">
                            <label>Principal Loan</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end principal" readonly placeholder="0.00"/>
                        </div>
                    </li>
                    <li class="mt-3 mb-3">Deductions</li>
                    <ul style="margin-left: 1rem;">
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="advance_interest">Advance Interest</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_advance_interest" id="override_advance_interest">
                                    <label for="override_advance_interest" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="advance_interest" id="advance_interest" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="service_fee">Service Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_service_fee" id="override_service_fee">
                                    <label for="override_service_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="service_fee" id="service_fee" placeholder="0.00"/>
                            </div>
                        </li>
                        @if(!filter_var(request('additional_loan'), FILTER_VALIDATE_BOOLEAN))
                        <li class="mt-3 mb-3" id="insurance">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="insurance_fee">Insurance Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_insurance_fee" id="override_insurance_fee">
                                    <label for="override_insurance_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="insurance_fee" id="insurance_fee" placeholder="0.00"/>
                            </div>
                        </li>
                        @endif
                        <li class="mt-3 mb-3" id="notary">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Notary Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_notary_fee" id="override_notary_fee">
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="notary_fee" id="notary_fee" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="membership">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="membership_fee">Membership Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_membership_fee" id="override_membership_fee">
                                    <label for="override_membership_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="membership_fee" id="membership_fee" value="{{number_format(config('settings.membership_fee'),2)}}" data-membership-fee="{{number_format(config('settings.membership_fee'),2)}}" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="redeem_charges_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="redeem_charges">Redeem Charges</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="redeem_charges" id="redeem_charges" placeholder="0.00" onblur="numberFormat(this)"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="principal_advance_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="principal_advance">Principal Advance</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="principal_advance" id="principal_advance" placeholder="0.00" onblur="numberFormat(this)"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="paid_interest_advance_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="paid_interest_advance">Interest Advance Payment</label>
                                <input type="number" class="form-control form-control-sm w-25 text-end" name="interest_advance" id="interest_advance" min="0" placeholder="# of Months"/>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="paid_interest_advance" id="paid_interest_advance" placeholder="0.00" readonly/>
                            </div>
                        </li>
                        @if(request()->has('reloan') && request()->has('loan_no_reference'))
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Special Loan Balance</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input special-loan-balance" name="include_special_loan_balance" id="include_special_loan_balance" checked>
                                    <label for="include_special_loan_balance" class="form-label text-muted">include?</label>
                                </div>
                                <input type="hidden" name="principal_loan_balance" id="principal_loan_balance" value="{{ $principal_loan_balance }}">
                                <input type="hidden" name="total_reloan_deduction" id="total_reloan_deduction" value="{{ $reloan_deduction }}">
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="special_loan_balance" id="special_loan_balance" value="{{ $special_loan_balance }}" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Reloan Deduction</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="reloan_deduction" id="reloan_deduction" value="{{$reloan_deduction??''}}" placeholder="0.00"/>
                            </div>
                        </li>
                        @endif
                    </ul>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="net_proceeds">Net Proceeds</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="net_proceeds" id="net_proceeds" placeholder="0.00"/>
                        </div>
                    </li>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="monthly_amortization">Monthly Amortization</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="monthly_amortization" id="monthly_amortization" placeholder="0.00"/>
                        </div>
                    </li>
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

<div class="row">
    <div class="col-12">
        <button type="submit" class="btn btn-primary float-end btn-add-loan"><i class="fa fa-save"></i> Add Loan</button>
    </div>
</div>

@endsection

@push('js')
<script>

    const urlParams = new URLSearchParams(window.location.search);

    $(document).ready(function () {

        var loan_type;
        var interest_rate;

        $(".term").hide().removeAttr("required");
        $(".special-description").hide().removeAttr("required");

        var is_additional_loan = urlParams.get('additional_loan')
        // if(is_additional_loan) {
            loan_type = $("[name='loan_product_id']").find('option:selected').data('loan-type');

            interest_rate = $("[name='loan_product_id']").find('option:selected').data('interest-rate');
            $("[name='interest_rate']").val(interest_rate)

            if(loan_type == 'loan-with-term') {
                $(".term").show().attr("required", true);
            } else if(loan_type == 'special-loan') {
                $(".term").show().attr("required", true);
                $(".special-description").show().attr("required", true);
                $("#term").val(12)
            } else if(loan_type == 'mortgage-loan') {
                $(".special-description").show().attr("required", true);
            } else {
                 $(".term").hide().removeAttr("required");
                 $(".special-description").hide().removeAttr("required");
            }

            // calculateLoan();
        // }


        $("[name='loan_product_id']").on('change', function() {
            loan_type = $(this).find('option:selected').data('loan-type');

            interest_rate = $(this).find('option:selected').data('interest-rate');
            $("[name='interest_rate']").val(interest_rate)

            $("#insurance").show()
            $("#notary").show()

            if(loan_type == 'loan-with-term') {
                $(".special-description").hide().removeAttr("required");
                $(".term").show().attr("required", true);
            } else if(loan_type == 'special-loan') {
                $(".term").show().attr("required", true);
                $(".special-description").show().attr("required", true);
                $("#term").val(12)
            } else if(loan_type == 'mortgage-loan') {
                $(".term").show().attr("required", true);
                $("#term").val(12)
                $("#insurance").hide()
                $("#notary").hide()
                $(".special-description").show().attr("required", true);
            } else {
                 $(".term").hide().removeAttr("required");
                 $(".special-description").hide().removeAttr("required");
            }

            calculateLoan();

        })

        $("[name='term']").on("change", function() {
            var loan_type = $("[name='loan_product_id']").find('option:selected').data('loan-type');
            var interest_rate = $("[name='loan_product_id']").find('option:selected').data('interest-rate');
            if(loan_type == 'special-loan') {
                if($(this).val() != 12) {
                    $("[name='interest_rate']").val(5)
                } else {
                    $("[name='interest_rate']").val(interest_rate)
                }
            }
            calculateLoan();
        })

        $("[name='principal'],[name='interest_rate'],[name='principal_advance'],[name='interest_advance'],[name='redeem_charges']").on('change', function() {
            calculateLoan();
        })

        $('.over-ride').on("change", function () {
            const isChecked = $(this).prop("checked");

            // Find the closest wrapper with .d-flex, then find the input[type=text] inside it
            const $input = $(this).closest('.d-flex').find('input[type="text"]');

            $input.prop("readonly", !isChecked);

            calculateLoan()
        });

        async function calculateLoan() {
            $('#btn-add-loan').attr('disabled', true)
            var principal = parseFloat($("[name='principal']").val().replace(/,/g, ''))
            interest_rate = parseFloat($("[name='interest_rate']").val())
            var term = parseFloat($("[name='term']").val())

            // override
            var override_advance_interest = $("#override_advance_interest").prop("checked");
            var override_service_fee = $("#override_service_fee").prop("checked");
            var override_insurance_fee = $("#override_insurance_fee").prop("checked");
            var override_notary_fee = $("#override_notary_fee").prop("checked");
            var override_membership_fee = $("#override_membership_fee").prop("checked");

            if (isNaN(principal) || isNaN(interest_rate) || (loan_type === 'loan-with-term' && isNaN(term))) {
                console.log(principal)
                console.log(interest_rate)
                console.log(loan_type)
                console.log(term)
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

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val().replace(/,/g, '') || 0
                } else {
                    advance_interest = principal - capital_amount;
                }

                monthly_amortization = principal / term;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val().replace(/,/g, '') || 0
                } else {
                    service_fee = capital_amount * (5/100);
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val().replace(/,/g, '') || 0
                } else {
                    insurance_fee = principal * (2.5/100);
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val().replace(/,/g, '') || 0
                } else {
                    notary_fee = principal * (3/100);
                }
            } else if(loan_type == 'auto-debit') {
                notaryFeeResponse = await getNotaryFee(principal);
                inuranceFeeResponse = await getInsuranceFee(principal);

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val().replace(/,/g, '') || 0
                } else {
                    advance_interest = principal * (interest_rate/100);
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val().replace(/,/g, '') || 0
                } else {
                    service_fee = principal * (5/100);
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val().replace(/,/g, '') || 0
                } else {
                    insurance_fee = inuranceFeeResponse.annual; // from insurance table
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val().replace(/,/g, '') || 0
                } else {
                    if(notaryFeeResponse.type == 'fixed') {
                        notary_fee = notaryFeeResponse.notary_fee;
                    } else if(notaryFeeResponse.type == 'percentage') {
                        notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                    }
                }

            } else if(loan_type == 'special-loan') {
                notaryFeeResponse = await getNotaryFee(principal);

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val().replace(/,/g, '') || 0
                } else {
                    advance_interest = (interest_rate/100) * principal * term;
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val().replace(/,/g, '') || 0
                } else {
                    if(principal<=1000) {
                        service_fee = 100;
                    } else if(principal<3000) {
                        service_fee = 200;
                    } else if(principal<6000) {
                        service_fee = 300;
                    } else if(principal<11000) {
                        service_fee = 500;
                    } else {
                        service_fee = principal * (5/100);
                    }
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val().replace(/,/g, '') || 0
                } else {
                    insurance_fee = 0;
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val().replace(/,/g, '') || 0
                } else {
                    if(notaryFeeResponse.type == 'fixed') {
                        notary_fee = notaryFeeResponse.notary_fee;
                    } else if(notaryFeeResponse.type == 'percentage') {
                        notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                    }
                }
            } else if(loan_type == 'open-term') {

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val().replace(/,/g, '') || 0
                } else {
                    var is_additional_loan = urlParams.get('additional_loan')
                    if(is_additional_loan) {
                        insurance_fee = 0;
                    } else {
                        inuranceFeeResponse = await getInsuranceFee(principal);
                        insurance_fee = inuranceFeeResponse.annual;
                    }
                }

                notaryFeeResponse = await getNotaryFee(principal);

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val().replace(/,/g, '') || 0
                } else {
                    advance_interest = principal * (interest_rate/100);
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val().replace(/,/g, '') || 0
                } else {
                    service_fee = principal * (3/100);
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val().replace(/,/g, '') || 0
                } else {
                    if(notaryFeeResponse.type == 'fixed') {
                        notary_fee = notaryFeeResponse.notary_fee;
                    } else if(notaryFeeResponse.type == 'percentage') {
                        notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                    }
                }
            } else if(loan_type == 'mortgage-loan') {
                insurance_fee = 0
                notary_fee = 0

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val().replace(/,/g, '') || 0
                } else {
                    advance_interest = principal * (interest_rate/100);
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val().replace(/,/g, '') || 0
                } else {
                    service_fee = principal * (6/100);
                }
            }

            var is_reloan = urlParams.get('reloan')
            if(is_reloan) {
                var reloan_deduction = parseFloat($('#reloan_deduction').val().replace(/,/g, '')) || 0;
            } else {
                var reloan_deduction = 0;
            }

            var membership_fee = parseFloat($('#membership_fee').val().replace(/,/g, '')) || 0;
            var redeem_charges = parseFloat($('#redeem_charges').val().replace(/,/g, '')) || 0;
            var interest_advance = parseFloat($('#interest_advance').val().replace(/,/g, '')) || 0;
            var paid_interest_advance = interest_advance * advance_interest;
            if(override_membership_fee) {
                membership_fee = parseFloat($('#membership_fee').val().replace(/,/g, '')) || 0
            } else {
                membership_fee = parseFloat($('#membership_fee').data("membership-fee").replace(/,/g, ''));
            }

            var rawValue = $('#principal_advance').val();
            var principal_advance = 0;
            if (rawValue) {
                principal_advance = parseFloat(rawValue.toString().replace(/,/g, '')) || 0;
            }

            var total_deductions = Math.floor(advance_interest)
                                + Math.ceil(service_fee)
                                + Math.floor(insurance_fee)
                                + Math.floor(notary_fee)
                                + Math.floor(reloan_deduction)
                                + Math.floor(membership_fee)
                                + Math.floor(redeem_charges)
                                + Math.floor(principal_advance)
                                + Math.floor(paid_interest_advance);

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
            $('#membership_fee').val(currencyFormat(Math.floor(membership_fee)))
            $('#paid_interest_advance').val(currencyFormat(Math.floor(paid_interest_advance)))
            $('#monthly_amortization').val(currencyFormat(monthly_amortization))
            $('#net_proceeds').val(currencyFormat(Math.floor(net_proceeds)))

            if(net_proceeds > 0) {
                $('.btn-add-loan').attr('disabled', false)
            } else {
                $('.btn-add-loan').attr('disabled', true)
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
            var reference_loan_no = urlParams.get('loan_no_reference')
            var is_reloan = urlParams.get('reloan')
            if(is_reloan) {
                var reloan_date = $(this).val();
                getReloanDeductionFee(reference_loan_no, reloan_date).then(function(response) {
                    var reloan_deduction_fee = response.amount
                    var principal_loan_balance = response.principal_loan_balance
                    var special_loan_balance = response.special_loan_balance
                    if ($('#include_special_loan_balance').is(':checked')) {
                        $("#reloan_deduction").val(reloan_deduction_fee)
                    } else {
                        $("#reloan_deduction").val(principal_loan_balance);
                    }
                    $("#principal_loan_balance").val(principal_loan_balance)
                    $("#total_reloan_deduction").val(reloan_deduction_fee)
                    $("#special_loan_balance").val(special_loan_balance)
                })
            }
            calculateLoan()
        })

        $('#include_special_loan_balance').on('change', function() {
            if ($(this).is(':checked')) {
                let reloanReduction = $("#total_reloan_deduction").val();
                $("#reloan_deduction").val(reloanReduction);
            } else {
                let principalLoanBalance = $("#principal_loan_balance").val();
                $("#reloan_deduction").val(principalLoanBalance);
            }
            calculateLoan()
        });

        function getReloanDeductionFee(reference_loan_no,reloan_date) {
            return $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `{{ route('loan.get-reloan-deduction') }}`,
                data: {reference_loan_no:reference_loan_no,reloan_date:reloan_date},
            })
        }

        function addLoan() {

            let formData = new FormData();

            // basic fields
            formData.append('loan_type', loan_type);
            formData.append('loan_date', $('#loan_date').val());
            formData.append('borrower_id', $('#borrower_id').val());
            formData.append('loan_product_id', $('#loan_product_id').val());
            formData.append('loan_duration', $('#term').val());
            formData.append('interest_rate', $('#interest_rate').val());
            formData.append('applied_amount', $('#principal').val().replace(/,/g, ''));
            formData.append('advance_interest', $('#advance_interest').val().replace(/,/g, ''));
            formData.append('service_fee', $('#service_fee').val().replace(/,/g, ''));
            formData.append('insurance_fee', $('#insurance_fee').length ? $('#insurance_fee').val().replace(/,/g, '') : '0');
            formData.append('notary_fee', $('#notary_fee').val().replace(/,/g, ''));
            formData.append('membership_fee', $('#membership_fee').val().replace(/,/g, ''));
            formData.append('redeem_charges', $('#redeem_charges').val().replace(/,/g, ''));
            formData.append('principal_advance', $('#principal_advance').val().replace(/,/g, ''));
            formData.append('interest_advance', $('#interest_advance').val().replace(/,/g, ''));
            formData.append('paid_interest_advance', $('#paid_interest_advance').val().replace(/,/g, ''));
            formData.append('monthly_amortization', $('#monthly_amortization').val().replace(/,/g, ''));
            formData.append('net_proceeds', $('#net_proceeds').val().replace(/,/g, ''));
            formData.append('reference_loan_no', urlParams.get('loan_no_reference') ?? '');
            formData.append('is_reloan', urlParams.get('reloan') ?? '');
            formData.append('is_additional_loan', urlParams.get('additional_loan') ?? '');
            formData.append('description', $('#description').val());
            formData.append('special_loan_balance', $('#special_loan_balance').length ? $('#special_loan_balance').val().replace(/,/g, '') : '0');
            formData.append(
                'include_special_loan_balance',
                $('#include_special_loan_balance').length && $('#include_special_loan_balance').is(':checked') ? 1 : 0
            );

            // comakers (array)
            $('input[name="client_comaker[]"]').each(function () {
                var rawId = $(this).val();
                var coMakerId = String(rawId).replace('tmp_', '');
                formData.append('client_comaker[]', coMakerId);
            });

            // documents (files)
            $('#documents_table tbody tr').each(function(index) {
                const docType = $(this).find('select[name*="[doc_type]"]').val();
                const fileInput = $(this).find('input[type="file"]')[0];
                const remarks = $(this).find('input[type="text"][name*="[file_upload_remarks]"]').val();

                // const docType = $(this).find('select[name="client_documents[' + index + '][document_type_id]"]').val();
                // const fileInput = $(this).find('input[type="file"]')[0];
                // const remarks = $(this).find('input[name="client_documents[' + index + '][file_upload_remarks]"]').val();

                if (docType && fileInput.files.length > 0) {
                    formData.append(`client_documents[${index}][document_type_id]`, docType);
                    formData.append(`client_documents[${index}][file_upload]`, fileInput.files[0]);
                    formData.append(`client_documents[${index}][file_upload_remarks]`, remarks || '');
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{ route('loan.store') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false, // REQUIRED
                contentType: false, // REQUIRED
                success: function (data) {
                    if (data.status === 'success') {
                        showToast.success(data.message);
                        $('#client_comaker tbody').empty();
                        $('#documents_table tbody').empty();
                        window.location.href = "{{ route('loan.index') }}";
                    } else {
                        showToast.error(data.message);
                        $('.btn-add-loan').prop('disabled', false);
                    }
                },
                error: function (error) {
                    let response = error.responseJSON;

                    if (response?.errors) {
                        let messages = Object.values(response.errors).flat().join('<br>');
                        showToast.error(messages);
                    } else if (response?.message) {
                        showToast.error(response.message);
                    }

                    $('.btn-add-loan').prop('disabled', false);
                }
            });


        }

    })
</script>
@endpush