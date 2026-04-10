@extends('layouts.app')

@section('title') Manage Role @endsection

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
                    <h4>Role | Manage Role</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Role</li>
                        <li class="breadcrumb-item active">Manage Role</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal('modalRole')"><i class="fa fa-plus"></i> New Role</button>
        </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblRole">
                    <thead class="bg-default">
                        <tr>
                            <th class="text-center" width="150">Name</th>
                            <th class="text-center" width="150"><i class="fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('role.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const canEdit = {{ auth()->user()->can('edit-role') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-role') ? 1 : 0 }};
        
        const table=$('#tblRole').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('role.index') }}",
            },
            columns: [
                { data: 'name', name: 'name' },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEdit) {
                            buttons += `<button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                        }
                        if (canDelete) {
                            buttons += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                        }
                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [1] },
            ]
        });

        $("#modalRole .btnSubmit").click(function (e) {
            e.preventDefault();

            let form=$("#frmRoleModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData=$("#frmRoleModal").serializeArray();

                var id=$("#modalRole [name='id']").val();
                if(id) {
                    _method='PUT';
                    _url=`{{route('role.update', ':id')}}`.replace(':id',id);
                } else {
                    _method='POST';
                    _url=`{{route('role.store')}}`;
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
                            hideModal('modalRole');
                            showToast.success(response.message)
                            table.ajax.reload();
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

        $("#tblRole").on("click", ".btnEdit", function(){
            let id=$(this).attr("data-id");

            fetchRoleData(id).then((data) => {
                // Populate the form fields with the fetched data
                $("#modalRole [name='id']").val(data.id);
                $("#modalRole [name='name']").val(data.name);

                // Uncheck all permissions first
                $("#modalRole input[name='permissions[]']").prop('checked', false);

                // Check the permissions that the role has
                data.permissions.forEach(function(permission) {
                    $("#modalRole input[name='permissions[]'][id='" + permission.name + "']").prop('checked', true);
                });

                // Show the modal
                showModal('modalRole');
            }).catch((error) => {
                showToast.error("Failed to fetch role data.");
            });
        })


    });

    async function fetchRoleData(id) {
        let response = await fetch(`/role/${id}`, {
            method: "GET",
        });

        if (!response.ok) throw new Error("Network response was not ok");

        let data = await response.json();
        return data;
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
                    url: '/role/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblRole').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

</script>
@endpush
