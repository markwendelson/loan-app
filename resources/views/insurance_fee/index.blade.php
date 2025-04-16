@extends('layouts.app')
@push('css')
<style>
        .text-right {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-left {
        text-align: left !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Insurance Fee Schedule</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Insurance Fee Schedule</li>
                        <li class="breadcrumb-item active">Manage Insurance Fees</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal()"><i class = "fa fa-plus"></i> New Insurance Sched</button>
        </div>
    </div>

    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id = "tblInsFee">
                    <thead class = "bg-primary">
                        <tr>
                            <th class = "text-center" width = "60">No.</th>
                            <th class = "text-center" width = "230">Min. Amount</th>
                            <th class = "text-center" width = "56"></th>
                            <th class = "text-center" width = "250">Max. Amount</th>
                            <th class = "text-center" width = "220">Monthly</th>
                            <th class = "text-center" width = "220">Annual</th>
                            <th class = "text-center" width = "80">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        @include('insurance_fee.index_data')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ins. Fee modal -->
    @include('insurance_fee/modal')
@endsection

@push('js')
    <script>

        $(document).ready(function () {
            $("#tblInsFee").on("click", ".btnEdit", function(){

                let insFeeId = $(this).attr("data-id");

                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{url('/insurance-fee/get-insurance-fee-schedule')}}",
                    data: {
                        id : insFeeId
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log('response receveid!');
                        if(typeof response !== 'undefined' && response.has_error == 0){
                            //pass data to modal form
                            $("#frmInsFeeModal [name='id']").val(response.data.id);
                            $("#frmInsFeeModal [name='min_amount']").val(response.data.min_amount);
                            $("#frmInsFeeModal [name='max_amount']").val(response.data.max_amount);
                            $("#frmInsFeeModal [name='monthly_amount']").val(response.data.monthly_amount);
                            $("#frmInsFeeModal [name='annual_amount']").val(response.data.annual_amount);
                            $("#frmInsFeeModal [name='status']").val(response.data.status);

                            showModal();
                        }else{
                            showAlert(response);
                        }
                    }
                });
            })

            $("#modalInsFee .btnSubmit").click(function (e) {
                e.preventDefault();

                let form = $("#frmInsFeeModal")[0];

                if (form.checkValidity()) {
                    //show loading screen
                    $(".loader-wrapper").fadeIn("slow", function () {
                        $(this).show();
                    });

                    let formData = $("#frmInsFeeModal").serializeArray();

                    $.ajax({
                        type: "post",
                        url: "{{url('/insurance-fee/submit-form')}}",
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
                                    $("#tblInsFee tbody").html(response.html);
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
            $("#modalInsFee").modal("show");

            // Set the default value of the status dropdown to '1' (Active)
            $('select[name="status"]').val(1);
        }

        function hideModal(){
            clearFormInputs();
            $("#modalInsFee").modal("hide");
        }

        function clearFormInputs() {
            $("#modalInsFee [name='id']").val("");
            $("#modalInsFee [name='min_amount']").val("");
            $("#modalInsFee [name='max_amount']").val("");
            $("#modalInsFee [name='monthly_amount']").val("");
            $("#modalInsFee [name='annual_amount']").val("");
            $("#modalInsFee [name='status']").val("");
        }
    </script>

@endpush
