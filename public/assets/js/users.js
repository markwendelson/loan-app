$(function () {
    $('#tblUser').DataTable({
        ordering: false,
        processing: true,
        serverSide: true,
        stateSave: true,
        searching: true,
        ajax: {
            url: userEndpoint,
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'status', name: 'status', render: function(data, type, row) {
                if (data == 1) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-danger">Inactive</span>';
                }
            } },
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
            { className: "text-center", targets: [3,4,5] },
        ]
    });

    $("#tblUser").on("click", ".btnEdit", function(){
        let id=$(this).attr("data-id");
        window.location.href = `/user/${id}/edit`;
    })

    $("#modalUser .btnSubmit").click(function (e) {
        e.preventDefault();

        let form=$("#frmUserModal")[0];

        if (!form.checkValidity()) {
            form.reportValidity();
        } else {

            //show loading screen
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            let formData=$("#frmUserModal").serializeArray();

            const _url = $('#frmUserModal').attr('action');

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
                        hideModal('modalUser');
                        showToast.success(response.message)
                        $('#tblUser').DataTable().ajax.reload();
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

                    if(response.responseJSON.id) {
                        swal({
                            title: 'View User?',
                            text: "Do you want to view user?",
                            icon: 'warning',
                            buttons: true,
                            dangerMode: true,
                        }).then((result) => {
                            if (result) {
                                window.location = '/users/'+response.responseJSON.id+'/edit'
                            }
                        })
                    }
                }
            });
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
                url: '/user/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showToast.success(response.message)
                    $('#tblUser').DataTable().ajax.reload();
                }
            });

        }
    })
}