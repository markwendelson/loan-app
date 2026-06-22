@extends('layouts.app')

@section('title') Roles & Permissions @endsection

@push('css')
<style>
    .bg-default {
        background-color: #1f807f;
    }
    .bg-default th {
        color: white; /* Set white text for <th> elements */
        border: 1px solid white;
    }
    .custom-input .form-check .form-check-input:valid ~ .form-check-label {
        color: #000000;
    }
    body.dark-only .custom-input .form-check .form-check-input:valid ~ .form-check-label {
        color:rgba(255, 255, 255, 0.6) !important;
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
                        <li class="breadcrumb-item">Administration</li>
                        <li class="breadcrumb-item">Users</li>
                        <li class="breadcrumb-item active">Roles & Permissions</li>
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
        <div class="card-header">
            <form action="{{ url()->current() }}" method="GET">
                <div class="d-flex justify-content-between gap-2">
                    <div class="d-flex gap-2 align-items-center">
                        <span>Show</span>
                        <select name="entries" class="form-select">
                            <option value="10" {{ isset($filterData['entries']) && $filterData['entries'] == 10 ? 'selected' : ''; }}>10</option>
                            <option value="25" {{ isset($filterData['entries']) && $filterData['entries'] == 25 ? 'selected' : ''; }}>25</option>
                            <option value="50" {{ isset($filterData['entries']) && $filterData['entries'] == 50 ? 'selected' : ''; }}>50</option>
                            <option value="100" {{ isset($filterData['entries']) && $filterData['entries'] == 100 ? 'selected' : ''; }}>100</option>
                        </select>
                    </div>
                    <!-- branch select -->
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <input type="search"
                            class="form-control"
                            placeholder="Search roles..."
                            value="{{ $filterData['q'] ?? ''; }}"
                            name="q"
                        >
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tblRole" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center"  width="50"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($roles) && $roles)
                            @foreach($roles as $key => $val)
                                <tr data-id="{{ $val->id }}">
                                    <td>{{ $val->name }}</td>
                                    <td class="d-flex justify-content-start gap-1">
                                        @can('edit-role')
                                        <button type="button" class="btn btn-primary btn-sm btnEdit" data-id="{{ $val->id }}"><i class="fa fa-pencil"></i></button>
                                        @endcan
                                        @can('delete-role')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteData({{ $val->id }})"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- pagination -->
    <div class="d-flex justify-content-end">
        {{ $roles->appends(request()->input())->onEachSide(0)->links('partials.pagination.default') }}
    </div>

    @include('role.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {
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

                checkGroupCheckbox();
                checkAllGroupCheckbox();

                // Show the modal
                showModal('modalRole');
            }).catch((error) => {
                showToast.error("Failed to fetch role data.");
            });
        })

        //handle checkAll functionality
        $(document).on("change", ".chckAll", function(e) {
            e.preventDefault();

            if($(this).is(":checked")) {
                $(".chck-permissions input[type='checkbox']").prop("checked", true);
            }else{
                $(".chck-permissions input[type='checkbox']").prop("checked", false);
            }
        });

        //handle checkAllGroup functionality
        $(document).on("change", ".chckAllGroup", function(e) {
            e.preventDefault();

            if($(this).is(":checked")) {
                $(this).closest(".group-permissions").find("input[type='checkbox']").prop("checked", true);
            }else{
                $(this).closest(".group-permissions").find("input[type='checkbox']").prop("checked", false);   
            }

            checkAllGroupCheckbox(this);
        });

        $(document).on("change", ".subCheckBox", function () {
            checkGroupCheckbox(this);
        });


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

    function checkGroupCheckbox(el = null) {
        if (el) {

            let card = $(el).closest(".card");

            let total = card.find(".subCheckBox").length;
            let checked = card.find(".subCheckBox:checked").length;

            let all = card.find(".chckAllGroup");

            all.prop("checked", checked === total);
            all.prop("indeterminate", checked > 0 && checked < total);

            checkAllGroupCheckbox();

            return;
        }

        $(".card").each(function () {

            let card = $(this);

            let total = card.find(".subCheckBox").length;
            let checked = card.find(".subCheckBox:checked").length;

            let all = card.find(".chckAllGroup");

            all.prop("checked", checked === total);
            all.prop("indeterminate", checked > 0 && checked < total);
        });
    }

    function checkAllGroupCheckbox(el = null) {
        let all = $(".chckAll");
        let isAllChecked = true;

        if (el) {
            $(".chckAllGroup").each(function () {
                if (!$(this).is(":checked")) {
                    isAllChecked = false;
                }
            });

            if (isAllChecked) {
                all.prop("checked", true);
            } else {
                all.prop("checked", false);
            }

            all.prop("indeterminate", false);

            return;
        }

        $(".chckAllGroup").each(function () {
            console.log("checking");
            if (!$(this).is(":checked")) {
                isAllChecked = false;
            }
        });

        if (isAllChecked) {
            all.prop("checked", true);
        } else {
            all.prop("checked", false);
        }

        all.prop("indeterminate", false);

        return;
    }

</script>
@endpush
