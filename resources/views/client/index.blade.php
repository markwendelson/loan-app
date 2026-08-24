@extends('layouts.app')

@section("title") Manage Client @endsection

@push('css')
    <style>
        .bg-default {
            background-color: #1f807f;
        }
        .bg-default th {
            color: white; /* Set white text for <th> elements */
            border: 1px solid white;
        }
        .dropdown-menu .dropdown-item {
            opacity: 0.95 !important;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client | Manage Client</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">Clients</li>
                        <li class="breadcrumb-item active">Manage Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body d-flex justify-content-end gap-2 align-items-center flex-wrap">
            @can('view-color-coding')
            <a href="#" class="btn btn-light btnColor"><i class="fa fa-paint-brush me-1"></i>Color Coding</a>
            @endcan
            <a href="{{route('client.registration')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Client</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <livewire:Client.ClientList.FilterData />
        </div>
        <div class="card-body p-0">
            <livewire:Client.ClientList.TableData defer />
        </div>
    </div>

    <!-- job modal -->
    @include('insurance-fee-schedule.modal')

    <!-- color coding modal -->
    @include('client.modal.color_coding')

    <!-- payroll / atm modal -->
    @include('client.modal.payroll_atm')
@endsection

@push('js')

<script>
    $(document).ready(function () {
        const canDelete = {{ auth()->user()->can('delete-client') ? 1 : 0 }};

        //handle show color coding modal
        $(".btnColor").click(function(e) {
            e.preventDefault();

            $("#modalColor").modal("show");
        })

        //handle modal color cancel button
        $("#modalColor .btnCancel").on("click", function(e) {
            e.preventDefault();

            $("#modalColor").modal("hide");
        })

        //handle color modal add color code button
        $(document).on("click", "#modalColor .btnAddColorCode", function(e) {
            e.preventDefault();

            let formElement =  $("#modalColor").find("form")[0];
            let formData = new FormData(formElement);

            if (!$("#modalColor").find("#frmColor")[0].checkValidity()) {
                swal({
                    title: 'Error',
                    text: 'Please fill all required fields',
                    icon: 'warning',
                    buttons: {
                        confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "btn btn-primary",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                });
            }else{
                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: "POST",
                    url: "{{route('client.create-color-coding')}}",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false, 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        //hide loading form
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(typeof response !== 'undefeined' && response.status == 'success') {
                            swal({
                                title: 'Success',
                                text: response.message,
                                icon: 'success',
                                buttons: {
                                    confirm: {
                                        text: "OK",
                                        value: true,
                                        visible: true,
                                        className: "btn btn-primary",
                                        closeModal: true
                                    }
                                },
                            }).then((value) => {
                                resetColorForm();
                                Livewire.dispatch('refresh-color-code');
                                Livewire.dispatch('refresh-client-list');
                            });
                        }
                    }
                });
            }

            
        })

        //handle modal color edit button
        $(document).on("click", "#modalColor .btnEdit", function(e) {
            e.preventDefault();

            let colorCodeId = $(this).data("id");

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: `{{url('client/color-code/get-data')}}/${colorCodeId}`,
                data: [],
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response !== 'undefined' && response.status == 'success') {
                        $("#modalColor input[name='id']").val(response.data.id);
                        $("#modalColor input[name='color_name']").val(response.data.color_name);
                        $("#modalColor select[name='status']").val(response.data.status);
                        $("#modalColor input[name='color_code']").val(response.data.color_code);
                        $("#modalColor textarea[name='color_description']").val(response.data.color_description);
                    }
                }
            });
        })

        //handle payroll atm button click function
        $(document).on("click", ".btnPayrollAtm", function(e) {
            e.preventDefault();

            let clientId = $(this).closest("tr").data("id");

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: `{{ url('client/payroll-accounts') }}/${clientId}`,
                data: {},
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response !== 'undefined' && response.status == 'success') {

                        populatePayrollAccountModal(response.data);
                        $("#modalPayrollATM").modal("show");
                    }
                }
            });
        })

        //handle payroll modal cancel button 
        $("#modalPayrollATM .btnCancel").click(function(e) {
            e.preventDefault();

            $("#modalPayrollATM").modal("hide");
        })

        //handle switchToggle function
        $(document).on("change", ".swtchBorrowed", function() {

            const $inputForm = $(this)
                .closest(".card-body")
                .find(".inputForm");

            if (this.checked) {
                $inputForm.removeClass("d-none");
            } else {
                $inputForm.addClass("d-none");
            }
        });
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
                    url: '/client/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.status == 'success') {
                            showToast.success(response.message)
                            location.reload();
                        }
                        if(response.status == 'error') {
                            showToast.error(response.message)
                        }
                    }
                });

            }
        })
    }

    function removeFromGroup(id) {
        swal({
            title: 'Confirm Action',
            text: "Are you sure you want to remove this client from the group?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: '/client-group/remove-grouping/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.status == 'success') {
                            showToast.success(response.message)

                            location.reload();
                        }
                        if(response.status == 'error') {
                            showToast.error(response.message)
                        }
                    }
                });
            }
        })
    }

    function resetColorForm(){
        $("#modalColor #frmColor")[0].reset();
    }

    function populatePayrollAccountModal(data) {
        let el = '';
        data.forEach(function(val, index) {
            let border = '';

            if (val.payroll_account_borrowings !== null) {
                border = `border border-warning`;
                let primary = val.is_primary ? '<span class="badge badge-info">Primary</span>' : '';
                let isChecked = val.payroll_account_borrowings !== null ? 'checked' : '';
                el += `
                    <div class="card">
                        <div class="card-body ${border}">
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <span class="fw-semibold">${val.bank_name}</span>
                                <span class="badge badge-light text-dark">ATM Card</span>
                                ${primary}
                            </div>
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <span>${val.account_no}</span>
                                <span>- ${val.account_name}</span>
                            </div>
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-semibold txt-dark">Borrowed by another branch</span>
                                            <p class="txt-dark">New loans are blocked while primary account is borrowed.</p>
                                        </div>
                                        <div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input swtchBorrowed" type="checkbox" role="switch" ${isChecked} value="1">
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="inputForm">
                                        <form>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label class="w-100 txt-dark">
                                                        Borrowing Branch
                                                        <select class="form-select">
                                                        </select>
                                                    </label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label class="w-100 txt-dark">
                                                        Borrowed By
                                                        <select class="form-select"></select>
                                                    </label>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <label class="w-100 txt-dark">
                                                        Borrowed At
                                                        <input type="date"
                                                            class="form-control"
                                                        >
                                                    </label>
                                                </div>

                                                <div class="col-lg-6">
                                                    <label class="w-100 txt-dark">
                                                        Expected Return At
                                                        <input type="date"
                                                            class="form-control"
                                                        >
                                                    </label>
                                                </div>

                                                <div class="col-12">
                                                    <label class="w-100 txt-dark">
                                                        Borrower Remarks
                                                        <textarea class="form-control"></textarea>
                                                    </label>
                                                </div>
                                            </div>  
                                        </form>

                                        <div>
                                            <button type="button" class="btn btn-light float-end"><i class="fa fa-reply me-1"></i>Mark as Returned</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            
                        </div>
                    </div>

                `;
            }else{

            }
        });

        $("#modalPayrollATM .modal-body").html(el);
    }

    </script>

@endpush
