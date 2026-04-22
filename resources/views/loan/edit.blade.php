@extends('layouts.app')

@section('title') Edit Loan @endsection
@push('css')
<style>
    ul > li > span {
        float: right;
    }

    .form-group {
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Loan | Edit Loan</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item">Loan</li>
                    <li class="breadcrumb-item active">Edit Loan</li>
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
                <input type="hidden" name="loan_id" id="loan_id" value="{{$loan->id}}"/>
                <div class="form-group">
                    <label for="loan_date">Loan Date</label>
                    <input type="date" class="form-control form-control-sm" name="loan_date" id="loan_date" value="{{$loan->loan_date}}"/>
                </div>
                <div class="form-group">
                    <label for="borrower_id">Borrower</label>
                    <select name="borrower_id" id="borrower_id" class="form-control form-control-sm">
                        <option value="" disabled>-Select-</option>
                        @foreach ($clients as $client)
                            <option value="{{$client->id}}" {{ $client->id == $loan->borrower_id ? 'selected' : ''}}>{{$client->details->fullname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="loan_product_id">Loan Product</label>
                    <select name="loan_product_id" id="loan_product_id" class="form-control form-control-sm">
                        <option value="" disabled selected>-Select-</option>
                        @foreach ($loan_products as $loan_product)
                            <option value="{{$loan_product->id}}" {{ $loan_product->id == $loan->loan_product_id ? 'selected' : '' }} data-loan-type={{Str::slug($loan_product->loanType->name)}} data-interest-rate="{{$loan_product->loanType->interest_rate}}">{{$loan_product->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="principal">Principal</label>
                    <input type="text" class="form-control form-control-sm text-end decimal-only" name="principal" id="principal" value="{{number_format($loan->principal,2)}}" min="1" placeholder="0" onblur="numberFormat(this)"/>
                </div>
                <div class="form-group term">
                    <label for="term">Term (months)</label>
                    <input type="number" class="form-control form-control-sm text-end" name="term" id="term" step="0.5" value="{{$loan->loan_duration}}"/>
                </div>
                <div class="form-group interest-rate">
                    <label for="interest_rate">Interest Rate</label>
                    <input type="number" class="form-control form-control-sm text-end" name="interest_rate" id="interest_rate" min="0" value="{{$loan->interest_rate}}"/>
                </div>
                <div class="form-group interest-advance">
                    <label for="interest_advance">Interest Advance</label>
                    <input type="number" class="form-control form-control-sm text-end" name="interest_advance" id="interest_advance" min="0" placeholder="0" value="{{ $loan->loanCharges->interest_advance }}"/>
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
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="advance_interest" id="advance_interest" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="service_fee">Service Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_service_fee" id="override_service_fee">
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="service_fee" id="service_fee" placeholder="0.00">
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="insurance">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="insurance_fee">Insurance Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_insurance_fee" id="override_insurance_fee">
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="insurance_fee" id="insurance_fee" placeholder="0.00">
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="notary">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Notary Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_notary_fee" id="override_notary_fee">
                                    <label for="override_notary_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="notary_fee" id="notary_fee" placeholder="0.00">
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="membership">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="membership_fee">Membership Fee</label>
                                <div class="ms-auto gap-3">
                                    <input type="checkbox" class="form-check-input over-ride" name="override_membership_fee" id="override_membership_fee">
                                    <label for="override_membership_fee" class="form-label text-muted">override?</label>
                                </div>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" readonly name="membership_fee" id="membership_fee" value="{{ number_format($loan->loanCharges->membership_fee_paid,2) }}" data-membership-fee="{{number_format(config('settings.membership_fee'),2)}}" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="redeem_charges_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="redeem_charges">Redeem Charges</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="redeem_charges" id="redeem_charges" value="{{ number_format($loan->loanCharges->redeem_charges,2) }}" placeholder="0.00" onblur="numberFormat(this)"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="principal_advance_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="principal_advance">Principal Advance</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="principal_advance" id="principal_advance" value="{{ number_format($loan->loanCharges->principal_advance,2) }}" placeholder="0.00"/>
                            </div>
                        </li>
                        <li class="mt-3 mb-3" id="paid_interest_advance_container">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="paid_interest_advance">Interest Advance Payment</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end decimal-only" name="paid_interest_advance" id="paid_interest_advance" placeholder="0.00" readonly value="{{ number_format($loan->loanCharges->paid_interest_advance,2) }}"/>
                            </div>
                        </li>
                        @if($loan->is_reloan)
                        <li class="mt-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="notary_fee">Reloan Deduction</label>
                                <input type="text" class="form-control form-control-sm w-25 text-end" readonly name="reloan_deduction" id="reloan_deduction" value="{{$loan->loanCharges->reloan_deduction}}" placeholder="0.00"/>
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
                            <input type="text" class="form-control form-control-sm w-25 text-end" name="monthly_amortization" id="monthly_amortization">
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
        <button type="submit" class="btn btn-primary float-end btn-update-loan"><i class="fa fa-save"></i> Update Loan</button>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {

        loan_type = $("[name='loan_product_id']").find('option:selected').data('loan-type');

        interest_rate = $("[name='loan_product_id']").find('option:selected').data('interest-rate');

        if(loan_type == 'loan-with-term') {
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
        } else {
                $(".term").hide().removeAttr("required");
                $(".special-description").hide().removeAttr("required");
        }

        calculateLoan();

        var loan_type;
        var interest_rate;
        $("[name='loan_product_id']").on('change', function() {
            loan_type = $(this).find('option:selected').data('loan-type');

            interest_rate = $(this).find('option:selected').data('interest-rate');
            $("[name='interest_rate']").val(interest_rate)

            $("#insurance").show()
            $("#notary").show()

            if(loan_type == 'loan-with-term') {
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
            var principal = parseFloat($("[name='principal']").val().replace(/,/g, ''))
            interest_rate = parseFloat($("[name='interest_rate']").val())
            var term = parseInt($("[name='term']").val())

            // override
            var override_advance_interest = $("#override_advance_interest").prop("checked");
            var override_service_fee = $("#override_service_fee").prop("checked");
            var override_insurance_fee = $("#override_insurance_fee").prop("checked");
            var override_notary_fee = $("#override_notary_fee").prop("checked");
            var override_membership_fee = $("#override_membership_fee").prop("checked");

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

            var reloan_deduction = 0;
            if($('#reloan_deduction').length) {
                reloan_deduction = parseFloat($('#reloan_deduction').val().replace(/,/g, '')) || 0;
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
                $('.btn-update-loan').attr('disabled', false)
            } else {
                $('.btn-update-loan').attr('disabled', true)
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


        $('.btn-update-loan').on('click', function() {
            $(this).attr("disabled", true);
            updateLoan()
        })

        function updateLoan() {
            let formData = new FormData();

            // basic fields
            formData.append('id', $('#loan_id').val());
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
            formData.append('monthly_amortization', $('#monthly_amortization').val().replace(/,/g, ''));
            formData.append('net_proceeds', $('#net_proceeds').val().replace(/,/g, ''));
            formData.append('membership_fee', $('#membership_fee').val().replace(/,/g, ''));
            formData.append('redeem_charges', $('#redeem_charges').val().replace(/,/g, ''));
            formData.append('principal_advance', $('#principal_advance').val().replace(/,/g, ''));
            formData.append('interest_advance', $('#interest_advance').val().replace(/,/g, ''));
            formData.append('paid_interest_advance', $('#paid_interest_advance').val().replace(/,/g, ''));

            // Needed for PUT method with FormData in Laravel
            formData.append('_method', 'PUT');

            // co-makers
            $('#client_comaker tbody tr').each(function() {
                const id = $(this).data('id');
                // Or if you are using hidden inputs:
                // const id = $(this).find('input[name="client_comaker[]"]').val();
                if(id) {
                    formData.append('client_comaker[]', id);
                }
            });

            // documents (files)
            $('#documents_table tbody tr').each(function(index) {
                const $row = $(this);

                const docType = $row.find('select[name*="[doc_type]"]').val();
                const fileInput = $row.find('input[type="file"]')[0];
                const remarks = $row.find('input[type="text"][name*="[file_upload_remarks]"]').val();

                const existingId = $row.data('id');

                // Get original values
                const originalDocType = $row.data('original-doc-type');
                const originalRemarks = $row.data('original-remarks');

                if (existingId) {
                    // Always send ID
                    formData.append('existing_document_ids[]', existingId);

                    // 🔥 Detect changes
                    const docTypeChanged = docType != originalDocType;
                    const remarksChanged = remarks != originalRemarks;

                    if (docTypeChanged || remarksChanged) {
                        formData.append(`updated_documents[${index}][id]`, existingId);
                        formData.append(`updated_documents[${index}][document_type_id]`, docType);
                        formData.append(`updated_documents[${index}][file_upload_remarks]`, remarks || '');
                    }
                }

                // Handle new uploads
                if (docType && fileInput && fileInput.files.length > 0) {
                    formData.append(`client_documents[${index}][document_type_id]`, docType);
                    formData.append(`client_documents[${index}][file_upload]`, fileInput.files[0]);
                    formData.append(`client_documents[${index}][file_upload_remarks]`, remarks || '');
                }
            });

            $.ajax({
                type: 'POST', // Use POST with _method: PUT for FormData
                url: `{{route('loan.update', ':id')}}`.replace(':id', $('#loan_id').val()),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response)
                    if (response.status == 'success') {
                        showToast.success(response.message)
                        setTimeout(() => {
                        window.location.href = "{{ route('loan.show', ':id') }}".replace(':id', $('#loan_id').val());
                        }, 1000);
                    } else {
                        showToast.error('Failed to update loan')
                    }
                },
                error: function(xhr, status, error) {
                    let response = xhr.responseJSON;
                    if (response?.errors) {
                        let messages = Object.values(response.errors).flat().join('<br>');
                        showToast.error(messages);
                    } else if (response?.message) {
                        showToast.error(response.message);
                    } else {
                        showToast.error('Something went wrong.');
                    }
                    $('.btn-update-loan').prop('disabled', false);
                }
            })
        }

    })
</script>
@endpush