@extends('layouts.app')

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
                    <h4>Job Titles | Manage Job Title</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Job Title</li>
                        <li class="breadcrumb-item active">Manage Job Title</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <button class="btn btn-primary float-end" onclick="showModal()"><i class = "fa fa-plus"></i> New Job Title</button>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table dataTable table-bordered table-hover" id = "tblJobs">
                    <thead class = "bg-default">
                        <tr>
                            <th class = "text-center" width = "120">No.</th>
                            <th class = "text-leftr" width = "150">Job Title</th>
                            <th>Description</th>
                            <th class = "text-center" width = "180">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        @include('job.index_data')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- job modal -->
    @include('job/modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const table = $('#tblJobs').DataTable();
        $("#tblJobs").on("click", ".btnEdit", function(){

        let jobId = $(this).attr("data-id");

        $.ajax({
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },
            url: "{{url('/job-titles/get-jobtitle')}}",
            data: {
                id : jobId
            },
            dataType: "json",
            success: function (response) {
                console.log('response receveid!');
                if(typeof response !== 'undefined' && response.has_error == 0){
                    //pass data to modal form
                    $("#frmJobTitleModal [name='id']").val(response.data.id);
                    $("#frmJobTitleModal [name='title']").val(response.data.title);
                    $("#frmJobTitleModal [name='description']").val(response.data.description);
                    $("#frmJobTitleModal [name='status']").val(response.data.status);

                    showModal();
                }else{
                    showAlert(response);
                }
            }
        });
        })

        $("#modalJobTitle .btnSubmit").click(function (e) {
                    e.preventDefault();

                    let form = $("#frmJobTitleModal")[0];

                    if (form.checkValidity()) {
                        //show loading screen
                        $(".loader-wrapper").fadeIn("slow", function () {
                            $(this).show();
                        });

                        let formData = $("#frmJobTitleModal").serializeArray();

                        $.ajax({
                            type: "post",
                            url: "{{url('job-titles/submit-form')}}",
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
                                        $("#tblJobs tbody").html(response.html);
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
        $("#modalJobTitle").modal("show");
    }

    function hideModal(){
        clearFormInputs();
        $("#modalJobTitle").modal("hide");
    }

    function clearFormInputs() {
        $("#modalJobTitle [name='id']").val("");
        $("#modalJobTitle [name='title']").val("");
        $("#modalJobTitle [name='description']").val("");
        $("#modalJobTitle [name='status']").val("");
    }

</script>
@endpush
