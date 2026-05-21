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
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
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

<!-- button container -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>{{$client->details->fullname}}</h3>
                <span>{{$client->details->present_full_address}}</span>
            </div>
            <a href="{{route('client.ledger.principal', $client->id)}}" class="btn btn-sm btn-primary">Ledger</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h2 id="client_balance">Balance: {{$client->balance}}</h2>
            @canany(['deposit-transactions','withdraw-transactions','cash-transactions'])
            <button class="btn btn-primary float-end btn-new"><i class="fa fa-plus"></i> New Transaction</button>
            @endcan
        </div>
    </div>
</div>


<!-- table container -->
<div class="card">
    <div class="card-body">
        <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}" />
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tblSavings">
                <thead class="bg-default">
                    <tr>
                        <th class="text-center" width="120">Date</th>
                        <th class="text-center" width="150">Description</th>
                        <th class="text-center" width="150">Amount</th>
                        <th class="text-center" width="150">Notes</th>
                        <th class="text-center" width="150">Transaction Type</th>
                        <th class="text-center" width="150">Mode of Payment</th>
                        <th class="text-center" width="150">Classifications</th>
                        <th class="text-center" width="150"><i class="fa fa-cog"></i></th>
                        <!-- New column for Edit/Delete buttons -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


@include('savings.modal')

