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
                            <label class="form-label" for="loan_id">Select Loan</label>
                            <select name="loan_id" id="loan_id" class="form-control form-control-sm">
                                <option value="">-select-</option>
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
                        <div class="col-md-3 form-group">
                            <label class="form-label" for="interest">Interest</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end" name="interest" id="interest" placeholder="0.00"/>
                            <span id="interest_and_interest_deficit" class="text-xs"></span>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="form-label" for="interest_period" class="form-label">Interest Period</label>
                            <input type="text" name="interest_period" id="interest_period" class="form-control form-control-sm"/>
                            <input type="checkbox" name="exclude_interest" id="exclude_interest" class="form-check-input" />
                            <label class="form-check-label" for="exclude_interest"><small>Exclude Interest</small></label>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label interest-label" for="interest_deficit">Interest Deficit</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end" name="interest_deficit" readonly placeholder="0.00"/>
                        </div>

                        {{-- <div class="col-md-4 form-group">
                            <label class="form-label" for="interest_month" class="form-label">Month</label>
                            <select name="interest_month" id="interest_month" class="form-control form-control-sm">
                                <option value="" disabled>Select</option>
                                @foreach (month_names() as $number => $name)
                                    <option value="{{ $number }}">{{ $name }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="interest_year" class="form-label">Year</label>
                            <input type="text" class="form-control form-control-sm" id="interest_year" name="interest_year">
                        </div> --}}
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance">Insurance</label>
                            <input type="text" class="form-control form-control-sm decimal-only text-end" name="insurance" id="insurance" placeholder="0.00"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_month" class="form-label">Month</label>
                            <select name="insurance_month" id="insurance_month" class="form-control form-control-sm my-insurance">
                                <option value="" disabled>Select</option>
                                @foreach (month_names() as $number => $name)
                                    <option value="{{ $number }}"  {{ $number == date('m') ? 'selected' : ''}}>{{ $name }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label" for="insurance_year" class="form-label">Year</label>
                            <input type="text" class="form-control form-control-sm my-insurance" id="insurance_year" name="insurance_year" value="{{date('Y')}}" min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label" for="principal">Principal</label>
                                <input type="text" class="form-control form-control-sm decimal-only text-end" name="principal" id="principal" placeholder="0.00" readonly/>
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-label" for="membership_fee">Membership Fee</label>
                                <input type="text" class="form-control form-control-sm decimal-only text-end" name="membership_fee" id="membership_fee" placeholder="0.00"/>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_early_settlement" id="is_early_settlement" class="form-check-input" />
                                <label class="form-check-label" for="is_early_settlement"><small>Early Settlement</small></label>
                            </div>
                            <div class="special-loan-group form-check">
                                <input type="checkbox" name="include_special_loan_balance" id="include_special_loan_balance" class="form-check-input" />
                                <label class="form-check-label" for="include_special_loan_balance"><small>Include Special Loan Balance</small></label>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                                {{-- <div class="form-group">
                                    <label class="form-label" for="change_mop">Change - Transaction Code</label>
                                    <input type="text" class="form-control" id="change_transaction_code" name="change_transaction_code" maxlength="50"/>
                                </div> --}}
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
        let repayment_order = [];
        let current_loan_id = null;
        let transaction_type;
        let choicesTransactionCode;
        let client_id = {{ $client->id }};

        transaction_type = $("[name='transaction_type']").find('option:selected').val();

        if (transaction_type == 'withdraw') {
            $(".withdraw").show()
            calculateChange();
        } else {
            $(".withdraw").hide()
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
            } else if (type == "deposit" || type == "cash") {
                $("#mode_of_payment_container").hide();
                $(".deposit-withdraw").show();
                $('.withdraw').hide();
            } else {
                $("#mode_of_payment_container").hide();
                $(".deposit-withdraw").hide();
                $('.withdraw').hide();
            }
        });

        $(".btnSubmit").click(function (e) {
            e.preventDefault();
            transaction_type = $("[name='transaction_type']").find('option:selected').val();
            if(transaction_type == 'withdraw') {
                calculateChange()

                var is_early_settlement = $("[name='is_early_settlement']").prop('checked');

                if(is_early_settlement) {
                    const baseUrl = `{{ env('APP_URL') }}`;
                    swal({
                        title: 'Select Action',
                        text: 'Choose whether to download or print SOA.',
                        icon: 'info',
                        buttons: {
                            // cancel: 'Cancel',
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
                        let selectedOption = $("[name='loan_id']").find(':selected');
                        let id = selectedOption.val();

                        if (value === 'download') {
                            let iframe = document.createElement('iframe');
                            iframe.style.display = 'none';
                            iframe.src = baseUrl + '/soa/' + id + '/download';
                            document.body.appendChild(iframe);
                            iframe.onload = function () {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            };
                        }

                        if (value === 'print') {
                            let iframe = document.createElement('iframe');
                            iframe.style.display = 'none';
                            iframe.src = baseUrl + '/soa/' + id + '/print';
                            document.body.appendChild(iframe);
                            iframe.onload = function () {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            };
                        }

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

        $(" [name='amount'], [name='include_special_loan_balance'], [name='membership_fee'],[name='exclude_interest'],[name='interest']")
            .on('change', function () {
            calculateChange()
        })

        $("[name='is_early_settlement']").on('change', function () {
            var is_early_settlement = $("[name='is_early_settlement']").prop('checked');
            if(is_early_settlement) {
                $("[name='include_special_loan_balance']").prop('checked', true);
                getData();
                $("[name='interest']").prop('readonly', true);

            } else {
                $("[name='include_special_loan_balance']").prop('checked', false);
                $("[name='interest']").prop('readonly', false);

            }
            calculateChange()
        });

        $("[name='principal']").on('change', function () {
            let selectedOption = $("[name='loan_id']").find(':selected');
            let loan_type = selectedOption.data('loan-type');

            if (loan_type === 'open-term' || loan_type === 'loan-with-term') {
                calculateChange()
            }
        })

        $("#loan_id").on("change", function () {
            let selectedOption = $(this).find(':selected');
            let loan_type = selectedOption.data('loan-type');

            if (loan_type === 'open-term' || loan_type === 'loan-with-term' || loan_type === 'special-loan') {
                $("[name='principal']").prop('readonly', false);
            } else {
                $("[name='principal']").prop('readonly', true);
            }

            if (loan_type == 'special-loan') {
                $(".special-loan-group").hide()
            } else {
                $(".special-loan-group").show()
            }

            getData();
        })

        $('input[name="interest_period"]').daterangepicker({
            opens: 'left'
        }, function (start, end, label) {
            // $("[name='transaction_date']").val(end.format('YYYY-MM-DD'))
            $("[name='insurance_month']").val(end.format('M'));
            $("[name='insurance_year']").val(end.format('Y'));
            getData()
        });

        $('input[name="insurance_year"]').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });

        $('.my-insurance').on('change', async function () {
            var loan_id = $("#loan_id").find('option:selected').val()

            if (loan_id === '') return;

            console.log('get insurance fee')
            feesResponse = await getFeesByMonthAndYear(loan_id);
            if (feesResponse.insurance_monthly_fee !== 0) {
                $("[name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
            } else {
                $("[name='insurance']").val('');
            }
            calculateChange()
        })


        $("[name='transaction_date']").on('change', function () {
            if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            const endDate = $(this).val();

            const picker = $('input[name="interest_period"]').data('daterangepicker');
            if (picker) {
                picker.setEndDate(moment(endDate));
                picker.updateView();
                picker.updateCalendars();
                getData();
            }
        })

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
        })

        $('#mode_of_payment').on('change', function () {
            //show loading screen
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            const elementChoices = document.querySelector('[name="transaction_code_select[]"]');

            // Destroy previous instance if it exists
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

                    // clear existing options
                    $("[name='amount']").val(0);

                    // prepare choices data
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

                    // set new choices
                    choicesTransactionCode.setChoices(options, 'value', 'label', true);
                    //hide loading screen
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });
                }
            });

            elementChoices.addEventListener('addItem', calculateTotal);
            elementChoices.addEventListener('removeItem', calculateTotal);

        })

        $("[name='interest']").on('change', function () {
            var interest = parseFloat($("[name='interest']").val())
            var data_interest = parseFloat($("[name='interest']").data('interest'))
            var deficit = interest - data_interest;

            let strDeficit = deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })

            $("[name='interest_deficit']").val(strDeficit)
        });

        $("#change_mop").on("change", function() {
            $('.attachment').toggleClass('d-none', $(this).val() !== 'authorized_person');
        }).trigger("change");

        function submitTransaction() {
            let form = $("#frmTransactionModal")[0];


            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let form = $('#frmTransactionModal')[0];
                let formData = new FormData(form);

                // add extra field (equivalent to push)
                formData.append('client_id', client_id);

                let transaction_type = $("[name='transaction_type']").val();
                var txn_code = $("#transaction_code").val()

                if(transaction_type == 'deposit' && txn_code == '') {
                    $("[name='transaction_code']").addClass('is-invalid')
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });
                    return
                } else {
                    $("[name='transaction_code']").removeClass('is-invalid')
                }

                if (transaction_type === 'deposit') {
                    let mop = $("[name='transaction_type'] option:selected").data('mop');

                    // overwrite instead of map
                    formData.set('mode_of_payment', mop);
                }

                const picker = $('input[name="interest_period"]').data('daterangepicker');

                if (picker) {
                    formData.append('interest_start_date', picker.startDate.format('YYYY-MM-DD'));
                    formData.append('interest_end_date', picker.endDate.format('YYYY-MM-DD'));
                }


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
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if (response.status == 'success') {
                            // wait for loader to fully disappear
                            setTimeout(function () {

                                showToast.success(response.message);

                                // redirect AFTER toast displays
                                setTimeout(function () {
                                    window.location.href = '/savings/' + client_id + '/transactions';
                                }, 1000);

                            }, 300);
                        } else {
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
        }

        async function getData() {
            var loan_id = $("#loan_id").find('option:selected').val()

            if (loan_id === '') return;


            feesResponse = await getFees(loan_id);
            repayment_order = feesResponse.repayment_order;
            var monthly_amortization = parseFloat(feesResponse.monthly_amortization.replace(/,/g, ''))
            var interest = parseFloat(feesResponse.interest.replace(/,/g, ''))
            $("[name='interest']").val(parseFloat(feesResponse.interest.replace(/,/g, '')));
            $("[name='interest']").data('interest',parseFloat(feesResponse.interest.replace(/,/g, '')));

            if (feesResponse.insurance_monthly_fee !== 0) {
                $("[name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
            } else {
                $("[name='insurance']").val('');
            }
            $("[name='principal']").data('principal-balance', parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
            $("[name='principal']").data('early-settlement-amount', parseFloat(feesResponse.early_settlement_amount.replace(/,/g, '')));
            $("[name='principal']").data('special-loan-balance', parseFloat(feesResponse.special_loan_balance.replace(/,/g, '')));
            $("[name='principal']").data('monthly-amortization', parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
            $("[name='principal']").data('previous-interest-deficit', parseFloat(feesResponse.previous_interest_deficit.replace(/,/g, '')));
            $("[name='principal']").data('current-interest', parseFloat(feesResponse.current_interest.replace(/,/g, '')));
            $("[name='membership_fee']").val(parseFloat(feesResponse.membership_fee_balance.replace(/,/g, '')));
            $("[name='insurance_month']").val(moment(feesResponse.interest_end_date).format('M'));
            $("[name='insurance_year']").val(moment(feesResponse.interest_end_date).format('Y'));
            const picker = $('input[name="interest_period"]').data('daterangepicker');
            const datenow = moment().format('YYYY-MM-DD');
            if (picker) {
                picker.minDate = moment(feesResponse.interest_start_date);
                picker.maxDate = moment(datenow);
                picker.setStartDate(feesResponse.interest_start_date)
                picker.setEndDate(feesResponse.interest_end_date)
                picker.updateView();    // visually update the calendar
                picker.updateCalendars();
            }

            calculateChange()

        }

        function getNotaryFee(principal) {
            return $.ajax({
                type: 'GET',
                url: '{{ route('get_notary_fee') }}',
                data: { amount: principal },
            })
        }

        function getInsuranceFee(principal) {
            return $.ajax({
                type: 'GET',
                url: '{{ route('get_insurance_fee') }}',
                data: { amount: principal },
            })
        }

        function getFees(loan_id) {
            const picker = $('input[name="interest_period"]').data('daterangepicker');
            const endDate = picker.endDate.format('YYYY-MM-DD');
            const transaction_date = $('[name="transaction_date"]').val();
            const data = { id: loan_id, interest_period: endDate, transaction_date: transaction_date };

            return $.ajax({
                type: 'GET',
                url: '{{ route('get_fees') }}',
                data: data,
            });
        }

        function getFeesByMonthAndYear(loan_id) {
            const insurance_month = $('[name="insurance_month"]').val();
            const insurance_year = $('[name="insurance_year"]').val();
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
            })

            $("[name='amount']").val(strTotal).trigger('change');


        }

        async function calculateChange() {
            if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            var amount = parseFloat($("[name='amount']").val().replace(/,/g, ''))
            var interest = parseFloat($("[name='interest']").val().replace(/,/g, ''))
            var data_interest = parseFloat($("[name='interest']").data('interest'))
            var monthly_amortization = parseFloat($("[name='principal']").data('monthly-amortization'))
            var insurance = parseFloat($("[name='insurance']").val().replace(/,/g, ''))
            var membership_fee = parseFloat($("[name='membership_fee']").val().replace(/,/g, ''))
            var $principal = $("[name='principal']")
            var principal_amount = parseFloat($("[name='principal']").val().replace(/,/g, ''))
            let selectedOption = $("[name='loan_id']").find(':selected');
            let loan_type = selectedOption.data('loan-type');
            let loan_id = selectedOption.val();

            if (!loan_id) return;

            if ( repayment_order.length === 0 || current_loan_id !== loan_id ) {
                const feesResponse = await getFees(loan_id);
                repayment_order = feesResponse.repayment_order || [];
                current_loan_id = loan_id;
            }

            if (isNaN(principal_amount)) principal_amount = 0;
            if (isNaN(amount)) amount = 0;
            if (isNaN(interest)) interest = 0;
            if (isNaN(data_interest)) data_interest = 0;
            if (isNaN(insurance)) insurance = 0;
            if (isNaN(membership_fee)) membership_fee = 0;

            /**
             * Balances
             */
            const balances = {
                Fees: insurance,
                Interest: interest,
                Principal: principal_amount,
                Penalty: 0
            };
            console.log(loan_type)
            console.log(balances)

            /**
            * Distributed amounts
            */
            const distributed = {
                Fees: 0,
                Interest: 0,
                Principal: 0,
                Penalty: 0
            };

            /**
             * Apply repayment order dynamically
             */
            let remaining = amount;

            repayment_order.forEach(type => {
                let balance = balances[type] || 0;
                if(type == 'Fees') {
                    balance = balances[type] + membership_fee
                }

                // if(type == 'Principal') {
                //     balance = monthly_amortization
                // }

                if (
                    balance <= 0
                    || remaining <= 0
                ) {
                    return;
                }

                let applied =
                    Math.min(balance, remaining);

                distributed[type] = applied;

                remaining -= applied;
            });


            // $("[name='interest']").val(distributed.Interest);
            // $("[name='principal']").val(distributed.Principal);
            var deficit = distributed.Interest - data_interest;
            let strDataInterest = distributed.Interest.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })

            let strDeficit = deficit.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })

            $("[name='interest']").val(strDataInterest)
            if(deficit > 0) {
                $("[name='interest_deficit']").val(0)
            } else {
                $("[name='interest_deficit']").val(strDeficit)
            }

            var principal = 0;
            if ($("[name='is_early_settlement']").prop('checked')) {
                principal = $principal.data('principal-balance');
                if ($("[name='include_special_loan_balance']").prop('checked')) {
                    principal = $principal.data('early-settlement-amount');



                    $("[name='interest']").val(strDataInterest)
                    $("[name='interest_deficit']").val(0)
            }
            } else {

                if (loan_type === '' || loan_type === undefined) return;

                if (loan_type === 'open-term' || loan_type === 'loan-with-term') {
                    principal = principal_amount;
                } else if (loan_type === 'special-loan') {
                    principal = $principal.data('principal-balance');
                } else {
                    $principal.val('');
                    x_amount = amount - membership_fee;
                    principal = x_amount - distributed.Interest - insurance;
                }

                if (principal < 0) {
                    principal = 0;
                }
            }

            let strPrincipal = principal.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })

            $principal.val(strPrincipal);


            var change;
            if ($("[name='exclude_interest']").prop('checked')) {
                change = amount - (insurance + principal + membership_fee);
            } else {
                change = amount - (distributed.Interest + insurance + principal + membership_fee);
            }

            console.log('amount:' + amount)
            console.log('interest:' + distributed.Interest)
            console.log('insurance:' + insurance)
            console.log('principal:' + principal)
            console.log('change:' + change)
            if (change < 0 || principal < 0) {
                $('.btnSubmit').attr('disabled', true)
                $("[name='amount']").addClass('is-invalid')
                $("[name='due_change']").val(0).trigger('change')
            } else {
                $('.btnSubmit').attr('disabled', false)
                $("[name='amount']").removeClass('is-invalid')

                let strChange = change.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })

                $("[name='due_change']").val(change).trigger('blur')
            }

            $('#change_container').toggleClass('d-none', change <= 0);

        }

        function getBalance() {
            var client_id = $("#client_id").val();
            return $.ajax({
                type: 'GET',
                url: `{{ route('client.get-balance',':client_id') }}`.replace(':client_id', client_id)
            })
        }
    })
</script>
@endpush