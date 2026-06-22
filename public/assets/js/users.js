$(function () {
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
                        Livewire.dispatch('user-created');

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

    //handle reset password click function
    $("#tblUsers tbody").on("click", ".btnResetPassword", function (e) {
        e.preventDefault();

        $("#modalResetPassword input[name='id']").val($(this).closest("tr").data("id"));

        $("#modalResetPassword").modal("show");
    });

    //handles submit function for reset password modal
    $("#modalResetPassword").on("click", ".btnSubmit", function(e) {
        e.preventDefault();

        let formData = $("#modalResetPassword form").serializeArray();
        let form = $("#modalResetPassword form")[0];

        if(form.checkValidity()) {
            //show loading form
            $(".loader-wrapper").fadeIn("slow");

            //form is valid, proceed with ajax submission
            $.ajax({
                type: 'PUT',
                url: 'user/reset-password',
                data: formData,
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {

                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });
                    

                    if(typeof response !== 'undefined' && response.status == 'success') {
                        showToast.success(response.message);

                        //close this modal
                        $("#modalResetPassword").modal("hide");
                    }else{
                        showToast.error(response.message);
                    }
                }
            });
        }else{
            form.reportValidity();
            return false;
        }
    });

    //handle toggle visibility for password button
    $(".btnTogglePassword").on("click", function(e) {
        e.preventDefault();;

        let input = $(this).closest(".input-group").find("input");

        let isPassword = input.prop("type") === "password";

        input.prop("type", isPassword ? "text" : "password");

        $(this).find('i').toggleClass("fa-eye fa-eye-slash");
    });


});

function deleteData(id) {
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(function (willDelete) {
        if (!willDelete) return;

        $.ajax({
            url: '/user/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showToast.success(response.message);
                $('#tblUser').DataTable().ajax.reload();
            }
        });
    });
}