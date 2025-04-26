@extends('layouts.app')

@push('css')
<style>
    .nav-link:hover {
        background-color: #f0f0f0;
        color: #333;
        transition: background-color 0.3s ease;
    }
</style>
@endpush

@section('content')


    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client Registration </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{route('home')}}">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Client Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php $notification=session('client_notification'); ?>

    @if(isset($notification) && $notification)
        <!-- notification container -->
        @include('partials.notification')
    @endif

    <section class="content">
        <div class="container-fluid">
            <form method="post" action="{{route('client.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="project-list">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link nav-link:hover active"
                                            id="top-personal-information"
                                            data-bs-toggle="tab"
                                            href="#tab-personal-information"
                                            role="tab"
                                            aria-controls="tab-personal-information"
                                            aria-selected="true"
                                        >
                                            <i data-feather="user"></i>
                                            Personal Information
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link nav-link:hover "
                                            id="top-comaker"
                                            data-bs-toggle="tab"
                                            href="#tab-comaker"
                                            role="tab"
                                            aria-controls="tab-comaker"
                                            aria-selected="false"
                                        >
                                            <i data-feather="users"></i>
                                            Co-Maker
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            id="top-client-references"
                                            data-bs-toggle="tab"
                                            href="#tab-client-references"
                                            role="tab"
                                            aria-controls="top-client-references"
                                            aria-selected="false"
                                        >
                                            <i data-feather="credit-card"></i>
                                            Client References
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            id="top-documents"
                                            data-bs-toggle="tab"
                                            href="#tab-documents"
                                            role="tab"
                                            aria-controls="tab-documents"
                                            aria-selected="false"
                                        >
                                            <i data-feather="list"></i>
                                            Documents
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- tab content -->
                <div class="tab-content" id="clientTabContent">
                    <!-- personal information -->
                    <div class="tab-pane fade show active" id="tab-personal-information" role="tabpanel" aria-labelledby="top-personal-information">
                        @include('client.personal_information')
                    </div>

                    <!-- gaurantor -->
                    <div class="tab-pane fade show" id="tab-comaker" role="tabpanel" aria-labelledby="top-gurantor">
                        @include('client.co-maker')
                    </div>

                    <!-- payroll account -->
                    <div class="tab-pane fades how" id="tab-client-references" role="tabpanel" aria-labelledby="top-payroll-account">
                        @include('client.references')
                    </div>

                    <!-- documents -->
                    <div class="tab-pane fades how" id="tab-documents" role="tabpanel" aria-labelledby="top-documents">
                        @include('client.documents')
                    </div>
                </div>

                <!-- button container -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary float-end"><i class="fa fa-save"></i> Register Client</button>
                    </div>
                </div>

            </form>
        </div>
    </section>

    <!-- account payroll modal -->
    <div class="personal-information-modal-container">
        @include('client.modal.payroll_account_modal')
    </div>

    <!-- job information modal -->
    <div class="job-information-modal-container">
        @include('client.modal.job_information_modal')
    </div>

    @include('client.modal.co-maker_modal')

@endsection

