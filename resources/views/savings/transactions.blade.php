@extends('layouts.app')

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
        <div class="d-flex justify-content-between">
            <div>
                <h1 style="font-size: 3.5rem;">{{$client->details->fullname}}</h1>
                <h3>{{$client->details->present_full_address}}</h3>
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
        const canDelete = {{ auth() -> user() -> can('delete-savings-transaction') ? 1 : 0
    }};

    var client_id = $("#client_id").val();

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

        let form = $("#frmTransactionModal")[0];

        if (!form.checkValidity()) {
            form.reportValidity();
        } else {

            //show loading screen
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            let formData = $("#frmTransactionModal").serializeArray();
            formData.push({ name: 'client_id', value: client_id });

            transaction_type = $("[name='transaction_type']").find('option:selected').val();
            if(transaction_type == 'deposit') {
                let mop = $("[name='transaction_type']").find('option:selected').data('mop');
                formData = formData.map(field => {
                    if (field.name === 'mode_of_payment') {
                        return { name: 'mode_of_payment', value: mop };
                    }
                    return field;
                });
            }

            const picker = $('input[name="interest_period"]').data('daterangepicker');
            if (picker) {
                formData.push({ name: 'interest_start_date', value: picker.startDate.format('YYYY-MM-DD') });
                formData.push({ name: 'interest_end_date', value: picker.endDate.format('YYYY-MM-DD') });
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
    });

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
        $("[name='include_special_loan_balance']").prop('checked', true);
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
        var monthly_amortization = parseFloat(feesResponse.monthly_amortization.replace(/,/g, ''))
        var interest = parseFloat(feesResponse.interest.replace(/,/g, ''))
        $("#modalTransaction [name='interest']").val(parseFloat(feesResponse.interest.replace(/,/g, '')));

        if (feesResponse.insurance_monthly_fee !== 0) {
            $("#modalTransaction [name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
        } else {
            $("#modalTransaction [name='insurance']").val('');
        }
        $("#modalTransaction [name='principal']").data('principal-balance', parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('early-settlement-amount', parseFloat(feesResponse.early_settlement_amount.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('special-loan-balance', parseFloat(feesResponse.special_loan_balance.replace(/,/g, '')));
        $("#modalTransaction [name='principal']").data('monthly-amortization', parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
        $("#modalTransaction [name='membership_fee']").val(parseFloat(feesResponse.membership_fee_balance.replace(/,/g, '')));
        $("[name='insurance_month']").val(moment(feesResponse.interest_end_date).format('M'));
        $("[name='insurance_year']").val(moment(feesResponse.interest_end_date).format('Y'));
        const picker = $('input[name="interest_period"]').data('daterangepicker');
        const datenow = moment().format('YYYY-MM-DD');
        if (picker) {
            picker.minDate = moment(feesResponse.interest_start_date);
            picker.maxDate = datenow;
            picker.setStartDate(feesResponse.interest_start_date)
            picker.setEndDate(feesResponse.interest_end_date)
            picker.updateView();    // visually update the calendar
            picker.updateCalendars();
        }

        calculateChange()



        // if ($("#modalTransaction [name='is_early_settlement']").prop('checked')) {
        //     $("#modalTransaction [name='principal']").val(parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
        // } else {
        //     var amount = $("#modalTransaction [name='amount']").val()
        //     console.log(isNaN(amount))
        //     if(amount === '') return;

        //     var principal = amount - interest;
        //     console.log('principal:'+principal)
        //     console.log('monthly_amortization:'+monthly_amortization)

        //     if(principal<0) return;

        // if(principal < monthly_amortization) {
        //     console.log('x')
        //     $("#modalTransaction [name='principal']").val(principal);
        // } else {
        //     $("#modalTransaction [name='principal']").val(parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
        // }
        // }

        // $("[name='amount'],[name='interest'],[name='insurance'],[name='principal']").trigger('change')

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
        // var _val = $(this).val()
        // if (_val == 'palawan' || _val == 'bank_transfer' || _val == 'atm_withdrawal' || _val == 'cash') {
        //     $('#modalTransaction #mop_transfer_select_container').show()
        // } else {
        //     $('#modalTransaction #mop_transfer_select_container').hide()
        // }

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

    // $('#modalTransaction select[name="transaction_code_select"]').on('change', function() {
    //     var amount = $(this).find('option:selected').data('amount')
    //     $("#modalTransaction [name='amount']").val(amount)
    // })

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

    });

    function calculateChange() {
        if ($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

        var amount = parseFloat($("#modalTransaction [name='amount']").val())
        var interest = parseFloat($("#modalTransaction [name='interest']").val())
        var insurance = parseFloat($("#modalTransaction [name='insurance']").val())
        var membership_fee = parseFloat($("#modalTransaction [name='membership_fee']").val())
        var $principal = $("#modalTransaction [name='principal']")
        var principal_amount = parseFloat($("#modalTransaction [name='principal']").val())
        let selectedOption = $("[name='loan_id']").find(':selected');
        let loan_type = selectedOption.data('loan-type');

        if (isNaN(principal_amount)) principal_amount = 0;
        if (isNaN(amount)) amount = 0;
        if (isNaN(interest)) interest = 0;
        if (isNaN(insurance)) insurance = 0;
        if (isNaN(membership_fee)) membership_fee = 0;

        var principal = 0;
        if ($("[name='is_early_settlement']").prop('checked')) {
            principal = $principal.data('principal-balance');
            if ($("[name='include_special_loan_balance']").prop('checked')) {
                principal = $principal.data('early-settlement-amount');
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
                principal = x_amount - interest - insurance;
            }

            if (principal < 0) {
                $("#modalTransaction [name='amount']").addClass('is-invalid')
                return;
            }
        }

        $principal.val(principal);


        var change;
        if ($("[name='exclude_interest']").prop('checked')) {
            change = amount - (insurance + principal + membership_fee);
        } else {
            change = amount - (interest + insurance + principal + membership_fee);
        }

        console.log('amount:' + amount)
        console.log('interest:' + interest)
        console.log('insurance:' + insurance)
        console.log('principal:' + principal)
        console.log('change:' + change)
        if (change < 0 || principal < 0) {
            $('.btnSubmit').attr('disabled', true)
            $("#modalTransaction [name='amount']").addClass('is-invalid')
            $("[name='due_change']").val('')
        } else {
            $('.btnSubmit').attr('disabled', false)
            $("#modalTransaction [name='amount']").removeClass('is-invalid')
            $("[name='due_change']").val(change)
        }
    }

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

    function getBalance() {
        var client_id = $("#client_id").val();
        return $.ajax({
            type: 'GET',
            url: `{{ route('client.get-balance',':client_id') }}`.replace(':client_id', client_id)
        })
    }

    $("[name='exclude_interest']").on('change', function () {
        calculateChange()
    });



</script>
@endpush