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
                            <option value="{{$client->id}}" data-bid="{{$previous_loan?->borrower_id}}" {{$previous_loan?->borrower_id==$client->id ? 'selected' : ''}}>{{$client->details->fullname}}</option>
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
                <div class="form-group special-description">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" name="description" id="description"/>
                </div>
                <div class="form-group">
                    <label for="principal">Principal</label>
                    <input type="number" class="form-control" name="principal" id="principal"/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="number" class="form-control" name="term" id="term" step="0.5"/>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <label>Principal Loan</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end principal" readonly>
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
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="advance_interest" id="advance_interest">
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="service_fee">Service Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_service_fee" id="override_service_fee">
                                    <label for="override_service_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="service_fee" id="service_fee">
                            </div>
                        </li>
                        @if(!filter_var(request('additional_loan'), FILTER_VALIDATE_BOOLEAN))
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="insurance_fee">Insurance Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_insurance_fee" id="override_insurance_fee">
                                    <label for="override_insurance_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="insurance_fee" id="insurance_fee">
                            </div>
                        </li>
                        @endif
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Notary Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_notary_fee" id="override_notary_fee">
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="notary_fee" id="notary_fee">
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
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="special_loan_balance" id="special_loan_balance" value="{{ $special_loan_balance }}">
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Reloan Deduction</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="reloan_deduction" id="reloan_deduction" value="{{$reloan_deduction??''}}">
                            </div>
                        </li>
                        @endif
                    </ul>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="net_proceeds">Net Proceeds</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="net_proceeds" id="net_proceeds">
                        </div>
                    </li>
                    <li class="mt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="monthly_amortization">Monthly Amortization</label>
                            <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="monthly_amortization" id="monthly_amortization">
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

    const urlParams = new URLSearchParams(window.location.search);

    $(document).ready(function () {

        // jQuery Combined Decimal Input Handler
        $(document).on('input', '.decimal-only', function () {
        let val = this.value;

        // Remove all characters except digits and dot
        val = val.replace(/[^0-9.]/g, '');

        // Prevent multiple dots
        const parts = val.split('.');
        if (parts.length > 2) {
            val = parts[0] + '.' + parts[1];
        }

        // Limit to 2 decimal places
        if (parts[1]) {
            parts[1] = parts[1].substring(0, 2);
            val = parts[0] + '.' + parts[1];
        }

        this.value = val;

        calculateLoan();

        });

        // Optional: prevent typing multiple dots directly
        $(document).on('keypress', '.decimal-only', function (e) {
        if (e.which === 46 && $(this).val().includes('.')) {
            e.preventDefault();
        }
        });


        var loan_type;
        var interest_rate;

        $(".term").hide().removeAttr("required");
        $(".special-description").hide().removeAttr("required");

        $("[name='loan_product_id']").on('change', function() {
            loan_type = $(this).find('option:selected').data('loan-type');

            interest_rate = $(this).find('option:selected').data('interest-rate');
            $("[name='interest_rate']").val(interest_rate)

            if(loan_type == 'loan-with-term') {
                $(".term").show().attr("required", true);
            } else if(loan_type == 'special-loan') {
                $(".term").show().attr("required", true);
                $(".special-description").show().attr("required", true);
                $("#term").val(12)
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

        $("[name='principal'],[name='interest_rate']").on('change', function() {
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
            var principal = parseFloat($("[name='principal']").val())
            interest_rate = parseFloat($("[name='interest_rate']").val())
            var term = parseFloat($("[name='term']").val())

            // override
            var override_advance_interest = $("#override_advance_interest").prop("checked");
            var override_service_fee = $("#override_service_fee").prop("checked");
            var override_insurance_fee = $("#override_insurance_fee").prop("checked");
            var override_notary_fee = $("#override_notary_fee").prop("checked");

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

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val()
                } else {
                    advance_interest = principal - capital_amount;
                }

                monthly_amortization = principal / term;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val()
                } else {
                    service_fee = capital_amount * (5/100);
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val()
                } else {
                    insurance_fee = principal * (2.5/100);
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val()
                } else {
                    notary_fee = principal * (3/100);
                }
            } else if(loan_type == 'auto-debit') {
                notaryFeeResponse = await getNotaryFee(principal);
                inuranceFeeResponse = await getInsuranceFee(principal);

                if(override_advance_interest) {
                    advance_interest = $('#advance_interest').val()
                } else {
                    advance_interest = principal * (interest_rate/100);
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val()
                } else {
                    service_fee = principal * (5/100);
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val()
                } else {
                    insurance_fee = inuranceFeeResponse.annual; // from insurance table
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val()
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
                    advance_interest = $('#advance_interest').val()
                } else {
                    advance_interest = (interest_rate/100) * principal * term;
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val()
                } else {
                    if(principal<3000) {
                        service_fee = 200;
                    } else if(principal==3000) {
                        service_fee = 300;
                    } else {
                        service_fee = principal * (5/100);
                    }
                }

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val()
                } else {
                    insurance_fee = 0;
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val()
                } else {
                    if(notaryFeeResponse.type == 'fixed') {
                        notary_fee = notaryFeeResponse.notary_fee;
                    } else if(notaryFeeResponse.type == 'percentage') {
                        notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                    }
                }
            } else if(loan_type == 'open-term') {

                if(override_insurance_fee) {
                    insurance_fee = $('#insurance_fee').val()
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
                    advance_interest = $('#advance_interest').val()
                } else {
                    advance_interest = principal * (interest_rate/100);
                }

                monthly_amortization = advance_interest;

                if(override_service_fee) {
                    service_fee = $('#service_fee').val()
                } else {
                    service_fee = principal * (3/100);
                }

                if(override_notary_fee) {
                    notary_fee = $('#notary_fee').val()
                } else {
                    if(notaryFeeResponse.type == 'fixed') {
                        notary_fee = notaryFeeResponse.notary_fee;
                    } else if(notaryFeeResponse.type == 'percentage') {
                        notary_fee = principal * (notaryFeeResponse.notary_fee/100);
                    }
                }
            }

            var is_reloan = urlParams.get('reloan')
            if(is_reloan) {
                var reloan_deduction = parseFloat($('#reloan_deduction').val().replace(/,/g, '')) || 0;
            } else {
                var reloan_deduction = 0;
            }

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
                    $("#reloan_deduction").val(reloan_deduction_fee)
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

            let params = {
                loan_date: $('#loan_date').val(),
                borrower_id: $('#borrower_id').val(),
                loan_product_id: $('#loan_product_id').val(),
                loan_duration: $('#term').val(),
                interest_rate: $('#interest_rate').val(),
                applied_amount: $('#principal').val().replace(/,/g, ''),
                advance_interest: $('#advance_interest').val().replace(/,/g, ''),
                service_fee: $('#service_fee').val().replace(/,/g, ''),
                insurance_fee: $('#insurance_fee').val().replace(/,/g, ''),
                notary_fee: $('#notary_fee').val().replace(/,/g, ''),
                monthly_amortization: $('#monthly_amortization').val().replace(/,/g, ''),
                net_proceeds: $('#net_proceeds').val().replace(/,/g, ''),
                reference_loan_no: urlParams.get('loan_no_reference'),
                is_reloan: urlParams.get('reloan'),
                is_additional_loan: urlParams.get('additional_loan'),
                description: $('#description').val(),
                special_loan_balance: $('#special_loan_balance').val().replace(/,/g, ''),
                include_special_loan_balance: $('#include_special_loan_balance').is(':checked') ? 1 : 0,
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