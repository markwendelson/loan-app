@extends('layouts.app')

@section('title') Manage Savings @endsection

@push('css')
<style>
    .bg-default {
        background-color: #1f807f;
    }

    .bg-default th {
        color: white;
        /* Set white text for <th> elements */
        border: 1px solid white;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}" />
<link rel="stylesheet" href="{{  asset('assets/css/choices.min.css') }}" />
@endpush

@section('content')

<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Savings | Manage Savings</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                    </li>
                    <li class="breadcrumb-item">Savings</li>
                    <li class="breadcrumb-item active">Manage Savings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@include('savings.client-info')

<div class="card">
    <div class="card-header">
        <h5>Transaction Information</h5>
    </div>
    <div class="card-body">
        <form id="frmTransactionModal" name="frmTransactionModal" class="g-3 needs-validation custom-input" novalidate="">
            @csrf
            <input type="hidden" value="" name="id" id="id">
            <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="transaction_date">Transaction Date</label>
                            <input type="date" class="form-control form-control-sm" name="transaction_date" placeholder="Transaction Date" value="{{ date('Y-m-d') }}" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="transaction_type">Transaction Type</label>
                            <select name="transaction_type" id="transaction_type" class="form-control form-control-sm">
                                <option value="">-select-</option>
                                @can('deposit-transactions')
                                <option value="deposit" data-mop="atm_withdrawal">Amount Withdrawn</option>
                                @endcan
                                @can('withdraw-transactions')
                                <option value="withdraw">Payment Transaction</option>
                                @endcan
                                @can('deposit-transactions')
                                <option value="deposit" data-mop="cash">Cash</option>
                                @endcan
                                @can('deposit-transactions')
                                <option value="deposit" data-mop="bank_transfer">Bank Transfer</option>
                                @endcan
                                @can('deposit-transactions')
                                <option value="deposit" data-mop="palawan">Palawan</option>
                                @endcan
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="mode_of_payment_container">
                            <div class="form-group">
                                <label class="form-label" for="mode_of_payment">Mode of Payment</label>
                                @php
                                    $modes = config('settings.mode_of_payment');
                                @endphp
                                <select name="mode_of_payment" id="mode_of_payment" class="form-control form-control-sm">
                                    @foreach($modes as $key => $mode)
                                        <option value="{{ $key }}">{{ $mode }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="mop_transfer_container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label" for="transaction_code">Transaction Code</label>
                                <input type="text" class="form-control form-control-sm" name="transaction_code" id="transaction_code" placeholder="Transaction Code"/>
                            </div>
                        </div>
                        <div class="col-md-12" id="mop_transfer_select_container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label" for="transaction_code">Transaction Code</label>
                                <select name="transaction_code_select[]" id="transaction_code" class="form-control" multiple></select>
                            </div>
                        </div>
                    </div>
                <div class="col-6 deposit-withdraw">
                    <div class="form-group">
                        <label class="form-label" for="amount">Amount</label>
                        <input type="text" class="form-control form-control-sm decimal-only text-end" name="amount" required autocomplete="off" placeholder="0.00"/>
                    </div>
                </div>
                <div class="col-6 deposit-withdraw">
                    <div class="form-group">
                        <label class="form-label" for="classifications">Classifications</label>
                        <select class="form-control form-control-sm" name="classifications" id="classifications">
                            <option value="">-select-</option>
                            <option value="Salary">Salary</option>
                            <option value="Receivable">Receivable</option>
                            <option value="Pension">Pension</option>
                            <option value="Allotment">Allotment</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 deposit-withdraw">
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <input class="form-control form-control-sm" type="text" name="description" id="description" placeholder="Description"/>
                    </div>
                </div>
                <div class="withdraw">
                    <div class="row mb-3">
                        <div class="col-12 form-group">
                            <label class="form-label" for="loan_id">Select Loan(s)</label>
                            <select name="loan_id[]" id="loan_id" class="form-control form-control-sm" multiple>
                                @foreach($loans as $loan)
                                    <option value="{{$loan->id}}"
                                        data-loan-product-id="{{$loan->loan_product_id}}"
                                        data-principal="{{$loan->principal}}"
                                        data-interest-rate="{{$loan->interest_rate}}"
                                        data-term="{{$loan->loan_duration}}"
                                        data-loan-type="{{Str::slug($loan->loanProduct->loanType->name)}}"
                                    >{{$loan->is_special ? 'Special Loan | '.$loan->description .' | ' .$loan->loan_no : 'Principal Loan | '.$loan->loan_no}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="loans_container"></div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-6">
                            <div class="form-group">
                                <label class="form-label" for="due_change">Change</label>
                                <input type="text" class="form-control form-control-sm decimal-only text-end" name="due_change" readonly min="0"/>
                            </div>
                            <div class="col d-none" id="change_container">
                                <div class="form-group">
                                    <label class="form-label" for="change_mop">Change - Mode of Payment</label>
                                    @php
                                        $modes = config('settings.mode_of_payment');
                                    @endphp
                                    <select name="change_mop" id="change_mop" class="form-control form-control-sm">
                                        @foreach($modes as $key => $mode)
                                            @if($key == 'atm_withdrawal')
                                            @else
                                            <option value="{{ $key }}">{{ $mode }}</option>
                                            @endif
                                        @endforeach
                                            <option value="authorized_person">Authorized Person</option>
                                    </select>
                                </div>
                                <div class="form-group attachment d-none">
                                    <label class="form-label" for="attachment">Attachment</label>
                                    <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,.pdf" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-footer gap-1">
        <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit</button>
        <a class="btn btn-light" href="{{ route('savings.transactions',['client_id' => $client->id]) }}"><i class="fa fa-close"></i> Cancel</a>
    </div>

</div>

@endsection
@push('js')
<script src="{{ asset('assets/js/datepicker/date-picker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/choices.min.js') }}"></script>
<script>
    $(document).ready(function () {
        let transaction_type;
        let choicesTransactionCode;
        let client_id = {{ $client->id }};
        let activeLoans = new Set();

        function parsePHPNumber(value) {
            if (value === null || value === undefined) return 0;
            if (typeof value === 'number') return value;
            let str = String(value).replace(/,/g, '');
            let parsed = parseFloat(str);
            return isNaN(parsed) ? 0 : parsed;
        }

        const clientLoansMap = {
            @foreach($loans as $loan)
                "{{ $loan->id }}": {
                    loan_type: "{{ Str::slug($loan->loanProduct->loanType->name) }}",
                    is_special: {{ $loan->is_special ? 'true' : 'false' }},
                    text: "{{ $loan->is_special ? 'Special Loan | '.$loan->description .' | ' .$loan->loan_no : 'Principal Loan | '.$loan->loan_no }}"
                },
            @endforeach
        };

        // Initialize Choices on loan_id select
        let choicesLoanId = new Choices(document.querySelector('#loan_id'), {
            removeItemButton: true,
            duplicateItemsAllowed: false,
            placeholder: true,
            placeholderValue: 'Select Loan(s)',
            searchEnabled: true,
        });

        transaction_type = $("[name='transaction_type']").find('option:selected').val();

        if (transaction_type == 'withdraw') {
            $(".withdraw").show()
            $("[name='amount']").prop('readonly', false);
            calculateChange('reset');
        } else {
            $(".withdraw").hide()
            $("[name='amount']").prop('readonly', false);
        }

        $("#mode_of_payment_container").hide();
        $('.withdraw').hide();
        $(".deposit-withdraw").hide();

        $("#transaction_type").change(function () {
            let type = $(this).val();
            if (type == "withdraw") {
                $("#mode_of_payment_container").show();
                $(".deposit-withdraw").show();
                $('.withdraw').show();
                $("[name='amount']").prop('readonly', false);
            } else if (type == "deposit" || type == "cash") {
                $("#mode_of_payment_container").hide();
                $(".deposit-withdraw").show();
                $('.withdraw').hide();
                $("[name='amount']").prop('readonly', false);
            } else {
                $("#mode_of_payment_container").hide();
                $(".deposit-withdraw").hide();
                $('.withdraw').hide();
                $("[name='amount']").prop('readonly', false);
            }
        });

        document.querySelector('#loan_id').addEventListener('addItem', function(event) {
            let loanId = event.detail.value;
            let loanInfo = clientLoansMap[loanId];
            let loanText = event.detail.label;

            activeLoans.add(loanId);
            renderLoanCard(loanId, loanText, loanInfo);
        });

        document.querySelector('#loan_id').addEventListener('removeItem', function(event) {
            let loanId = event.detail.value;
            activeLoans.delete(loanId);
            $(`#loan-card-${loanId}`).remove();
            calculateChange('card');
        });

        function renderLoanCard(loanId, loanText, loanInfo) {
            let isSpecial = loanInfo ? loanInfo.is_special : false;
            let loanType = loanInfo ? loanInfo.loan_type : '';
            let insuranceDiv = `
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_${loanId}">Insurance</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-insurance-input" name="insurance[${loanId}]" id="insurance_${loanId}" placeholder="0.00"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_month_${loanId}">Insurance Month</label>
                            <select name="insurance_month[${loanId}]" id="insurance_month_${loanId}" class="form-control form-control-sm loan-input my-insurance">
                                <option value="" disabled>Select</option>
                                @foreach (month_names() as $number => $name)
                                    <option value="{{ $number }}"  {{ $number == date('m') ? 'selected' : ''}}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_year_${loanId}">Insurance Year</label>
                            <input type="text" class="form-control form-control-sm loan-input my-insurance" id="insurance_year_${loanId}" name="insurance_year[${loanId}]" value="{{date('Y')}}" min="0">
                        </div>`;

            let membershipDiv = `
                            <div class="col-md-6 form-group mb-2">
                                <label class="form-label" for="membership_fee_${loanId}">Membership Fee</label>
                                <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-membership-fee-input" name="membership_fee[${loanId}]" id="membership_fee_${loanId}" placeholder="0.00"/>
                            </div>`;

            if(isSpecial || loanType == 'mortgage-loan') {
                membershipDiv = ``;
            }

            if(isSpecial) {
                insuranceDiv = ``;
            }

            let cardHtml = `
            <div class="card mb-3 border border-light-subtle shadow-sm loan-card" id="loan-card-${loanId}" style="animation: fadeIn 0.3s ease-in-out;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0 text-primary loan-card-title"><i class="fa fa-file-text-o me-2"></i> ${loanText}</h6>
                    <span class="badge bg-info text-light loan-card-title">${loanType}</span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-3 form-group d-none">
                            <label class="form-label" for="loan_amount_${loanId}">Allocated Amount</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-amount-input" name="loan_amount[${loanId}]" id="loan_amount_${loanId}" placeholder="0.00" required autocomplete="off"/>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="form-label" for="interest_${loanId}">Interest</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-interest-input" name="interest[${loanId}]" id="interest_${loanId}" placeholder="0.00"/>
                            <span id="interest_and_interest_deficit_${loanId}" class="text-xs text-muted d-block mt-1"></span>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="interest_period_${loanId}">Interest Period</label>
                            <input type="text" name="interest_period[${loanId}]" id="interest_period_${loanId}" class="form-control form-control-sm interest-period-input"/>
                            <div class="form-check mt-1">
                                <input type="checkbox" name="exclude_interest[${loanId}]" id="exclude_interest_${loanId}" class="form-check-input loan-input exclude-interest-checkbox" value="1"/>
                                <label class="form-check-label" for="exclude_interest_${loanId}"><small>Exclude Interest</small></label>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label interest-label" for="interest_deficit_${loanId}">Interest Deficit</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end" name="interest_deficit[${loanId}]" id="interest_deficit_${loanId}" readonly placeholder="0.00"/>
                        </div>

                        `+insuranceDiv+`

                        <div class="col-md-8">
                            <div class="row g-2 mb-2">
                                <div class="col-6 form-group">
                                    <label class="form-label" for="principal_${loanId}">Principal</label>
                                    <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-principal-input" name="principal[${loanId}]" id="principal_${loanId}" placeholder="0.00"/>
                                </div>
                                <div class="col-6 form-group">
                                    <label class="form-label" for="principal_advance_${loanId}">Principal Advance</label>
                                    <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-principal-advance-input" name="principal_advance[${loanId}]" id="principal_advance_${loanId}" placeholder="0.00"/>
                                </div>
                                `+membershipDiv+`
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_early_settlement[${loanId}]" id="is_early_settlement_${loanId}" class="form-check-input loan-input early-settlement-checkbox" value="1"/>
                                <label class="form-check-label" for="is_early_settlement_${loanId}"><small>Early Settlement</small></label>
                            </div>
                            <div class="special-loan-group-${loanId} form-check" style="${loanType === 'special-loan' ? 'display: none;' : ''}">
                                <input type="checkbox" name="include_special_loan_balance[${loanId}]" id="include_special_loan_balance_${loanId}" class="form-check-input loan-input include-special-loan-checkbox" value="1"/>
                                <label class="form-check-label" for="include_special_loan_balance_${loanId}"><small>Include Special Loan Balance</small></label>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-end justify-content-end">
                            <div class="text-end mb-2">
                                <small class="text-muted d-block">Loan Total Due:</small>
                                <h5 class="mb-0 text-success" id="loan_total_due_display_${loanId}">0.00</h5>
                                <div id="early_settlement_warning_${loanId}" class="text-danger d-none" style="font-size: 0.8rem; font-weight: bold; margin-top: 5px;">Insufficient amount for early settlement</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            $('#loans_container').append(cardHtml);

            // Initialize Date Range Picker
            $(`#interest_period_${loanId}`).daterangepicker({
                opens: 'left'
            }, function (start, end, label) {
                $("[name='transaction_date']").val(end.format('YYYY-MM-DD'));
                $(`#insurance_month_${loanId}`).val(end.format('M'));
                $(`#insurance_year_${loanId}`).val(end.format('Y'));
                $("[name='transaction_date']").trigger('change');
            });

            // Initialize Date Picker for Insurance Year
            $(`#insurance_year_${loanId}`).datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose: true
            });

            // if (loanType === 'open-term' || loanType === 'loan-with-term' || loanType === 'special-loan') {
            //     $(`#principal_${loanId}`).prop('readonly', false);
            // } else {
            //     $(`#principal_${loanId}`).prop('readonly', true);
            // }

            getSingleLoanData(loanId);
        }

        async function getSingleLoanData(loanId) {
            if (!loanId) return;

            const picker = $(`#interest_period_${loanId}`).data('daterangepicker');
            const endDate = picker ? picker.endDate.format('YYYY-MM-DD') : '';
            const transaction_date = $('[name="transaction_date"]').val();

            const feesResponse = await $.ajax({
                type: 'GET',
                url: '{{ route('get_fees') }}',
                data: { id: loanId, interest_period: transaction_date, transaction_date: transaction_date }
            });

            let $card = $(`#loan-card-${loanId}`);
            $card.data('repayment_order', feesResponse.repayment_order || []);

            var monthly_amortization = parsePHPNumber(feesResponse.monthly_amortization);
            var interest = parsePHPNumber(feesResponse.interest);
            var insurance = parsePHPNumber(feesResponse.insurance_monthly_fee);
            var membership_fee = parsePHPNumber(feesResponse.membership_fee_balance);
            let principal_advance = parsePHPNumber(feesResponse.principal_advance_balance);
            let principal_balance = parsePHPNumber(feesResponse.principal_balance);
            let special_loan_balance = parsePHPNumber(feesResponse.special_loan_balance) || 0;
            let early_settlement_amount = parsePHPNumber(feesResponse.early_settlement_amount);

            // Cache dues
            $card.data('interest-due', interest);
            $card.data('insurance-due', insurance);
            $card.data('membership-fee-due', membership_fee);
            $card.data('principal-balance', principal_balance);
            $card.data('special-loan-balance', special_loan_balance);
            $card.data('early-settlement-amount', early_settlement_amount);
            $card.data('principal-advance-due', principal_advance);
            $card.data('monthly-amortization', monthly_amortization);
            $card.data('previous-interest-deficit', parsePHPNumber(feesResponse.previous_interest_deficit));
            $card.data('current-interest', parsePHPNumber(feesResponse.current_interest));
            $card.find('input').data('edited', false);

            $(`#insurance_month_${loanId}`).val(moment(feesResponse.interest_end_date).format('M'));
            $(`#insurance_year_${loanId}`).val(moment(feesResponse.interest_end_date).format('Y'));

            const datenow = moment().format('YYYY-MM-DD');
            if (picker) {
                picker.minDate = moment(feesResponse.interest_start_date);
                picker.maxDate = moment(datenow);
                picker.setStartDate(feesResponse.interest_start_date);
                picker.setEndDate(feesResponse.interest_end_date);
                picker.updateView();
                picker.updateCalendars();
            }

            // Populate fields with default dues
            let exclude_interest = $(`#exclude_interest_${loanId}`).prop('checked');
            $(`#interest_${loanId}`).val(exclude_interest ? '0.00' : interest.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $(`#insurance_${loanId}`).val(insurance > 0 ? insurance.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) : '');

            $(`#membership_fee_${loanId}`).val(membership_fee.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Determine principal
            let is_early_settlement = $(`#is_early_settlement_${loanId}`).prop('checked');
            let defaultPrincipal = 0;
            if (is_early_settlement) {
                let earlySettlementAmount = early_settlement_amount;
                let includeSpecial = $(`#include_special_loan_balance_${loanId}`).prop('checked');
                if (!includeSpecial) {
                    earlySettlementAmount -= special_loan_balance;
                }
                defaultPrincipal = earlySettlementAmount;
            } else {
                let loanInfo = clientLoansMap[loanId];
                let isSpecial = loanInfo ? loanInfo.is_special : false;
                if (isSpecial) {
                    defaultPrincipal = principal_balance;
                } else {
                    defaultPrincipal = monthly_amortization;
                }
            }
            $(`#principal_${loanId}`).val(defaultPrincipal.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $(`#principal_advance_${loanId}`).val(principal_advance.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Calculate interest deficit
            let interestPaid = exclude_interest ? 0 : interest;
            let deficit = 0;
            if (!is_early_settlement) {
                deficit = interestPaid - interest;
                if (deficit > 0) {
                    deficit = 0;
                }
            }
            $(`#interest_deficit_${loanId}`).val(deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            let overallVal = parsePHPNumber($("[name='amount']").val()) || 0;
            if (overallVal > 0) {
                calculateChange('amount');
            } else {
                calculateChange('card');
            }
        }

        $(".btnSubmit").click(function (e) {
            e.preventDefault();
            transaction_type = $("[name='transaction_type']").find('option:selected').val();
            if(transaction_type == 'withdraw') {
                calculateChange('card');
                submitTransaction();
            } else {
                submitTransaction();
            }
        });

        function triggerSoaPrompt(loanId) {
            const baseUrl = `{{ env('APP_URL') }}`;
            swal({
                title: 'Select Action',
                text: 'Choose whether to download or print SOA.',
                icon: 'info',
                buttons: {
                    download: {
                        text: 'Download',
                        value: 'download',
                    },
                    print: {
                        text: 'Print',
                        value: 'print',
                    }
                }
            }).then((value) => {
                let loanInfo = clientLoansMap[loanId];
                let isNoTerm = loanInfo && (loanInfo.loan_type === 'open-term');
                let reportType = isNoTerm ? 'soa-no-term' : 'soa-term';

                if (value === 'download') {
                    let iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = baseUrl + '/loan/report/' + loanId + '/download-pdf/' + reportType;
                    document.body.appendChild(iframe);
                    iframe.onload = function () {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                }

                if (value === 'print') {
                    let iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = baseUrl + '/loan/report/' + loanId + '/preview/' + reportType;
                    document.body.appendChild(iframe);
                    iframe.onload = function () {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                }
            });

            setTimeout(() => {
                $('.swal-footer').css({
                    display: 'flex',
                    justifyContent: 'center',
                    gap: '10px'
                });
            }, 10);
        }

        $(document).on('input', "[name='amount']", function () {
            calculateChange('amount');
        });

        $(document).on('change', ".loan-input", function () {
            if ($(this).is(':checkbox') || $(this).is('select')) {
                if ($(this).hasClass('exclude-interest-checkbox') || $(this).hasClass('include-special-loan-checkbox')) {
                    let loanId = $(this).attr('id').split('_').pop();
                    getSingleLoanData(loanId);
                } else {
                    calculateChange('reset');
                }
            }
        });

        $(document).on('input', ".loan-interest-input, .loan-principal-input, .loan-principal-advance-input, .loan-insurance-input, .loan-membership-fee-input", function () {
            $(this).data('edited', true);
            calculateChange('amount');
        });

        $(document).on('blur', ".loan-interest-input, .loan-principal-input, .loan-principal-advance-input, .loan-insurance-input, .loan-membership-fee-input", function () {
            calculateChange('amount');
        });

        $(document).on('change', '.early-settlement-checkbox', function () {
            let loanId = $(this).attr('id').split('_').pop();
            var is_early_settlement = $(this).prop('checked');
            if (is_early_settlement) {
                $(`#include_special_loan_balance_${loanId}`).prop('checked', true);
                $(`#interest_${loanId}`).prop('readonly', true);
            } else {
                $(`#include_special_loan_balance_${loanId}`).prop('checked', false);
                $(`#interest_${loanId}`).prop('readonly', false);
            }
            getSingleLoanData(loanId);

            if (is_early_settlement) {
                triggerSoaPrompt(loanId);
            }
        });

        $(document).on('change', '.my-insurance', async function () {
            let loanId = $(this).attr('id').split('_')[2];
            if (!loanId) return;

            console.log('get insurance fee for loan ' + loanId);
            let feesResponse = await getFeesByMonthAndYear(loanId);
            let insurance = parsePHPNumber(feesResponse.insurance_monthly_fee);
            $(`#loan-card-${loanId}`).data('insurance-due', insurance);
            getSingleLoanData(loanId);
        });

        $("[name='transaction_date']").on('change', function () {
            if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            const endDate = $(this).val();

            $('.interest-period-input').each(function() {
                const picker = $(this).data('daterangepicker');
                if (picker) {
                    picker.setEndDate(moment(endDate));
                    picker.updateView();
                    picker.updateCalendars();
                }
            });

            let selectedLoans = $("#loan_id").val() || [];
            selectedLoans.forEach(loanId => {
                getSingleLoanData(loanId);
            });
        });

        $('#transaction_type').on('change', function () {
            var _val = $(this).find('option:selected').val()
            if (_val == 'deposit') {
                $('#mop_transfer_container').show()
                $('#mop_transfer_select_container').hide()
            } else {
                $('#mop_transfer_container').hide()
                $('#mop_transfer_select_container').show()
                $('#mode_of_payment').trigger('change')
            }
        });

        $('#mode_of_payment').on('change', function () {
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            const elementChoices = document.querySelector('[name="transaction_code_select[]"]');

            if (choicesTransactionCode) {
                choicesTransactionCode.destroy();
            }

            choicesTransactionCode = new Choices(elementChoices, {
                removeItemButton: true,
                duplicateItemsAllowed: false,
            });

            choicesTransactionCode.clearStore();

            var mode_of_payment = $("#mode_of_payment").val();

            $.ajax({
                type: 'GET',
                data: { client_id: client_id, mode_of_payment: mode_of_payment },
                url: `{{ route('ajax.get-merchant-slip') }}`,
                success: function (response) {
                    $("[name='amount']").val(0);

                    let options = [];
                    $.each(response, function (index, value) {
                        options.push({
                            value: value.transaction_code,
                            label: value.transaction_code,
                            customProperties: {
                                amount: value.amount
                            }
                        });
                    });

                    choicesTransactionCode.setChoices(options, 'value', 'label', true);
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if (mode_of_payment === 'cash') {
                        calculateChange('reset');
                    } else {
                        calculateChange('amount');
                    }
                }
            });

            elementChoices.addEventListener('addItem', () => calculateChange('amount'));
            elementChoices.addEventListener('removeItem', () => calculateChange('amount'));
        });

        $("#change_mop").on("change", function() {
            $('.attachment').toggleClass('d-none', $(this).val() !== 'authorized_person');
        }).trigger("change");

        function submitTransaction() {
            let form = $("#frmTransactionModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let form = $('#frmTransactionModal')[0];
                let formData = new FormData(form);

                formData.append('client_id', client_id);

                let transaction_type = $("[name='transaction_type']").val();
                var txn_code = $("#transaction_code").val();

                if(transaction_type == 'deposit' && txn_code == '') {
                    $("[name='transaction_code']").addClass('is-invalid');
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });
                    return;
                } else {
                    $("[name='transaction_code']").removeClass('is-invalid');
                }

                if (transaction_type === 'deposit') {
                    let mop = $("[name='transaction_type'] option:selected").data('mop');
                    formData.set('mode_of_payment', mop);
                }

                // Append interest period ranges for all selected loans
                let selectedLoans = $("#loan_id").val() || [];
                selectedLoans.forEach(loanId => {
                    const picker = $(`#interest_period_${loanId}`).data('daterangepicker');
                    if (picker) {
                        formData.append(`interest_start_date[${loanId}]`, picker.startDate.format('YYYY-MM-DD'));
                        formData.append(`interest_end_date[${loanId}]`, picker.endDate.format('YYYY-MM-DD'));
                    }
                });

                var id = $("[name='id']").val();
                if (id) {
                    _method = 'PUT';
                    _url = `{{route('savings.update', ':id')}}`.replace(':id', id);
                } else {
                    _method = 'POST';
                    _url = `{{route('savings.store')}}`;
                }

                $.ajax({
                    method: _method,
                    url: _url,
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if (response.status == 'success') {
                            setTimeout(function () {
                                showToast.success(response.message);
                                setTimeout(function () {
                                    window.location.href = '/savings/' + client_id + '/transactions';
                                }, 1000);
                            }, 300);
                        } else {
                            showToast.error(response.message);
                        }
                    },
                    error: function (response) {
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        showToast.error(response.responseJSON.message);
                    }
                });
            }
        }

        function getFeesByMonthAndYear(loan_id) {
            const insurance_month = $(`#insurance_month_${loan_id}`).val();
            const insurance_year = $(`#insurance_year_${loan_id}`).val();
            const data = { id: loan_id, insurance_month: insurance_month, insurance_year: insurance_year };

            return $.ajax({
                type: 'GET',
                url: '{{ route('get_fees_by_month_and_year') }}',
                data: data,
            });
        }

        function calculateTotalSlipsAmount() {
            if (!choicesTransactionCode) return 0;
            const selectedItems = choicesTransactionCode.getValue();
            let total = 0;

            if (Array.isArray(selectedItems)) {
                selectedItems.forEach(item => {
                    if (item.customProperties && item.customProperties.amount) {
                        total += parseFloat(item.customProperties.amount);
                    }
                });
            } else if (selectedItems && selectedItems.customProperties) {
                total = parseFloat(selectedItems.customProperties.amount);
            }
            return total;
        }

        function getLoanDues(loanId, useInputs = false) {
            let $card = $(`#loan-card-${loanId}`);
            if ($card.length === 0) return { total: 0, interest: 0, insurance: 0, membership_fee: 0, principal: 0, principal_advance: 0 };

            let interestDue = 0;
            let insuranceDue = 0;
            let membershipFeeDue = 0;
            let principalDue = 0;
            let principalAdvanceDue = 0;

            let is_early_settlement = $(`#is_early_settlement_${loanId}`).prop('checked');
            let loanInfo = clientLoansMap[loanId];
            let loan_type = loanInfo ? loanInfo.loan_type : '';
            let isSpecial = loanInfo ? loanInfo.is_special : false;

            let defaultInterest = $(`#exclude_interest_${loanId}`).prop('checked') ? 0 : (parsePHPNumber($card.data('interest-due')) || 0);
            let defaultInsurance = isSpecial ? 0 : (parsePHPNumber($card.data('insurance-due')) || 0);
            let defaultMembershipFee = isSpecial ? 0 : (parsePHPNumber($card.data('membership-fee-due')) || 0);
            let defaultPrincipalAdvance = parsePHPNumber($card.data('principal-advance-due')) || 0;
            let defaultPrincipal = 0;
            if (is_early_settlement) {
                let earlySettlementAmount = parsePHPNumber($card.data('early-settlement-amount')) || 0;
                let includeSpecial = $(`#include_special_loan_balance_${loanId}`).prop('checked');
                if (!includeSpecial) {
                    let specialBalance = parsePHPNumber($card.data('special-loan-balance')) || 0;
                    earlySettlementAmount -= specialBalance;
                }
                defaultPrincipal = earlySettlementAmount;
            } else {
                let isSpecial = loanInfo ? loanInfo.is_special : false;
                if (isSpecial) {
                    defaultPrincipal = parsePHPNumber($card.data('principal-balance')) || 0;
                } else {
                    defaultPrincipal = parsePHPNumber($card.data('monthly-amortization')) || 0;
                }
            }

            let isPrincipalEdited = $(`#principal_${loanId}`).data('edited') === true;
            let isPrincipalExplicitlyZero = false;

            if (useInputs) {
                let $interestInput = $(`#interest_${loanId}`);
                let $insuranceInput = $(`#insurance_${loanId}`);
                let $membershipFeeInput = $(`#membership_fee_${loanId}`);
                let $principalInput = $(`#principal_${loanId}`);
                let $principalAdvanceInput = $(`#principal_advance_${loanId}`);

                let rawInt = $interestInput.val();
                let rawIns = $insuranceInput.val();
                let rawMem = $membershipFeeInput.val();
                let rawPrin = $principalInput.val();
                let rawAdv = $principalAdvanceInput.val();

                interestDue = ($interestInput.data('edited') === true)
                    ? ((rawInt === '' || rawInt === undefined) ? 0 : (parsePHPNumber(rawInt) || 0))
                    : defaultInterest;

                insuranceDue = ($insuranceInput.data('edited') === true)
                    ? ((rawIns === '' || rawIns === undefined) ? 0 : (parsePHPNumber(rawIns) || 0))
                    : defaultInsurance;

                membershipFeeDue = ($membershipFeeInput.data('edited') === true)
                    ? ((rawMem === '' || rawMem === undefined) ? 0 : (parsePHPNumber(rawMem) || 0))
                    : defaultMembershipFee;

                principalDue = ($principalInput.data('edited') === true)
                    ? ((rawPrin === '' || rawPrin === undefined) ? 0 : (parsePHPNumber(rawPrin) || 0))
                    : defaultPrincipal;

                principalAdvanceDue = ($principalAdvanceInput.data('edited') === true)
                    ? ((rawAdv === '' || rawAdv === undefined) ? 0 : (parsePHPNumber(rawAdv) || 0))
                    : defaultPrincipalAdvance;

                if ($(`#exclude_interest_${loanId}`).prop('checked')) {
                    interestDue = 0;
                }

                if (isPrincipalEdited && (rawPrin === '' || rawPrin === undefined || parsePHPNumber(rawPrin) === 0)) {
                    isPrincipalExplicitlyZero = true;
                }
            } else {
                interestDue = defaultInterest;
                insuranceDue = defaultInsurance;
                membershipFeeDue = defaultMembershipFee;
                principalDue = defaultPrincipal;
                principalAdvanceDue = defaultPrincipalAdvance;
            }

            let total = interestDue + insuranceDue + membershipFeeDue + principalAdvanceDue + principalDue;

            return {
                total: total,
                interest: interestDue,
                interest_base: parsePHPNumber($card.data('interest-due')) || 0,
                insurance: insuranceDue,
                membership_fee: membershipFeeDue,
                principal: principalDue,
                principal_advance: principalAdvanceDue,
                isPrincipalExplicitlyZero: isPrincipalExplicitlyZero
            };
        }

        function calculateTotalDuesAllSelected() {
            let selectedLoans = $("#loan_id").val() || [];
            let totalDues = 0;
            selectedLoans.forEach(loanId => {
                let dues = getLoanDues(loanId, false);
                totalDues += dues.total;
            });
            return totalDues;
        }

        function distributeLoanAmount(loanId, allocatedAmount, dues) {
            let $card = $(`#loan-card-${loanId}`);
            if ($card.length === 0) return;

            let repaymentOrder = $card.data('repayment_order') || ["Special Loan","Fees", "Interest", "Principal"];
            if (typeof repaymentOrder === 'string') {
                try {
                    repaymentOrder = JSON.parse(repaymentOrder);
                } catch (e) {
                    repaymentOrder = repaymentOrder.split(',').map(s => s.trim());
                }
            }
            if (!Array.isArray(repaymentOrder)) {
                repaymentOrder = ["Special Loan","Fees", "Interest", "Principal"];
            }

            console.log(repaymentOrder)
            let allocatedInterest = 0;
            let allocatedInsurance = 0;
            let allocatedMembershipFee = 0;
            let allocatedPrincipal = 0;
            let allocatedPrincipalAdvance = 0;

            let tempAllocated = allocatedAmount;

            repaymentOrder.forEach(category => {
                let catLower = (category || "").toString().toLowerCase();
                console.log(catLower)
                if (catLower === "interest") {
                    let val = Math.min(tempAllocated, dues.interest);
                    allocatedInterest += val;
                    tempAllocated -= val;
                } else if (catLower === "fees") {
                    let valMem = Math.min(tempAllocated, dues.membership_fee);
                    allocatedMembershipFee += valMem;
                    tempAllocated -= valMem;

                    let valIns = Math.min(tempAllocated, dues.insurance);
                    allocatedInsurance += valIns;
                    tempAllocated -= valIns;
                } else if (catLower === "principal" || catLower === "special loan" || catLower === "special-loan") {
                    let valPrin = Math.min(tempAllocated, dues.principal);
                    allocatedPrincipal += valPrin;
                    tempAllocated -= valPrin;

                    let valAdv = Math.min(tempAllocated, dues.principal_advance);
                    allocatedPrincipalAdvance += valAdv;
                    tempAllocated -= valAdv;
                }
            });
            console.log(allocatedInterest)
            console.log(allocatedMembershipFee)
            console.log(allocatedInsurance)
            console.log(allocatedPrincipal)

            // Excess goes to principal only if not explicitly set to 0 by the user
            if (tempAllocated > 0 && !dues.isPrincipalExplicitlyZero) {
                allocatedPrincipal += tempAllocated;
                tempAllocated = 0;
            }

            // Write values back to inputs
            if (document.activeElement !== $(`#interest_${loanId}`)[0]) {
                $(`#interest_${loanId}`).val(allocatedInterest.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            if (document.activeElement !== $(`#insurance_${loanId}`)[0]) {
                if (dues.insurance > 0 || allocatedInsurance > 0) {
                    $(`#insurance_${loanId}`).val(allocatedInsurance.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    $(`#insurance_${loanId}`).val('');
                }
            }

            if (document.activeElement !== $(`#membership_fee_${loanId}`)[0]) {
                $(`#membership_fee_${loanId}`).val(allocatedMembershipFee.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            let is_early_settlement = $(`#is_early_settlement_${loanId}`).prop('checked');
            if (document.activeElement !== $(`#principal_${loanId}`)[0]) {
                $(`#principal_${loanId}`).val((is_early_settlement ? dues.principal : allocatedPrincipal).toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            if (document.activeElement !== $(`#principal_advance_${loanId}`)[0]) {
                $(`#principal_advance_${loanId}`).val(allocatedPrincipalAdvance.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            // Interest Deficit
            let deficit = 0;
            if (!is_early_settlement) {
                deficit = allocatedInterest - dues.interest_base;
                if (deficit > 0) {
                    deficit = 0;
                }
            }
            let strDeficit = deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            $(`#interest_deficit_${loanId}`).val(strDeficit);

            // Update card total due display
            $(`#loan_total_due_display_${loanId}`).text((allocatedAmount - tempAllocated).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Set the hidden input field loan_amount
            $(`#loan_amount_${loanId}`).val((allocatedAmount - tempAllocated).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            return allocatedAmount - tempAllocated;
        }

        async function calculateChange(mode = 'amount') {
            if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            let selectedLoans = $("#loan_id").val() || [];
            if (selectedLoans.length === 0) {
                let isSlipBased = ($("#mode_of_payment").val() !== 'cash');
                let overall_amount = 0;
                if (isSlipBased) {
                    overall_amount = calculateTotalSlipsAmount();
                }
                $("[name='amount']").val(overall_amount.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $("[name='due_change']").val(overall_amount.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })).trigger('change');
                $('#change_container').toggleClass('d-none', overall_amount <= 0);
                return;
            }

            // Check if payment mode is slip-based (non-cash)
            let isSlipBased = ($("#mode_of_payment").val() !== 'cash');

            let overall_amount = 0;
            let total_allocated_all = 0;

            if (mode === 'card') {
                // Sum up card component values manually edited by the user
                selectedLoans.forEach(loanId => {
                    let interest = parsePHPNumber($(`#interest_${loanId}`).val());
                    let insurance = parsePHPNumber($(`#insurance_${loanId}`).val());
                    let membership_fee = parsePHPNumber($(`#membership_fee_${loanId}`).val());
                    let principal = parsePHPNumber($(`#principal_${loanId}`).val());
                    let principal_advance = parsePHPNumber($(`#principal_advance_${loanId}`).val());

                    if (principal < 0) principal = 0;
                    if (principal_advance < 0) principal_advance = 0;

                    let strPrincipal = principal.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (document.activeElement !== $(`#principal_${loanId}`)[0]) {
                        $(`#principal_${loanId}`).val(strPrincipal);
                    }

                    let strPrincipalAdvance = principal_advance.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (document.activeElement !== $(`#principal_advance_${loanId}`)[0]) {
                        $(`#principal_advance_${loanId}`).val(strPrincipalAdvance);
                    }

                    let exclude_interest = $(`#exclude_interest_${loanId}`).prop('checked');
                    let interestPaid = exclude_interest ? 0 : interest;
                    let loanAmount = interestPaid + insurance + principal + principal_advance + membership_fee;

                    // Write allocated amount of this loan to hidden field
                    $(`#loan_amount_${loanId}`).val(loanAmount.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    // Update total due display for this loan
                    $(`#loan_total_due_display_${loanId}`).text(loanAmount.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));

                    // Update interest deficit
                    let $card = $(`#loan-card-${loanId}`);
                    let is_early_settlement = $(`#is_early_settlement_${loanId}`).prop('checked');
                    let deficit = 0;
                    if (!is_early_settlement) {
                        let interestBase = parsePHPNumber($card.data('interest-due')) || 0;
                        deficit = interestPaid - interestBase;
                        if (deficit > 0) {
                            deficit = 0;
                        }
                    }
                    let strDeficit = deficit.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $(`#interest_deficit_${loanId}`).val(strDeficit);

                    total_allocated_all += loanAmount;
                });

                if (isSlipBased) {
                    overall_amount = calculateTotalSlipsAmount();
                    $("[name='amount']").val(overall_amount.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    overall_amount = total_allocated_all;
                    if (document.activeElement !== $("[name='amount']")[0]) {
                        $("[name='amount']").val(overall_amount.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                    }
                }
            } else {
                // Sequential allocation modes ('amount' or 'reset')
                if (isSlipBased) {
                    overall_amount = calculateTotalSlipsAmount();
                    $("[name='amount']").val(overall_amount.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    if (mode === 'reset') {
                        overall_amount = calculateTotalDuesAllSelected();
                        $("[name='amount']").val(overall_amount.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                    } else {
                        overall_amount = parsePHPNumber($("[name='amount']").val()) || 0;
                    }
                }

                // Sort loans: special first, regular last
                let sortedLoans = [...selectedLoans].sort((a, b) => {
                    let isSpecialA = (clientLoansMap[a]?.is_special === true);
                    let isSpecialB = (clientLoansMap[b]?.is_special === true);
                    if (isSpecialA === isSpecialB) return 0;
                    return isSpecialA ? -1 : 1;
                });

                let remainingAmount = overall_amount;
                console.log(1189)

                if (isSlipBased && overall_amount === 0) {
                    console.log(1193)
                    // Do not distribute 0, keep loaded card dues
                    selectedLoans.forEach(loanId => {
                        let interest = parsePHPNumber($(`#interest_${loanId}`).val());
                        let insurance = parsePHPNumber($(`#insurance_${loanId}`).val());
                        let membership_fee = parsePHPNumber($(`#membership_fee_${loanId}`).val());
                        let principal = parsePHPNumber($(`#principal_${loanId}`).val());
                        let principal_advance = parsePHPNumber($(`#principal_advance_${loanId}`).val());

                        let exclude_interest = $(`#exclude_interest_${loanId}`).prop('checked');
                        let interestPaid = exclude_interest ? 0 : interest;
                        let loanAmount = interestPaid + insurance + principal + principal_advance + membership_fee;

                        total_allocated_all += loanAmount;
                    });
                } else {
                    console.log(1209)
                    let useInputsForTarget = (mode === 'amount');
                    let loanTargetDues = {};
                    let loanDefaultDues = {};
                    let loanTotalDues = {};

                    console.log(useInputsForTarget)
                    console.log(loanTargetDues)
                    console.log(loanDefaultDues)
                    console.log(loanTotalDues)

                    selectedLoans.forEach(loanId => {
                        loanDefaultDues[loanId] = getLoanDues(loanId, false);
                        loanTargetDues[loanId] = getLoanDues(loanId, useInputsForTarget);
                        loanTotalDues[loanId] = Math.max(loanDefaultDues[loanId].total, loanTargetDues[loanId].total);
                    });

                    // First pass: distribute sequentially up to totalDue
                    for (let i = 0; i < sortedLoans.length; i++) {
                        let loanId = sortedLoans[i];
                        let isLastLoan = (i === sortedLoans.length - 1);

                        let totalDue = loanTotalDues[loanId];
                        let targetDues = loanTargetDues[loanId];

                        let allocatedToLoan = 0;
                        if (isLastLoan && !isSlipBased) {
                            allocatedToLoan = remainingAmount;
                        } else {
                            allocatedToLoan = Math.min(remainingAmount, totalDue);
                        }

                        remainingAmount -= allocatedToLoan;
                        console.log('loanId: ' +loanId)
                        console.log('allocatedToLoan: ' +allocatedToLoan)
                        console.log('targetDues: ' +targetDues)

                        console.log('1st iteration start')
                        let actualAllocated = distributeLoanAmount(loanId, allocatedToLoan, targetDues);
                        remainingAmount += (allocatedToLoan - actualAllocated);
                        console.log('remainingAmount: ' +remainingAmount)
                        total_allocated_all += actualAllocated;
                    }

                    // Second pass (Iterative): if there is still remainingAmount, distribute it to loans that can absorb more
                    let iterations = 0;
                    let maxIterations = sortedLoans.length * 2;
                    console.log('2nd iteration start')
                    while (remainingAmount > 0.01 && iterations < maxIterations) {
                        let allocatedThisIteration = 0;
                        for (let i = 0; i < sortedLoans.length; i++) {
                            let loanId = sortedLoans[i];
                            let isLastLoan = (i === sortedLoans.length - 1);

                            let totalDue = loanTotalDues[loanId];
                            let targetDues = loanTargetDues[loanId];
                            let currentAllocated = parsePHPNumber($(`#loan_amount_${loanId}`).val()) || 0;

                            let canAbsorb = 0;
                            if (isLastLoan && !isSlipBased) {
                                canAbsorb = remainingAmount;
                            } else {
                                canAbsorb = totalDue - currentAllocated;
                            }

                            if (canAbsorb <= 0.01) continue;

                            let allocatedToLoan = Math.min(remainingAmount, canAbsorb);
                            remainingAmount -= allocatedToLoan;

                            console.log('loanId: ' +loanId)
                            console.log('allocatedToLoan: ' +allocatedToLoan)
                            console.log('currentAllocated: ' +currentAllocated)
                            console.log('targetDues: ' +targetDues)
                            let actualAllocated = distributeLoanAmount(loanId, currentAllocated + allocatedToLoan, targetDues);
                            let unused = (currentAllocated + allocatedToLoan) - actualAllocated;

                            remainingAmount += unused;
                            allocatedThisIteration += (actualAllocated - currentAllocated);
                            total_allocated_all += (actualAllocated - currentAllocated);
                            console.log('i: ' + i)
                            console.log('total_allocated_all: ' + total_allocated_all)
                        }
                        if (allocatedThisIteration <= 0.01) {
                            break;
                        }
                        iterations++;
                    }
                }
            }

            // Validate early settlement amount sufficiency
            let hasInsufficientEarlySettlement = false;
            selectedLoans.forEach(loanId => {
                let is_early = $(`#is_early_settlement_${loanId}`).prop('checked');
                if (is_early) {
                    let dues = getLoanDues(loanId);
                    let allocated = parsePHPNumber($(`#loan_amount_${loanId}`).val()) || 0;
                    if (Math.round(allocated * 100) < Math.round(dues.total * 100)) {
                        hasInsufficientEarlySettlement = true;
                        $(`#loan-card-${loanId}`).addClass('border-danger');
                        $(`#early_settlement_warning_${loanId}`).removeClass('d-none');
                    } else {
                        $(`#loan-card-${loanId}`).removeClass('border-danger');
                        $(`#early_settlement_warning_${loanId}`).addClass('d-none');
                    }
                } else {
                    $(`#loan-card-${loanId}`).removeClass('border-danger');
                    $(`#early_settlement_warning_${loanId}`).addClass('d-none');
                }
            });

            // Update change
            let change = overall_amount - total_allocated_all;
            console.log(overall_amount + '-' + total_allocated_all)
            if (change < 0) {
                change = 0;
            }

            if (overall_amount <= 0 || (isSlipBased && change < -0.01) || hasInsufficientEarlySettlement) {
                $('.btnSubmit').attr('disabled', true);
                if (overall_amount <= 0 || (isSlipBased && change < -0.01)) {
                    $("[name='amount']").addClass('is-invalid');
                } else {
                    $("[name='amount']").removeClass('is-invalid');
                }
                $("[name='due_change']").val(0).trigger('change');
            } else {
                $('.btnSubmit').attr('disabled', false);
                $("[name='amount']").removeClass('is-invalid');

                $("[name='due_change']").val(change.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })).trigger('change');
            }

            $('#change_container').toggleClass('d-none', change <= 0);
        }

        function getBalance() {
            var client_id = $("#client_id").val();
            return $.ajax({
                type: 'GET',
                url: `{{ route('client.get-balance',':client_id') }}`.replace(':client_id', client_id)
            });
        }
    })
</script>
@endpush
