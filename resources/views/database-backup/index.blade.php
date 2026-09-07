@extends('layouts.app')

@section('title') Database Backup @endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Database Backup</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item active">Settings</li>
                        <li class="breadcrumb-item">Database Backup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body d-flex justify-content-end align-items-center">
            @can('create-database-backup')
            <button type="button" class="btn btn-primary btnGenerateBackup"><i class="fa fa-plus me-1"></i>Generate Backup</button>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <livewire:Database.DatabaseFilter />
        </div>
        <div class="card-body p-0">
            <livewire:Database.DatabaseData defer />
        </div>
    </div>
@endsection

@push('js')
    <script>
        //handle button generate backup function
        $(".btnGenerateBackup").click(function(){

            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type:"GET",
                url: "{{route('database.create')}}",
                data: [],
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(typeof response !== 'undefined' && response.status == 'success') {
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
                            }
                        }).then(function(value) {
                            if (value) {
                                Livewire.dispatch('refresh-database-data');
                            }
                        });
                    }
                }
            });
        })

        //handle restore backup button
        $(document).on('click', '.btnRestore', function(e) {
            e.preventDefault();
            var filename = $(this).data('filename');

            swal({
                title: 'Are you sure?',
                text: 'This will overwrite the current database with the backup: ' + filename + '. This action cannot be undone!',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: null,
                        visible: true,
                        className: "btn btn-secondary",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Restore",
                        value: true,
                        visible: true,
                        className: "btn btn-danger",
                        closeModal: true
                    }
                },
                dangerMode: true,
            }).then(function(value) {
                if (value) {
                    // Show loading
                    $(".loader-wrapper").fadeIn("slow");

                    $.ajax({
                        type: "POST",
                        url: "{{ url('database/restore') }}/" + filename,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".loader-wrapper").fadeOut("slow", function () {
                                $(this).hide();
                            });

                            if(response.status == 'success') {
                                swal({
                                    title: 'Restored!',
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
                                    }
                                });
                            } else {
                                swal({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    buttons: {
                                        confirm: {
                                            text: "OK",
                                            value: true,
                                            visible: true,
                                            className: "btn btn-primary",
                                            closeModal: true
                                        }
                                    }
                                });
                            }
                        },
                        error: function () {
                            $(".loader-wrapper").fadeOut("slow", function () {
                                $(this).hide();
                            });

                            swal({
                                title: 'Error!',
                                text: 'An error occurred while restoring the database.',
                                icon: 'error',
                                buttons: {
                                    confirm: {
                                        text: "OK",
                                        value: true,
                                        visible: true,
                                        className: "btn btn-primary",
                                        closeModal: true
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });

    </script>
@endpush