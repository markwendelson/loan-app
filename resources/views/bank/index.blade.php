@extends('layouts.app')

@section('title') Banks @endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Banks</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item">Libraries</li>
                        <li class="breadcrumb-item active">Banks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!--button container -->
    <div class="card">
        <div class="card-body">
            <button type="button" class="btn btn-primary btnNewBank btn-sm float-end"><i class="fa fa-plus me-1"></i> New Bank</button>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <livewire:Bank.FilterData />
        </div>
        <div class="card-body p-0">
            <livewire:Bank.BankData defer />
        </div>
    </div>

    <!-- modal here -->
    @include('bank.modal.modal')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            //handle show new bank modal
            $(".btnNewBank").on("click", function(e) {
                e.preventDefault();

                $("#bankModal .modal-title").text("New Bank");

                $("#bankModal").modal("show");
            });

            //handle edit button function
            $(document).on("click", "#bankTable .btnEdit", function(e) {
                e.preventDefault();

                let bankId = $(this).closest("tr").data("id");

                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: "GET",
                    url: `{{ url('bank/get-bank-data') }}/${bankId}`,
                    data: [],
                    dataType: "json",
                    success: function (response) {

                        //hide loading form
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(typeof response !== 'undefined' && response.status == 'success') {
                            $("#bankModal .modal-title").text("Update Bank");

                            $("#bankModal form input[name='id']").val(response.data.id);
                            $("#bankModal form input[name='bank_code']").val(response.data.bank_code);
                            $("#bankModal form input[name='name']").val(response.data.name);
                            $("#bankModal form select[name='status']").val(response.data.status);

                            $("#bankModal").modal("show");
                        }else{
                            swal({
                                title: 'Error',
                                text: response.message,
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
                        }
                    }
                });
            });

            //handle delet button function
            $(document).on("click", "#bankTable .btnDelete", function(e) {
                e.preventDefault();
                
                let bankId = $(this).closest("tr").data('id');

                swal({
                    title: 'Are you sure?',
                    text: "This action cannot be undone. All associated data will be permanently removed.",
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(function (willDelete) {
                    if (!willDelete) return;

                    $.ajax({
                        url: `{{ url('bank/delete') }}/${bankId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            swal({
                                title: 'Success!',
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
                                dangerMode: true,
                            });
                            
                            Livewire.dispatch("refresh-bank-data");
                        }
                    });
                });
            });

            //handle modal submit button function
            $(document).on("click", "#bankModal .btnSubmit", function(e) {
                e.preventDefault();
                let submitUrl = `{{ route('bank.store') }}`;

                let modal = $(this).closest(".modal-content");
                
                let formElement =  $(modal).find("form")[0];
                let formData = new FormData(formElement);

                //check type of function
                let bankId = $(`input[name='id']`).val();
                if(bankId){
                    formData.append('_method', 'PUT');
                    submitUrl = `{{ route('bank.put') }}`;
                }

                // return alert(submitUrl);
                if (!$(modal).find("#frmBanks")[0].checkValidity()) {
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
                        type: 'POST',
                        url: submitUrl,
                        data: formData,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            //hide loading form
                            $(".loader-wrapper").fadeOut("slow", function () {
                                $(this).hide();
                            });

                            if(response.status !== 'undifined' && response.status == 'success') {
                                swal({
                                    title: 'Success!',
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
                                }).then((willClose) => {
                                    if (willClose) {
                                        Livewire.dispatch('refresh-bank-data');

                                        $("#bankModal").modal("hide");
                                    }
                                });
                            }else{
                                swal({
                                    title: 'Error',
                                    text: response.message,
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
                            }
                        },
                        error: function(xhr, status, error) {
                            swal({
                                title: 'Error',
                                text: error,
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
                        }
                    });
                }

            });
        });
    </script>
@endpush