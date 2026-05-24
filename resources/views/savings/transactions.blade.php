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

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const canDelete = {{ auth() -> user() -> can('delete-savings-transaction') ? 1 : 0 }};
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