@push('js')
    <script>
        $('#btn_img_upload').click(function() {
            $("[name='client_details[photo]']").click();
        });

        // image preview
        $("[name='client_details[photo]']").change(function(event) {
            const file=event.target.files[0];
            if (file) {
                const reader=new FileReader();
                reader.onload=function(e) {
                    $('#client_photo_preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#btn_comaker_img_upload').click(function() {
            $("[name='comaker[photo]']").click();
        });

        // image preview
        $("[name='comaker[photo]']").change(function(event) {
            const file=event.target.files[0];
            if (file) {
                const reader=new FileReader();
                reader.onload=function(e) {
                    $('#comaker_photo_preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        var payrollCount=1;
        var jobInformationCount=1;
        var referenceCount=1;
        var payrollAccountStatus={!! json_encode(config('constants.status_value')) !!};

        $("[name='client_details[present_province]'], [name='client_details[permanent_province]'], [name='comaker[present_province]'], [name='comaker[permanent_province]']").change(function(){
            let params={
                province_id : $(this).val()
            };

            let targetData=$(this).attr("data-target");

            getCities(params, function(response){
                if(typeof response !== 'undefined' && response.has_error == 0){
                    if(response.html){
                        $("#" + targetData).empty();
                        $("#" + targetData).append("<option value='' selected hidden disabled>-- Select --</option>")
                        $("#" + targetData).append(response.html);
                    }

                }
            });
        })

        function getCities(params, callBack){
            //show loading
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            $.ajax({
                type: "post",
                url: "{{url('cities/get-cities')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: params,
                dataType: "json",
                success: function (response) {
                    //hide loading
                    $(".loader-wrapper").fadeIn("slow", function () {
                        $(this).hide();
                    });
                    callBack(response);
                }
            });
        }

        //job information function  --------------------------------------------------------------------------------------------------------
        $(".btnJobInformation").on("click", function(){
            let targetField=$(this).attr("data-target");

            $("#modalJobInformation #jobInformationTarget").val(targetField);

            showJobInformationModal();
        })

        $("#modalJobInformation .btnCancel").on("click", function(){
            hideJobInformationModal();
        })

        function showJobInformationModal(){
            $("#modalJobInformation").modal("show");
        }

        function hideJobInformationModal() {

            clearJobInformationModal();
            $("#modalJobInformation").modal("hide");
        }

        function clearJobInformationModal() {
            $("#modalJobInformation #jobInformationTarget").val("");
            $("#modalJobInformation #office_name").val("");
            $("#modalJobInformation #salary").val("");
            $("#modalJobInformation #job_id").val("");
            $("#modalJobInformation #status").val("");
        }

        $("#modalJobInformation .btnAdd").on("click", function(){
            let params={
                targetField : $("#modalJobInformation #jobInformationTarget").val(),
                office_name : $("#modalJobInformation #office_name").val(),
                salary : $("#modalJobInformation #salary").val(),
                job_id : $("#modalJobInformation #job_id").val(),
                job_title : $("#modalJobInformation #job_id option:selected").text(),
                status : $("#modalJobInformation #status").val()
            };

            console.log(params);

            addJobInformation(params);
        })

        function addJobInformation(params){
            let field='client_job_information'; //default value

            if(params.targetField == '.comaker-job-informations') {
                field='comaker_job_title';
            }

            let card= "<div class='col-sm-12 col-md-4 col-lg-3 client-job-information-items'> "+
                    "<div class='card'>"+
                        "<input type='hidden' name='"+ field +"["+ jobInformationCount +"][job_id]' value='"+ params.job_id +"'>" +
                        "<input type='hidden' name='"+ field +"["+ jobInformationCount +"][office_name]' value='"+ params.office_name +"'>" +
                        "<input type='hidden' name='"+ field +"["+ jobInformationCount +"][salary]' value='"+ params.salary +"'>" +
                        "<input type='hidden' name='"+ field +"["+ jobInformationCount +"][status]' value='"+ params.status +"'>" +
                        "<div class='card-body'>" +
                            "<button type='button' class='btn btn-danger btn-xs float-end'><i class='fa fa-times'></i></button>" +
                            "<h5>"+ params.job_title +"</h5>" +
                            "<p class='mb-0'>Office: "+ params.office_name +"</p>" +
                            "<p class='mb-0'>Salary: "+ params.salary +"</p>" +
                        "</div>" +
                        "<div class='card-footer'>" +
                            "<p>Active</p>"+
                        "</div>" +
                    "</div>" +
                "</div>";

            $(card).hide().appendTo(params.targetField).fadeIn(300);

            jobInformationCount++;

            hideJobInformationModal();
        }

        // payroll account function -----------------------------------------------------------------------------------------------------------------------------

        function addPayrollAccount(params){
            let field='client_payroll_account'; //default field

            if(params.targetField == '.comaker-payroll-accounts'){
                field='comaker_payroll_account';
            }

            let card="<div class='col-sm-12 col-md-4 col-lg-3 client-payroll-items'>" +
                    "<div class='card'>" +
                        "<div class='card-body'>" +
                            "<input type='hidden' name='"+ field +"["+ payrollCount +"][bank_id]' value='"+ params.bankId +"'>" +
                            "<input type='hidden' name='"+ field +"["+ payrollCount +"][account_no]' value='"+ params.accountNo +"'>" +
                            "<input type='hidden' name='"+ field +"["+ payrollCount +"][account_name]' value='"+ params.accountName +"'>" +
                            "<input type='hidden' name='"+ field +"["+ payrollCount +"][is_active]' value='"+ params.is_active +"'>" +

                            "<button type='button' class='btn btn-danger btn-xs float-end btnRemovePayrollAccount'><i class='fa fa-times'></i></button>" +
                            "<h5><strong>"+ params.bankName +"</strong></h5>" +
                            "<p class='mb-0'>Account No: " + params.accountNo + "</p>" +
                            "<p>Account Name: "+ params.accountName +"</p>" +
                        "</div>" +
                        "<div class= 'card-footer'>" +
                            "<p>"+ payrollAccountStatus[params.status] +"</p>" +
                        "</div>" +
                    "</div>" +
                "</div>";

            $(card).hide().appendTo(params.targetField).fadeIn(300);

            payrollCount++;

            hidePayrollAccountModal();
        }

        $(".btnPayrollAccount").on("click", function(){
            let targetField=$(this).attr("data-target");

            $("#modalPayrollAccount #payrollModalTarget").val(targetField);

            showPayrollAccountModal();
        })

        $("#modalPayrollAccount .btnAdd").on("click", function(){

            let params={
                'targetField' : $("#modalPayrollAccount #payrollModalTarget").val(),
                'bankId' : $("#modalPayrollAccount #bank_id").val(),
                'bankName' : $("#modalPayrollAccount #bank_id option:selected").text(),
                'accountNo' : $("#modalPayrollAccount #account_no").val(),
                'accountName' : $("#modalPayrollAccount #account_name").val(),
                'status' : $("#modalPayrollAccount #status").val()
            };

            addPayrollAccount(params);
        })

        $("#modalPayrollAccount .btnCancel").on("click", function(){
            hidePayrollAccountModal();
        })

        $(document).on("click", ".btnRemovePayrollAccount", function() {
            $(this).closest('.client-payroll-items').fadeOut(300, function() {
                $(this).remove();
            });
        });

        function showPayrollAccountModal() {
            $("#modalPayrollAccount").modal("show");
        }

        function hidePayrollAccountModal() {
            clearPayrollAccountModal();
            $("#modalPayrollAccount").modal("hide");
        }

        function clearPayrollAccountModal() {
            $("#modalPayrollAccount #payrollModalTarget").val(""),
            $("#modalPayrollAccount #bank_id").val(""),
            $("#modalPayrollAccount #account_no").val(""),
            $("#modalPayrollAccount #account_name").val(""),
            $("#modalPayrollAccount #status").val("")
        }
    </script>
@endpush