@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    $(document).ready(function () {
        const canDelete = {{ auth() -> user() -> can('delete-savings-transaction') ? 1 : 0 }};
        var client_id = $("#client_id").val();
        let repayment_order = [];
        let current_loan_id = null;


    const table = $('#tblSavings').DataTable({
        ordering: false,
        processing: true,
        serverSide: true,
        stateSave: true,
        searching: true,
        ajax: {
            url: `{{ route('savings.transactions',':client_id') }}`.replace(':client_id', client_id)

        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'notes', name: 'notes' },
            {
                data: 'transaction_type',
                name: 'transaction_type',
                render: function (data, type, row) {
                    let typeBadge = '';
                    let deletedBadge = '';

                    if (row.deleted_at) {
                        deletedBadge = ` <span class="badge badge-warning">reversal</span>`;
                        return `${deletedBadge}`;
                    }

                    if (data === 'deposit') {
                        typeBadge = `<span class="badge badge-success">deposit</span>`;
                    } else if (data === 'withdraw') {
                        typeBadge = `<span class="badge badge-danger">withdraw</span>`;
                    }

                    return `${typeBadge}`;
                }
            },
            { data: 'mop_trans_code', name: 'mop_trans_code' },
            { data: 'classifications', name: 'classifications' },
            {
                data: null,
                name: 'actions',
                render: function (data, type, row) {
                    if (row.deleted_at) return ``;

                    let button = '';

                    if (canDelete) {
                        button += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                    }


                    return `
                                <button class="btn btn-dark btn-xs" onclick="window.open('/savings/${row.id}/print', '_blank')"><i class="fa fa-print"></i></button>
                                ${button}
                            `;
                }
            }
        ],
        columnDefs: [
            { className: "text-center", targets: [7] },
            { className: "text-end", targets: [2] },
        ]
    });

    $('.btn-new').on('click', function () {
        showModal('modalTransaction')
        $('.withdraw').hide();
        $("#modalTransaction #mode_of_payment_container").hide();
        $("#modalTransaction .deposit-withdraw").hide();
    })

    $("#modalTransaction #transaction_type").change(function () {
        let type = $(this).val();
        if (type == "withdraw") {
            $("#modalTransaction #mode_of_payment_container").show();
            $("#modalTransaction .deposit-withdraw").show();
        } else if (type == "deposit" || type == "cash") {
            $("#modalTransaction #mode_of_payment_container").hide();
            $("#modalTransaction .deposit-withdraw").show();
        } else {
            $("#modalTransaction #mode_of_payment_container").hide();
            $("#modalTransaction .deposit-withdraw").hide();
        }
    });

    $("#modalTransaction .btnSubmit").click(function (e) {
        e.preventDefault();
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



    });

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


            var id = $("#modalTransaction [name='id']").val();
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
                        hideModal('modalTransaction');
                        showToast.success(response.message)
                        table.ajax.reload();
                        $("#client_balance").text('Balance: ' + response.balance)
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

    table.on("click", ".btnEdit", function () {
        let id = $(this).attr("data-id");
        getTransaction(id)
    })

    async function getTransaction(id) {
        try {
            let response = await fetch(`/savings/${id}`, {
                method: "GET",
            });

            if (!response.ok) throw new Error("Network response was not ok");

            let data = await response.json();

            $("#modalTransaction [name='id']").val(data.id);
            $("#modalTransaction [name='transaction_date']").val(data.transaction_date);
            $("#modalTransaction [name='transaction_type']").val(data.transaction_type);
            $("#modalTransaction [name='mode_of_payment']").val(data.mode_of_payment);
            $("#modalTransaction [name='amount']").val(data.amount);
            $("#modalTransaction [name='interest']").val(data.interest);
            $("#modalTransaction [name='insurance']").val(data.insurance);
            $("#modalTransaction [name='insurance_month']").val(data.insurance_month);
            $("#modalTransaction [name='insurance_year']").val(data.insurance_year);
            $("#modalTransaction [name='principal']").val(data.principal);
            $("#modalTransaction [name='description']").val(data.description);
            $("#modalTransaction [name='notes']").val(data.notes);
            $("#modalTransaction [name='classifications']").val(data.classifications);
            showModal('modalTransaction')

            if (data.transaction_type == 'withdraw') {
                $('.withdraw').show();
                calculateChange();
            } else {
                $('.withdraw').hide();
            }

        } catch (error) {
            console.error("Error fetching data:", error);
        }
    }


    $('#transaction_type').on('change', function () {
        let selected = $(this).val();
        if (selected == 'withdraw') {
            $('.withdraw').show();
            // getBalance().then(function(balanceResponse) {
            //     var balance = parseFloat(balanceResponse.balance.replace(/,/g, ''));
            //     $("[name='amount']").val(balance)
            // }).catch(function(error) {
            //     console.error("Failed to fetch balance:", error);
            // });

            // calculateChange();
        } else {
            $('.withdraw').hide();
        }
    })


    var transaction_type;
    transaction_type = $("[name='transaction_type']").find('option:selected').val();

    if (transaction_type == 'withdraw') {
        $(".withdraw").show()
        calculateChange();
    } else {
        $(".withdraw").hide()
    }

    $("[name='amount']").on('change', function () {
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

    $("[name='include_special_loan_balance']").on('change', function () {
        calculateChange()
    });

    $("[name='membership_fee']").on('change', function () {
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

    async function getData() {
        var loan_id = $("#loan_id").find('option:selected').val()

        if (loan_id === '') return;


        feesResponse = await getFees(loan_id);
        repayment_order = feesResponse.repayment_order;
        var monthly_amortization = parseFloat(feesResponse.monthly_amortization.replace(/,/g, ''))
        var interest = parseFloat(feesResponse.interest.replace(/,/g, ''))
        $("#modalTransaction [name='interest']").val(parseFloat(feesResponse.interest.replace(/,/g, '')));
        $("#modalTransaction [name='interest']").data('interest',parseFloat(feesResponse.interest.replace(/,/g, '')));

        if (feesResponse.insurance_monthly_fee !== 0) {
            $("#modalTransaction [name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
        } else {
            $("#modalTransaction [name='insurance']").val('');
        }
        $("#modalTransaction [name='principal']").data('principal-balance', parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('early-settlement-amount', parseFloat(feesResponse.early_settlement_amount.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('special-loan-balance', parseFloat(feesResponse.special_loan_balance.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('monthly-amortization', parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('previous-interest-deficit', parseFloat(feesResponse.previous_interest_deficit.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('current-interest', parseFloat(feesResponse.current_interest.replace(/,/g, '')));
        $("#modalTransaction [name='membership_fee']").val(parseFloat(feesResponse.membership_fee_balance.replace(/,/g, '')));
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
            $("#modalTransaction [name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
        } else {
            $("#modalTransaction [name='insurance']").val('');
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

    $('#modalTransaction #transaction_type').on('change', function () {
        var _val = $(this).find('option:selected').val()
        if (_val == 'deposit') {
            $('#modalTransaction #mop_transfer_container').show()
            $('#modalTransaction #mop_transfer_select_container').hide()
        } else {
            $('#modalTransaction #mop_transfer_container').hide()
            $('#modalTransaction #mop_transfer_select_container').show()
            $('#modalTransaction #mode_of_payment').trigger('change')
        }
    })

    let choicesTransactionCode;

    $('#modalTransaction #mode_of_payment').on('change', function () {
        //show loading screen
        $(".loader-wrapper").fadeIn("slow", function () {
            $(this).show();
        });

        const elementChoices = document.querySelector('#modalTransaction [name="transaction_code_select[]"]');

        // Destroy previous instance if it exists
        if (choicesTransactionCode) {
            choicesTransactionCode.destroy();
        }

        choicesTransactionCode = new Choices(elementChoices, {
            removeItemButton: true,
            duplicateItemsAllowed: false,
        });

        choicesTransactionCode.clearStore();


        var client_id = $("#client_id").val();
        var mode_of_payment = $("#mode_of_payment").val();

        $.ajax({
            type: 'GET',
            data: { client_id: client_id, mode_of_payment: mode_of_payment },
            url: `{{ route('ajax.get-merchant-slip') }}`,
            success: function (response) {

                // clear existing options
                $("#modalTransaction [name='amount']").val(0);

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

        $("#modalTransaction [name='amount']").val(total.toFixed(2));

    }

    async function calculateChange() {
        if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

        var amount = parseFloat($("#modalTransaction [name='amount']").val())
        var interest = parseFloat($("#modalTransaction [name='interest']").val())
        var data_interest = parseFloat($("#modalTransaction [name='interest']").data('interest'))
        var monthly_amortization = parseFloat($("#modalTransaction [name='principal']").data('monthly-amortization'))
        var insurance = parseFloat($("#modalTransaction [name='insurance']").val())
        var membership_fee = parseFloat($("#modalTransaction [name='membership_fee']").val())
        var $principal = $("#modalTransaction [name='principal']")
        var principal_amount = parseFloat($("#modalTransaction [name='principal']").val())
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


        $("#modalTransaction [name='interest']").val(distributed.Interest);
        // $("#modalTransaction [name='principal']").val(distributed.Principal);
        var deficit = distributed.Interest - data_interest;
        if(deficit > 0) {
            $("#modalTransaction [name='interest']").val(data_interest)
            $("#modalTransaction [name='interest_deficit']").val(0)
        } else {
            $("#modalTransaction [name='interest_deficit']").val(deficit)
        }

        var principal = 0;
        if ($("[name='is_early_settlement']").prop('checked')) {
            principal = $principal.data('principal-balance');
            if ($("[name='include_special_loan_balance']").prop('checked')) {
                principal = $principal.data('early-settlement-amount');
                $("#modalTransaction [name='interest']").val(data_interest)
                $("#modalTransaction [name='interest_deficit']").val(0)
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

        $principal.val(principal);


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
            $("#modalTransaction [name='amount']").addClass('is-invalid')
            $("[name='due_change']").val(0)
        } else {
            $('.btnSubmit').attr('disabled', false)
            $("#modalTransaction [name='amount']").removeClass('is-invalid')
            $("[name='due_change']").val(change)
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

    $("[name='exclude_interest'],[name='interest']").on('change', function () {
        calculateChange()
    });

    $("[name='interest']").on('change', function () {
        var interest = parseFloat($("#modalTransaction [name='interest']").val())
        var data_interest = parseFloat($("#modalTransaction [name='interest']").data('interest'))
        var deficit = interest - data_interest;
        $("#modalTransaction [name='interest_deficit']").val(deficit)
    });

    $("#change_mop").on("change", function() {
        $('.attachment').toggleClass('d-none', $(this).val() !== 'authorized_person');
    }).trigger("change");

});

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
                    url: '/savings/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showToast.success(response.message)
                        $('#tblSavings').DataTable().ajax.reload();
                        $("#client_balance").text('Balance: ' + response.balance)
                    }
                });

            }
        })
    }


</script>
@endpush