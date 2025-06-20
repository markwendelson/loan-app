@extends('layouts.app')

@push('css')
<style>
    .bg-default {
         background-color: #1f807f;
     }
     .bg-default th {
         color: white; /* Set white text for <th> elements */
         border: 1px solid white;
     }
 </style>
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
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
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
                <h2 id="client_balance">Balance: {{$client->balance}}</h2>
                <button class="btn btn-primary float-end btn-new"><i class="fa fa-plus"></i> New Transaction</button>
            </div>
        </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}"/>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblSavings">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="120">Date</th>
                            <th class="text-center" width="150">Description</th>
                            <th class="text-center" width="150">Amount</th>
                            <th class="text-center" width="150">Notes</th>
                            <th class="text-center" width="150">Transaction Type</th>
                            <th class="text-center" width="150">Classifications</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    @include('savings.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {

        var client_id = $("#client_id").val();

        const table=$('#tblSavings').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: `{{ route('savings.transactions',':client_id') }}`.replace(':client_id',client_id)

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
                        if (data == 'deposit') {
                            return '<span class="badge badge-success">deposit</span>';
                        } else if (data == 'withdraw') {
                            return '<span class="badge badge-danger">withdraw</span>';
                        }
                    }
                },
                { data: 'classifications', name: 'classifications' },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        return `
                                <button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>
                            `;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [6] },
                { className: "text-end", targets: [2] },
            ]
        });

        $('.btn-new').on('click', function() {
            showModal('modalTransaction')
            $('.withdraw').hide();
        })

        $("#modalTransaction .btnSubmit").click(function (e) {
            e.preventDefault();

            let form=$("#frmTransactionModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData=$("#frmTransactionModal").serializeArray();
                formData.push({ name: 'client_id', value: client_id });


                var id=$("#modalTransaction [name='id']").val();
                if(id) {
                    _method='PUT';
                    _url=`{{route('savings.update', ':id')}}`.replace(':id',id);
                } else {
                    _method='POST';
                    _url=`{{route('savings.store')}}`;
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
                        if(response.status == 'success'){
                            hideModal('modalTransaction');
                            showToast.success(response.message)
                            table.ajax.reload();
                            $("#client_balance").text('Balance: ' + response.balance)
                        }else{
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

        table.on("click", ".btnEdit", function(){
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
                $("#modalTransaction [name='amount']").val(data.amount);
                $("#modalTransaction [name='interest']").val(data.interest);
                $("#modalTransaction [name='interest_month']").val(data.interest_month);
                $("#modalTransaction [name='interest_year']").val(data.interest_year);
                $("#modalTransaction [name='insurance']").val(data.insurance);
                $("#modalTransaction [name='insurance_month']").val(data.insurance_month);
                $("#modalTransaction [name='insurance_year']").val(data.insurance_year);
                $("#modalTransaction [name='principal']").val(data.principal);
                $("#modalTransaction [name='description']").val(data.description);
                $("#modalTransaction [name='notes']").val(data.notes);
                $("#modalTransaction [name='classifications']").val(data.classifications);
                showModal('modalTransaction')

                if(data.transaction_type == 'withdraw'){
                    $('.withdraw').show();
                    calculateChange();
                }else{
                    $('.withdraw').hide();
                }

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }

        
        $('#transaction_type').on('change', function() {
            let selected = $(this).val();
            if(selected == 'withdraw'){
                $('.withdraw').show();
                calculateChange();
            }else{
                $('.withdraw').hide();
            }
        })


        var transaction_type;
        $("[name='transaction_type']").on('change', function() {
            transaction_type = $(this).find('option:selected').val();

            if(transaction_type == 'withdraw') {
                $(".withdraw").show()
            } else {
                $(".withdraw").hide()
            }

            calculateChange();

        })

        $("[name='amount'],[name='interest'],[name='insurance'],[name='principal']").on('change', function() {
            calculateChange();
        })

        $("#loan_id").on("change", function() {
            calculateLoan();
        })
        async function calculateLoan() {
            
            var principal = $("#loan_id").find('option:selected').data('principal');
            var interest_rate = $("#loan_id").find('option:selected').data('interest-rate');
            var term = $("#loan_id").find('option:selected').data('term');
            var loan_type = $("#loan_id").find('option:selected').data('loan-type');


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

                interest = (0.03/30) * number_of_days * principal_balance
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

            var total_deductions = Math.floor(advance_interest) + Math.ceil(service_fee) + Math.floor(insurance_fee) + Math.floor(notary_fee);

            var net_proceeds = principal - total_deductions;

            // $("#modalTransaction [name='interest']").val(data.interest);
            // $("#modalTransaction [name='interest_month']").val(data.interest_month);
            // $("#modalTransaction [name='interest_year']").val(data.interest_year);
            // $("#modalTransaction [name='insurance']").val(data.insurance);
            // $("#modalTransaction [name='insurance_month']").val(data.insurance_month);
            // $("#modalTransaction [name='insurance_year']").val(data.insurance_year);
            $("#modalTransaction [name='principal']").val(monthly_amortization);
            // $('.advance_interest').text(currencyFormat(Math.floor(advance_interest)))
            // $('.service_fee').text(currencyFormat(Math.ceil(service_fee)))
            // $('.insurance_fee').text(currencyFormat(Math.floor(insurance_fee)))
            // $('.notary_fee').text(currencyFormat(Math.floor(notary_fee)))
            // $('.monthly_amortization').text(currencyFormat(monthly_amortization))
            // $('.net_proceeds').text(currencyFormat(Math.floor(net_proceeds)))
            $("[name='amount'],[name='interest'],[name='insurance'],[name='principal']").trigger('change')

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

    });

    function calculateChange() {
            var amount = parseFloat($("#modalTransaction [name='amount']").val())
            var interest = parseFloat($("#modalTransaction [name='interest']").val())
            var insurance = parseFloat($("#modalTransaction [name='insurance']").val())
            var principal = parseFloat($("#modalTransaction [name='principal']").val())

            if (isNaN(amount)) return;
            if (isNaN(interest)) interest = 0;
            if (isNaN(insurance)) insurance = 0;
            if (isNaN(principal)) principal = 0;

            if(transaction_type === 'deposit') {
                return;
            }

            var change;
            change = amount - (interest + insurance + principal);
            if(change < 0) {
                $('.btnSubmit').attr('disabled', true)
            } else {
                $('.btnSubmit').attr('disabled', false)
            }
            $("[name='change']").val(change)
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
                    success: function(response) {
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
