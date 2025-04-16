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


    .custom-checkbox {
        appearance: none; /* Removes default styles */
        -webkit-appearance: none; /* For Safari */
        width: 16px;
        height: 16px;
        border: 1px solid #000; /* Solid border */
        border-radius: 4px; /* Optional: Adds rounded corners */
        background-color: #fff; /* Background color */
        cursor: not-allowed; /* Indicates disabled */
    }

    .custom-checkbox:checked {
        background-color: #007bff; /* Checked state background */
        border-color: #007bff; /* Optional: Change border color when checked */
    }

    .custom-checkbox:disabled {
        opacity: 0.6; /* Reduce visibility to indicate disabled */
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Notary Fee Schedule</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Notary Fee Schedule</li>
                        <li class="breadcrumb-item active">Manage Notary Fees</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary float-end" onclick="showModal()"><i class = "fa fa-plus"></i> New Notary Schedule</button>
        </div>
    </div>

    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id = "tblNotaryFee">
                    <thead class = "bg-primary">
                        <tr>
                            <th class = "text-center" width = "60">No.</th>
                            <th class = "text-center" width = "230">Min. Amount</th>
                            <th class = "text-center" width = "56"></th>
                            <th class = "text-center" width = "250">Max. Amount</th>
                            <th class = "text-center" width = "220">Notary Fee</th>
                            <th class = "text-center" width = "50">Is Percentage</th>
                            <th class = "text-center" width = "80">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        @include('notary_fee.index_data')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notary. Fee modal -->
    @include('notary_fee/modal')

@endsection
@push('js')
    <script>

        $(document).ready(function () {
            $("#tblNotaryFee").on("click", ".btnEdit", function(){

                let insFeeId = $(this).attr("data-id");

                $.ajax({
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{url('/insurance_fee/get-notaryFee-schedule')}}",
                    data: {
                        id : insFeeId
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log('response receveid!');
                        if(typeof response !== 'undefined' && response.has_error == 0){
                            //pass data to modal form
                            $("#frmNotaryFeeModal [name='id']").val(response.data.id);
                            $("#frmNotaryFeeModal [name='min_amount']").val(response.data.min_amount);
                            $("#frmNotaryFeeModal [name='max_amount']").val(response.data.max_amount);
                            $("#frmNotaryFeeModal [name='notary_fee']").val(response.data.notary_fee);
                            $("#frmNotaryFeeModal [name='is_percentage']").val(response.data.is_percentage);
                            $("#frmNotaryFeeModal [name='status']").val(response.data.status);

                            showModal();
                        }else{
                            showAlert(response);
                        }
                    }
                });
            })

            $("#modalNotaryFee .btnSubmit").click(function (e) {
                e.preventDefault();

                let form = $("#frmNotaryFeeModal")[0];

                if (form.checkValidity()) {
                    //show loading screen
                    $(".loader-wrapper").fadeIn("slow", function () {
                        $(this).show();
                    });

                    let formData = $("#frmNotaryFeeModal").serializeArray();

                    $.ajax({
                        type: "post",
                        url: "{{url('/notary_fee/submit-form')}}",
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
                                    $("#tblNotaryFee tbody").html(response.html);
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
            $("#modalNotaryFee").modal("show");

            // Set the default value of the status dropdown to '1' (Active)
            $('select[name="status"]').val(1);
        }

        function hideModal(){
            clearFormInputs();
            $("#modalNotaryFee").modal("hide");
        }

        function clearFormInputs() {
            $("#modalNotaryFee [name='id']").val("");
            $("#modalNotaryFee [name='min_amount']").val("");
            $("#modalNotaryFee [name='max_amount']").val("");
            $("#modalNotaryFee [name='notary_fee']").val("");
            $("#modalNotaryFee [name='is_percentage']").val("");
            $("#modalNotaryFee [name='status']").val("");
        }
    </script>

@endpush
