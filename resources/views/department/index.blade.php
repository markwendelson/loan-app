@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Departments | Manage Departments</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Department</li>
                        <li class="breadcrumb-item active">Manage Departments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal()"><i class = "fa fa-plus"></i> New Department</button>
        </div>
    </div>

    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id = "tblDepartments">
                    <thead class = "bg-primary">
                        <tr>
                            <th class = "text-center" width = "120">No.</th>
                            <th class = "text-center" width = "150">Code</th>
                            <th>Department Name</th>
                            <th class = "text-center" width = "180">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        @include('department.index_data')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- department modal -->
    @include('department/modal')



@endsection

@push('js')
<script>

    $(document).ready(function () {
        $("#tblDepartments").on("click", ".btnEdit", function(){

            let deptId = $(this).attr("data-id");

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{url('/department/get-department')}}",
                data: {
                    id : deptId
                },
                dataType: "json",
                success: function (response) {
                    console.log('response receveid!');
                    if(typeof response !== 'undefined' && response.has_error == 0){
                        //pass data to modal form
                        $("#frmDepartmentModal [name='id']").val(response.data.id);
                        $("#frmDepartmentModal [name='code']").val(response.data.code);
                        $("#frmDepartmentModal [name='name']").val(response.data.name);
                        $("#frmDepartmentModal [name='status']").val(response.data.status);

                        showModal();
                    }else{
                        showAlert(response);
                    }
                }
            });
        })

        $("#modalDepartment .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmDepartmentModal")[0];

            if (form.checkValidity()) {
                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmDepartmentModal").serializeArray();

                $.ajax({
                    type: "post",
                    url: "{{url('/departments/submit-form')}}",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(typeof response !== 'undefined' && response.has_error == 0){
                            showAlert(response);

                            if(typeof response.html !== 'undefined' && response.html){
                                $("#tblDepartments tbody").html(response.html);
                            }
                            hideModal();
                        }else{
                            showAlert(response);
                        }
                    }
                });
            } else {
                form.reportValidity();
            }
        });
    });


    function showModal(){
        $("#modalDepartment").modal("show");
    }

    function hideModal(){
        clearFormInputs();
        $("#modalDepartment").modal("hide");
    }

    function clearFormInputs() {
        $("#modalDepartment [name='id']").val("");
        $("#modalDepartment [name='code']").val("");
        $("#modalDepartment [name='name']").val("");
        $("#modalDepartment [name='status']").val("");
    }

</script>
@endpush