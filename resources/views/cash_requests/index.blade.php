@extends('layouts.app')

@section('title') Cash Requests @endsection

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Cash Requests</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item active">Cash Requests</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0 card-no-border">
                    <div class="d-flex justify-content-between">
                        <h5>Cash Requests</h5>
                        @can('create-cash-request')
                        <button class="btn btn-primary btn-sm" onclick="showModal('modalCashRequest')">
                            <i class="fa fa-plus"></i> New Request
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <livewire:CashRequests.FilterData />
                    <livewire:CashRequests.CashRequestData defer />
                </div>
            </div>
        </div>
    </div>
</div>

@include('cash_requests.modal')

@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Use delegated events so they still work after Livewire DOM updates
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let employee = $(this).data('employee');
            let branch = $(this).data('branch');
            let amount = $(this).data('amount');

            $('#modalCashRequest form').attr('action', '/cash-requests/' + id);
            $('#modalCashRequest form').append('<input type="hidden" name="_method" value="PUT" id="method_put">');
            
            $('#employee_id').val(employee);
            $('#branch_id').val(branch);
            $('#amount').val(amount);

            showModal('modalCashRequest');
        });

        // Reset form on hidden
        $('#modalCashRequest').on('hidden.bs.modal', function () {
            $('#modalCashRequest form').attr('action', '{{ route('cash-requests.store') }}');
            $('#method_put').remove();
            $('#employee_id').val('');
            $('#branch_id').val('');
            $('#amount').val('');
        });

        $(document).on('click', '.btn-approve', function() {
            let id = $(this).data('id');
            swal({
                title: "Approve Request?",
                text: "Are you sure you want to approve this cash request?",
                icon: "warning",
                buttons: true,
                dangerMode: false,
            })
            .then((willApprove) => {
                if (willApprove) {
                    $('#approve-form-' + id).submit();
                }
            });
        });

        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            swal({
                title: "Delete Request?",
                text: "Are you sure you want to delete this cash request?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $('#delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush
