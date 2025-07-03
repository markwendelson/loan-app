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
                    <h4>Savings | Manage Savings | {{$client->details->fullname}}</h4>
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
            calculateChange()

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
        transaction_type = $("[name='transaction_type']").find('option:selected').val();

        if(transaction_type == 'withdraw') {
            $(".withdraw").show()
            calculateChange();
        } else {
            $(".withdraw").hide()
        }

        $("[name='amount']").on('change', function() {
            calculateChange()
        })

        $("[name='is_early_settlement']").on('change', function () {
            calculateChange()
        });

        $("#loan_id").on("change", function() {
            getData();
        })
             
        async function getData(interest_period = null) {
            var loan_id = $("#loan_id").find('option:selected').val()
            console.log(interest_period)

            if(loan_id ==='') return;

            const data = {}
            if (interest_period !== null) {
                data.interest_period = interest_period;
            }

            console.log(data)

            feesResponse = await getFees(loan_id,data.interest_period);
            var monthly_amortization = parseFloat(feesResponse.monthly_amortization.replace(/,/g, ''))
            var interest = parseFloat(feesResponse.interest.replace(/,/g, ''))
            $("#modalTransaction [name='interest']").val(parseFloat(feesResponse.interest.replace(/,/g, '')));
            console.log(feesResponse.insurance_monthly_fee !== 0)
            if(feesResponse.insurance_monthly_fee !== 0) {
                $("#modalTransaction [name='insurance']").val(parseFloat(feesResponse.insurance_monthly_fee.replace(/,/g, '')));
            } else {
                $("#modalTransaction [name='insurance']").val('');
            }

            $("#modalTransaction [name='principal']").data('principal-balance',parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
            $("#modalTransaction [name='principal']").data('monthly-amortization',parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
            const picker = $('input[name="interest_period"]').data('daterangepicker');
            if (picker) {
                picker.minDate = moment(feesResponse.interest_start_date);
                picker.setStartDate(feesResponse.interest_start_date) 
                picker.setEndDate(feesResponse.interest_end_date)
                picker.updateView();    // visually update the calendar
                picker.updateCalendars();
            }


            if ($("#modalTransaction [name='is_early_settlement']").prop('checked')) {
                $("#modalTransaction [name='principal']").val(parseFloat(feesResponse.principal_balance.replace(/,/g, '')));
            } else {
                var amount = $("#modalTransaction [name='amount']").val()
                console.log(isNaN(amount))
                if(amount === '') return;

                var principal = amount - interest;
                console.log('principal:'+principal)
                console.log('monthly_amortization:'+monthly_amortization)

                if(principal<0) return;

                if(principal < monthly_amortization) {
                    console.log('x')
                    $("#modalTransaction [name='principal']").val(principal);
                } else {
                    $("#modalTransaction [name='principal']").val(parseFloat(feesResponse.monthly_amortization.replace(/,/g, '')));
                }
            }

            // $("[name='amount'],[name='interest'],[name='insurance'],[name='principal']").trigger('change')

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

        function getFees(loan_id, interest_period = null) {
            const data = { id: loan_id };
            if (interest_period !== null) {
                data.interest_period = interest_period;
            }

            return $.ajax({
                type: 'GET',
                url: '{{ route('get_fees') }}',
                data: data,
            });
        }

        $('input[name="interest_period"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            var interest_period = start.format('YYYY-MM-DD') + ' ' + end.format('YYYY-MM-DD')
            getData(interest_period)
        });

    });

    function calculateChange() {
            if($("[name='transaction_type']").find('option:selected').val() === 'deposit') return;

            var amount = parseFloat($("#modalTransaction [name='amount']").val())
            var interest = parseFloat($("#modalTransaction [name='interest']").val())
            var insurance = parseFloat($("#modalTransaction [name='insurance']").val())
            var $principal = $("#modalTransaction [name='principal']")
            $principal.val('');

            if (isNaN(amount)) return;
            if (isNaN(interest)) interest = 0;
            if (isNaN(insurance)) insurance = 0;

            var principal = 0;
            if ($("[name='is_early_settlement']").prop('checked')) {
                principal = $principal.data('principal-balance');
            } else {
                var monthly_amortization = $principal.data('monthly-amortization');
                principal = amount - interest - insurance;
                if(principal<0) {
                    $("#modalTransaction [name='amount']").addClass('is-invalid')
                    return;
                }
            }

            $principal.val(principal);

            var change;
            change = amount - (interest + insurance + principal);

            console.log('amount:'+amount)
            console.log('interest:'+interest)
            console.log('insurance:'+insurance)
            console.log('principal:'+principal)
            console.log('change:'+change)
            if(change < 0 || principal < 0) {
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
