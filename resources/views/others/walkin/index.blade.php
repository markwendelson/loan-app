@extends('layouts.app')

@section('title') Walk-in Clients @endsection

@push('css')
    <style>
        .custom-bg-badge {
            background-color: #f2f7f7;
        }

        body.dark-only .custom-bg-badge {
            background-color: #22242b;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Walk-in Clients</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">Others</li>
                        <li class="breadcrumb-item active">Walk-In Transactions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Search + Buttons Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">

                <!-- Right side: Buttons grouped -->
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="btnAddWalkin"><i class="fa fa-plus me-1"></i>Add Walk-in</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card d-none d-md-block">
        <div class="card-header">
            @livewire('Walkin.FilterData')
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @livewire('Walkin.WalkinData' ,[
                    'defer' => true
                ])
            </div>
        </div>
    </div>

    {{-- Include Walk-in Modal --}}
    @include('others.walkin.modal')
@endsection

@push('js')
<script>

    $(document).ready(function(){

        //handles new walkin button click function
        $("#btnAddWalkin").on("click", function(e) {
            $("#walkinModal").modal("show");
        })

        //handles walkin modal cancel button click function
        $("#walkinModal .btnCancel").on("click", function(e) {
            e.preventDefault();

            resetForm();
        })

        //handle walkin edit button click function
        $(document).on("click", "#walkinTable tbody tr .btnEdit", function(e) {
            e.preventDefault();

            let walkinId = $(this).closest("tr").data("id");

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: `{{ url('walkin/show' ) }}/${walkinId}`,
                data: {},
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });
                    if(typeof response !== 'undefined' && response.status =='success') {
                        $("#walkinModal input[name='id']").val(response.data.id);
                        $("#walkinModal input[name='date']").val(response.data.date);
                        $("#walkinModal input[name='last_name']").val(response.data.last_name);
                        $("#walkinModal input[name='first_name']").val(response.data.first_name);
                        $("#walkinModal input[name='middle_initial']").val(response.data.middle_initial);

                        $("#walkinModal input[name='amount_withdrawn']").val(response.data.amount_withdrawn);
                        $("#walkinModal input[name='charge']").val(response.data.charge);
                        $("#walkinModal input[name='net_proceeds']").val(response.data.net_proceeds);

                        if(response.data.no_service_charge) {
                            $("#no_service_charge").prop('checked',true);
                        }else{
                            $("#no_service_charge").prop('checked',false);
                        }

                        $("#walkinModal").modal("show");

                    }else {
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
                }
            });
        });

        //handle walkin delete button click function
        $(document).on('click', "#walkinTable tbody tr .btnDelete", function(e) {
            e.preventDefault();

            let walkinId = $(this).closest("tr").data("id");

            swal({
                title: "Are you sure?",
                text: "This record will be permanently deleted and cannot be recovered.",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "btn btn-light"
                    },
                    confirm: {
                        text: "Yes, delete it",
                        className: "btn btn-danger"
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    //show loading form
                    $(".loader-wrapper").fadeIn("slow");

                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('walkin/delete') }}/${walkinId}`,
                        data: {},
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            //hide loading form
                            $(".loader-wrapper").fadeOut("slow", function () {
                                $(this).hide();
                            });

                            if(typeof response !== 'undefined' && response.status =='success') {
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
                                        Livewire.dispatch('refresh-walkin-data');
                                    }
                                });
                            }else{
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
                        }
                    });
                }
            });
        })

        $(document).on("click", "#walkinModal .btnSubmit", function(e) {
            e.preventDefault();
            let submitUrl;
            let method;

            let modal = $(this).closest(".modal-content");

            let formElement =  $(modal).find("form")[0];
            let formData = new FormData(formElement);

            let walkinId = $("#walkinModal input[name='id']").val();
            
            if(walkinId){
                method = "POST";
                submitUrl = '{{ route('walkin.update') }}';
            }else{
                method = "POST";
                submitUrl = '{{ route('walkin.store') }}';
            }

            if (!$(modal).find("#walkinForm")[0].checkValidity()) {
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
                    type: method,
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
                                    Livewire.dispatch('refresh-walkin-data');

                                    $("#walkinModal").modal("hide");

                                    resetForm();
                                    
                                    if(!walkinId) {
                                        window.open(`walkin/${response.data.id}/receipt`, '_blank');
                                    }
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

        })
    });
    
</script>
@endpush
