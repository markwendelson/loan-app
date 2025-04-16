@extends('layouts.app')

@push('css')

    <style>
        .thumbnail-size {
            max-width: 250px;
            max-height: 250px;
            object-fit: cover;
        }
        .checkbox-wrapper {
            display: inline-block;
            padding: 5px;
            border: 2px solid #000; /* Border around the checkbox */
            border-radius: 5px;
        }

        .nav-link:hover {
            background-color: #f0f0f0;
            color: #333;
            transition: background-color 0.3s ease;
        }
        .employee-thumbnail {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        .employee-card {
            width: 250px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            padding: 10px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Light shadow effect */
            transition: box-shadow 0.3s ease-in-out; /* Optional: Add smooth shadow animation */
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Slightly larger shadow on hover */
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Employee Registration </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{route('employee.index')}}" title="Go to Employee List">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Employee Register</li>
                        {{-- <li class="breadcrumb-item active">Project List</li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>

  <!-- action = "{{url()->current()}}"  -->
    <section class = "content">
        <div class = "container-fluid">
            <form id="employeeForm" method = "POST" action="{{ isset($employee) ? route('employee.update', $employee['id']) : route('employee.register') }}" enctype="multipart/form-data" onsubmit="validateForm(event)">
        @csrf

        @if(isset($employee))
             @method('POST') <!-- This is for updating an existing employee -->
        @endif

            <div class = "project-list">
                <div class = "card">
                    <div class = "card-body">
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
                                        Employee Information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link nav-link:hover "
                                        id="top-family-information"
                                        data-bs-toggle="tab"
                                        href="#tab-family-information"
                                        role="tab"
                                        aria-controls="tab-family-information"
                                        aria-selected="false"
                                    >
                                        <i data-feather="users"></i>
                                        Family Information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        id="top-Emp-Identification"
                                        data-bs-toggle="tab"
                                        href="#tab-Emp-Identification"
                                        role="tab"
                                        aria-controls="tab-Emp-Identification"
                                        aria-selected="false"
                                    >
                                        <i data-feather="credit-card"></i>
                                        Employee Identifications
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- tab content -->
            <div class="tab-content" id="employeeTabContent">
                <!-- personal information -->
                <div class="tab-pane fade show active" id="tab-personal-information" role="tabpanel" aria-labelledby="top-personal-information">
                    <!-- Include personal information view -->
                    @include('employee.register.personal_information', ['branch_data' => $branch_data])
                </div>

                <!-- Emp Family -->
                <div class="tab-pane fade show" id="tab-family-information" role="tabpanel" aria-labelledby="top-family-information">
                    <!-- Include Family view , ['family_data' => $family_data])  -->
                    @include('employee.register.family_information')
                </div>

                <!-- Emp IDs -->
                <div class="tab-pane fade show" id="tab-Emp-Identification" role="tabpanel" aria-labelledby="tab-Emp-Identification">
                    <!-- Include IDs-->
                     @include('employee.register.identification_information', ['identification_Data' => $identification_Data])
                </div>

                <input type="hidden" id="employeeId" name="employee_id" value="{{ isset($employee) && $employee['employee_id'] ? $employee['employee_id'] : '' }}">
                <button type = "submit" id="registerButton" class = "btn btn-primary float-end">Register Employee</button>

                <br>
            </div>

            <!-- button container -->
            <!-- <div class = "card">
                <div class = "card-body">

                   <input type="hidden" id="employeeId" name="employee_id" value="{{ isset($employee) && $employee['employee_id'] ? $employee['employee_id'] : '' }}">
                   <button type = "submit" class = "btn btn-primary float-end">Register Employee</button>
                </div>
            </div> -->
            </form>
        </div>
    </section>
@endsection

@push('js')
<script>
    window.onload = function() {
    var employeeId = localStorage.getItem('employeeId');
    var employeeName = localStorage.getItem('employeeName');

    if (employeeId && employeeName) {
        // Set the hidden input value
        document.getElementById('employeeId').value = employeeId;

        // Update the button text
        document.getElementById('registerButton').textContent = 'Update ' + employeeName;
    }
};
</script>



<script>
        function getCities(params, callBack){
            //show loading
            $(".loader-wrapper").fadeIn("slow", function () {
                $(this).show();
            });

            var urL = "{{url('cities/get-cities')}}"; // Default to registration route
            var ajaxType = 'post'; // Default method is POST for registration
            // if ($('#employeeId').val()) {
            //     urL = "{{ route('employee.update', ':id') }}".replace(':id', $('#employeeId').val()); // Set the update route
            //     ajaxType = 'put'; // Set method to PUT for update
            // }

            $.ajax({
                type: ajaxType, //"post",
                url: urL,
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
    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>

$(document).ready(function() {
    $('#employeeForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        if (this.checkValidity()) {
            $(".loader-wrapper").fadeIn("slow");

            // Create a FormData object
            var formData = new FormData(this);


           // Check if employeeId exists in the form (for update)
           var urL = "{{ route('employee.register') }}"; // Default to registration route
            var ajaxType = 'POST'; // Default method is POST for registration
            if ($('#employeeId').val()) {
                urL = "{{ route('employee.update', ':id') }}".replace(':id', $('#employeeId').val()); // Set the update route
                ajaxType = 'post'; // Set method to PUT for update
                formData.append('_method', 'post'); // Add the _method field for PUT requests
            }

            $.ajax({
                url: urL, //"",
                type: ajaxType, //'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include the CSRF token
                },
                contentType: false, // Set to false to let jQuery handle the content type
                processData: false, // Set to false to prevent jQuery from processing data
                dataType: 'json',
                success: function(response) {
                    $(".loader-wrapper").fadeOut("slow");

                    // Show SweetAlert based on the response
                    if (response && response.has_error === 0) {
                        showAlert(response);
                        $('#employeeForm')[0].reset(); // Optional: Reset form on success

                          // Redirect after user dismisses the alert
                        swal({
                            title: "Success!",
                            text: "Employee registered successfully.",
                            icon: "success",
                            button: "OK",
                        }).then(function() {
                            // Redirect to the index page after the alert is closed
                            window.location.href = "{{ route('employee.index') }}";
                        });
                    } else {
                        showAlert(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Status:", status);
                    console.error("Response:", xhr.responseText);

                    // Use SweetAlert for consistent error handling
                    swal(
                        'Request Failed',
                        'An error occurred while processing the request. Please try again.',
                        'error'
                    );

                    $(".loader-wrapper").fadeOut("slow");
                }
            });
        } else {
            this.reportValidity();
        }
    });
});

</script>

<script>
    $(document).ready(function() {
        $('#top-family-information').on('click', function() {
            // Ensure the tab content becomes active when clicked
            $('#tab-family-information').addClass('active show');
            $(this).addClass('active');
        });
    });
</script>
@endpush

