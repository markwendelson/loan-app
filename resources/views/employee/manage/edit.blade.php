@include('partials.__header')
<style>
    .nav-link:hover {
    background-color: #f0f0f0; /* Light background color on hover */
    color: #333; /* Optional: adjust text color for better visibility */
    transition: background-color 0.3s ease; /* Smooth transition effect */
}


</style>

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
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Employee Register</li>
                        {{-- <li class="breadcrumb-item active">Project List</li> --}}
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class = "content">
        <div class = "container-fluid">
    <!-- <div class = "alert alert-success">
        <p>Hello world</p>
    </div> -->
        <form id="employeeForm" method = "post" action = "{{url()->current()}}" enctype="multipart/form-data">
        @csrf

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
                    @include('employee.register.personal_information', ['branch_data' => $branch_data, 'empStatus' => $empStatus])
                   
                </div>
            
                <!-- Emp Family -->
                <div class="tab-pane fade show" id="tab-family-information" role="tabpanel" aria-labelledby="top-family-information">
                    <!-- Include Family view -->
                     @include('employee.register.family_information')
                </div>
            
                <!-- Emp IDs -->
                <div class="tab-pane fade show" id="tab-Emp-Identification" role="tabpanel" aria-labelledby="tab-Emp-Identification">
                    <!-- Include IDs-->
                    @include('employee.register.identification_information', ['identification_Data' => $identification_Data])
                </div>
            </div>

            <!-- button container -->
            <div class = "card">
                <div class = "card-body">
                    <button type = "submit" class = "btn btn-primary float-end">Register Employee</button>
                </div>
            </div>
        </form>
        </div>
    </section>












@include('partials.__footer')

