<div class="card" id="roles">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Branch and Roles</h5>
        <button class="btn btn-primary float-end" onclick="showModal('modalAssignBranchRole')"><i class="fa fa-plus"></i></button>
    </div>
    <div class ="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id = "tblBranchRoles">
                <thead class = "bg-default">
                    <tr>
                        <th>Branch</th>
                        <th class="text-center">Role</th>
                        <th width="30%" class="text-center">Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($branch_roles as $branch_role)
                            @if(!empty($branch_role['roles']))
                            <tr>
                                <td>{{ $branch_role['branch'] }}</td>
                                <td class="text-center">{{ $branch_role['roles'] }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm btn-view-permissions"
                                            data-user-id="{{ $branch_role['user_id'] }}"
                                            data-branch-id="{{ $branch_role['branch_id'] }}"
                                            title="View Permissions">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm btn-edit-role"
                                            data-user-id="{{ $branch_role['user_id'] }}"
                                            data-branch-id="{{ $branch_role['branch_id'] }}"
                                            data-role-name="{{ $branch_role['roles'] }}"
                                            title="Edit Role">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete-role"
                                            data-user-id="{{ $branch_role['user_id'] }}"
                                            data-branch-id="{{ $branch_role['branch_id'] }}"
                                            title="Delete Role">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </thead>
            </table>
        </div>
    </div>
</div>

@include('user.modal-assign-role')

@push('js')
<script>
    $(function() {
        $("#modalAssignBranchRole .btnSubmit").click(function (e) {
            e.preventDefault();

            let form=$("#frmAssignBranchRole")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData=$("#frmAssignBranchRole").serializeArray();

                const _url = $('#frmAssignBranchRole').attr('action');

                $.ajax({
                    method: 'POST',
                    url: _url,
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if(response.status == 'success'){
                            hideModal('modalAssignBranchRole');
                            showToast.success(response.message)
                            $('#roles').load(location.href + ' #roles');
                            $('#permissions').load(location.href + ' #permissions')
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

        $(document).on('click', '.btn-delete-role', function () {
            var userId = $(this).data('user-id')
            var branchId = $(this).data('branch-id')
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((result) => {
                if (result) {

                    $.ajax({
                        url: '/user/'+userId+'/branch/'+branchId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showToast.success(response.message)
                            $('#roles').load(location.href + ' #roles');
                            $('#permissions').load(location.href + ' #permissions')
                        },
                        error: function(error) {
                            let response = error.responseJSON;

                            if (response?.errors) {
                                let messages = Object.values(response.errors).flat().join('<br>');
                                showToast.error(messages);
                            } else if (response?.message) {
                                showToast.error(response.message);
                            }
                        },
                    });

                }
            })
        })

        $(document).on('click', '.btn-edit-role', function () {
            var userId = $(this).data('user-id')
            var branchId = $(this).data('branch-id')
            var role = $(this).data('role-name')
            console.log(userId, branchId, role)
            showModal('modalAssignBranchRole')
            let selectedId = null;

            $("#frmAssignBranchRole [name='role_id'] option").each(function () {
                if ($(this).text().trim() === role.trim()) {
                    selectedId = $(this).val();
                }
            });

            $("#frmAssignBranchRole [name='branch_id']").val(branchId);
            $("#frmAssignBranchRole [name='role_id']").val(selectedId);
        })
    })
</script>
@endpush