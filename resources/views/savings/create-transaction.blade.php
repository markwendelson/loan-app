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
            $("[name='amount']").prop('readonly', true);
            calculateChange();
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
                $("[name='amount']").prop('readonly', true);
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
            let loanType = loanInfo ? loanInfo.loan_type : '';
            let loanText = event.detail.label;

            activeLoans.add(loanId);
            renderLoanCard(loanId, loanText, loanType);
        });

        document.querySelector('#loan_id').addEventListener('removeItem', function(event) {
            let loanId = event.detail.value;
            activeLoans.delete(loanId);
            $(`#loan-card-${loanId}`).remove();
            calculateChange();
        });

        function renderLoanCard(loanId, loanText, loanType) {
            let insuranceDiv = `
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_${loanId}">Insurance</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end loan-input" name="insurance[${loanId}]" id="insurance_${loanId}" placeholder="0.00"/>
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
                                <input type="text" class="form-control form-control-sm decimal-only text-end loan-input" name="membership_fee[${loanId}]" id="membership_fee_${loanId}" placeholder="0.00"/>
                            </div>`;

            if(loanType == 'special-loan' || loanType == 'mortgage-loan') {
                membershipDiv = ``;
            }

            if(loanType == 'special-loan') {
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
                                    <input type="text" class="form-control form-control-sm decimal-only text-end loan-input loan-principal-input" name="principal[${loanId}]" id="principal_${loanId}" placeholder="0.00" readonly/>
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
                $(`#insurance_month_${loanId}`).val(end.format('M'));
                $(`#insurance_year_${loanId}`).val(end.format('Y'));
                getSingleLoanData(loanId);
            });

            // Initialize Date Picker for Insurance Year
            $(`#insurance_year_${loanId}`).datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years",
                autoclose: true
            });

            if (loanType === 'open-term' || loanType === 'loan-with-term' || loanType === 'special-loan') {
                $(`#principal_${loanId}`).prop('readonly', false);
            } else {
                $(`#principal_${loanId}`).prop('readonly', true);
            }

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
                data: { id: loanId, interest_period: endDate, transaction_date: transaction_date }
            });

            $(`#loan-card-${loanId}`).data('repayment_order', feesResponse.repayment_order || []);

            var monthly_amortization = parsePHPNumber(feesResponse.monthly_amortization);
            var interest = parsePHPNumber(feesResponse.interest);
            var insurance = parsePHPNumber(feesResponse.insurance_monthly_fee);
            var membership_fee = parsePHPNumber(feesResponse.membership_fee_balance);

            $(`#interest_${loanId}`).val(interest.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $(`#interest_${loanId}`).data('interest', interest);

            if (parsePHPNumber(feesResponse.insurance_monthly_fee) !== 0) {
                $(`#insurance_${loanId}`).val(insurance.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            } else {
                $(`#insurance_${loanId}`).val('');
            }

            let $principal = $(`#principal_${loanId}`);
            $principal.data('principal-balance', parsePHPNumber(feesResponse.principal_balance));
            $principal.data('early-settlement-amount', parsePHPNumber(feesResponse.early_settlement_amount));
            $principal.data('special-loan-balance', parsePHPNumber(feesResponse.special_loan_balance));
            $principal.data('monthly-amortization', monthly_amortization);
            $principal.data('previous-interest-deficit', parsePHPNumber(feesResponse.previous_interest_deficit));
            $principal.data('current-interest', parsePHPNumber(feesResponse.current_interest));

            // Cache principal advance balance
            let principal_advance = parsePHPNumber(feesResponse.principal_advance_balance);
            $(`#principal_advance_${loanId}`).data('principal-advance-balance', principal_advance);

            $(`#membership_fee_${loanId}`).val(membership_fee.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $(`#insurance_month_${loanId}`).val(moment(feesResponse.interest_end_date).format('M'));
            $(`#insurance_year_${loanId}`).val(moment(feesResponse.interest_end_date).format('Y'));

            // Initialize default principal if 0 or empty
            let loanInfo = clientLoansMap[loanId];
            let loan_type = loanInfo ? loanInfo.loan_type : '';
            let currentPrincipalVal = parsePHPNumber($principal.val());
            if (currentPrincipalVal === 0) {
                let defaultPrincipal = 0;
                if (loan_type === 'special-loan') {
                    defaultPrincipal = parsePHPNumber(feesResponse.principal_balance);
                } else {
                    defaultPrincipal = monthly_amortization;
                }
                $principal.val(defaultPrincipal.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            // Initialize default principal advance if 0 or empty
            let currentPrincipalAdvanceVal = parsePHPNumber($(`#principal_advance_${loanId}`).val());
            if (currentPrincipalAdvanceVal === 0) {
                $(`#principal_advance_${loanId}`).val(principal_advance.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            const datenow = moment().format('YYYY-MM-DD');
            if (picker) {
                picker.minDate = moment(feesResponse.interest_start_date);
                picker.maxDate = moment(datenow);
                picker.setStartDate(feesResponse.interest_start_date);
                picker.setEndDate(feesResponse.interest_end_date);
                picker.updateView();
                picker.updateCalendars();
            }

            calculateChange();
        }

        $(".btnSubmit").click(function (e) {
            e.preventDefault();
            transaction_type = $("[name='transaction_type']").find('option:selected').val();
            if(transaction_type == 'withdraw') {
                calculateChange()

                let hasEarlySettlement = $('.early-settlement-checkbox:checked').length > 0;

                if(hasEarlySettlement) {
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
                        $('.early-settlement-checkbox:checked').each(function() {
                            let loanId = $(this).attr('id').split('_')[1];
                            if (value === 'download') {
                                let iframe = document.createElement('iframe');
                                iframe.style.display = 'none';
                                iframe.src = baseUrl + '/soa/' + loanId + '/download';
                                document.body.appendChild(iframe);
                                iframe.onload = function () {
                                    iframe.contentWindow.focus();
                                    iframe.contentWindow.print();
                                };
                            }

                            if (value === 'print') {
                                let iframe = document.createElement('iframe');
                                iframe.style.display = 'none';
                                iframe.src = baseUrl + '/soa/' + loanId + '/print';
                                document.body.appendChild(iframe);
                                iframe.onload = function () {
                                    iframe.contentWindow.focus();
                                    iframe.contentWindow.print();
                                };
                            }
                        });

                        submitTransaction()
                    });

                    setTimeout(() => {
                        $('.swal-footer').css({
                            display: 'flex',
                            justifyContent: 'center',
                            gap: '10px'
                        });
                    }, 10);
                } else {
                    submitTransaction()
                }
            } else {
                submitTransaction()
            }
        });

        $(document).on('change input', ".loan-input, [name='amount']", function () {
            calculateChange();
        });

        $(document).on('change', '.loan-interest-input', function() {
            let loanId = $(this).attr('id').split('_')[1];
            var interest = parsePHPNumber($(this).val());
            var data_interest = parsePHPNumber($(this).data('interest'));
            var deficit = interest - data_interest;

            let strDeficit = deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            $(`#interest_deficit_${loanId}`).val(strDeficit);
        });

        $(document).on('change', '.early-settlement-checkbox', function () {
            let loanId = $(this).attr('id').split('_')[1];
            var is_early_settlement = $(this).prop('checked');
            if (is_early_settlement) {
                $(`#include_special_loan_balance_${loanId}`).prop('checked', true);
                $(`#interest_${loanId}`).prop('readonly', true);
            } else {
                $(`#include_special_loan_balance_${loanId}`).prop('checked', false);
                $(`#interest_${loanId}`).prop('readonly', false);
            }
            getSingleLoanData(loanId);
        });

        $(document).on('change', '.loan-principal-input', function () {
            calculateChange();
        });

        $(document).on('change', '.my-insurance', async function () {
            let loanId = $(this).attr('id').split('_')[2];
            if (!loanId) return;

            console.log('get insurance fee for loan ' + loanId);
            let feesResponse = await getFeesByMonthAndYear(loanId);
            if (parsePHPNumber(feesResponse.insurance_monthly_fee) !== 0) {
                $(`#insurance_${loanId}`).val(parsePHPNumber(feesResponse.insurance_monthly_fee).toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            } else {
                $(`#insurance_${loanId}`).val('');
            }
            calculateChange();
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
                }
            });

            elementChoices.addEventListener('addItem', calculateTotal);
            elementChoices.addEventListener('removeItem', calculateTotal);
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

        function calculateTotal() {
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

            let strTotal = total.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            $("[name='amount']").val(strTotal).trigger('change');
        }

        function calculateSingleLoanChange(loanId) {
            let $card = $(`#loan-card-${loanId}`);
            if ($card.length === 0) return { total_due: 0, amount: 0, principal: 0 };

            var interest = parsePHPNumber($(`#interest_${loanId}`).val());
            var data_interest = parsePHPNumber($(`#interest_${loanId}`).data('interest'));
            var monthly_amortization = parsePHPNumber($(`#principal_${loanId}`).data('monthly-amortization'));
            var insurance = parsePHPNumber($(`#insurance_${loanId}`).val());
            var membership_fee = parsePHPNumber($(`#membership_fee_${loanId}`).val());
            var $principal = $(`#principal_${loanId}`);
            var principal_amount = parsePHPNumber($(`#principal_${loanId}`).val());

            var $principalAdvance = $(`#principal_advance_${loanId}`);
            var principal_advance_amount = parsePHPNumber($principalAdvance.val());

            let loanInfo = clientLoansMap[loanId];
            let loan_type = loanInfo ? loanInfo.loan_type : '';

            var principal = 0;
            if ($(`#is_early_settlement_${loanId}`).prop('checked')) {
                principal = $principal.data('principal-balance') || 0;
                if ($(`#include_special_loan_balance_${loanId}`).prop('checked')) {
                    principal = $principal.data('early-settlement-amount') || 0;
                }
            } else {
                if (loan_type === 'open-term' || loan_type === 'loan-with-term') {
                    principal = principal_amount;
                } else if (loan_type === 'special-loan') {
                    principal = $principal.data('principal-balance') || 0;
                } else {
                    principal = principal_amount === 0 ? monthly_amortization : principal_amount;
                }
            }

            if (principal < 0) {
                principal = 0;
            }

            let strPrincipal = principal.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (document.activeElement !== $principal[0]) {
                $principal.val(strPrincipal);
            }

            // Principal Advance
            var principal_advance = principal_advance_amount;
            if (principal_advance < 0) {
                principal_advance = 0;
            }
            let strPrincipalAdvance = principal_advance.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            if (document.activeElement !== $principalAdvance[0]) {
                $principalAdvance.val(strPrincipalAdvance);
            }

            let exclude_interest = $(`#exclude_interest_${loanId}`).prop('checked');
            let interestPaid = exclude_interest ? 0 : interest;
            let amount = interestPaid + insurance + principal + principal_advance + membership_fee;

            // Write the calculated allocated amount back to the hidden input field
            $(`#loan_amount_${loanId}`).val(amount.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            // Interest Deficit
            let deficit = interestPaid - data_interest;
            if (deficit > 0) {
                deficit = 0;
            }
            let strDeficit = deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            $(`#interest_deficit_${loanId}`).val(strDeficit);

            $(`#loan_total_due_display_${loanId}`).text(amount.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            return {
                total_due: amount,
                amount: amount,
                principal: principal
            };
        }

        async function calculateChange() {
            if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            let selectedLoans = $("#loan_id").val() || [];
            if (selectedLoans.length === 0) {
                $("[name='due_change']").val(0).trigger('change');
                $('#change_container').addClass('d-none');
                return;
            }

            let total_due_all = 0;
            let total_allocated_all = 0;
            let invalid_state = false;

            for (let i = 0; i < selectedLoans.length; i++) {
                let loanId = selectedLoans[i];
                let res = calculateSingleLoanChange(loanId);
                total_due_all += res.total_due;
                total_allocated_all += res.amount;
                if (res.principal < 0) {
                    invalid_state = true;
                }
            }

            // Check if there are selected merchant slips
            const elementChoices = document.querySelector('[name="transaction_code_select[]"]');
            let hasSlips = false;
            if (elementChoices && choicesTransactionCode) {
                hasSlips = choicesTransactionCode.getValue().length > 0;
            }

            // If no slips, overall amount is exactly the sum of allocated amounts
            if (!hasSlips) {
                $("[name='amount']").val(total_allocated_all.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            let overall_amount = parsePHPNumber($("[name='amount']").val());
            let change = overall_amount - total_allocated_all;

            console.log('Overall Amount:', overall_amount);
            console.log('Total Allocated:', total_allocated_all);
            console.log('Change:', change);

            if (change < 0 || invalid_state || total_allocated_all === 0) {
                $('.btnSubmit').attr('disabled', true);
                $("[name='amount']").addClass('is-invalid');
                $("[name='due_change']").val(0).trigger('change');
            } else {
                $('.btnSubmit').attr('disabled', false);
                $("[name='amount']").removeClass('is-invalid');

                $("[name='due_change']").val(change.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })).trigger('blur');
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
