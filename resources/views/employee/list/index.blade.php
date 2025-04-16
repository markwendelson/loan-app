@extends('layouts.app')
@push('css')

<style>
    .employee-thumbnail {
        width: 150px; /* Width of the square */
        height: 150px; /* Height of the square */
        object-fit: cover; /* Ensures the image fits without stretching */
        border-radius: 8px; /* Creates rounded edges */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: Adds a shadow effect */
        transition: transform 0.2s; /* Optional: Smooth hover effect */
        margin-bottom: 10px; /* Adds space below the image */
        margin: 0px;
    }

    .thumbnail-size {
        width: 230px;
        height: 200px;
        object-fit: cover;
        border-radius: 16px;
        align-self: center;
    }

    .employee-card {
        display: flex;
        flex-direction: column; /* Stack the content vertically */
        align-items: center; /* Center items horizontally */
        justify-content: center; /* Center items vertically */
        width: 100%; /* Makes it responsive to container width */
        max-width: 250px; /* Maximum width */
        max-height: 1000px;
        height: 270px;
        aspect-ratio: 1 / 1; /* Ensures it remains square */
        margin-right: 1px;
        margin-top: 10px;
        margin-left: 1px;
        border: 1px solid #ddd;
        border-radius: 16px; /* Increase this value for more rounded corners */
        text-align: center;
        padding: 2px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for the card */
        transition: box-shadow 0.3s ease-in-out; /* Smooth transition */
        overflow: hidden; /* Ensures rounded edges show properly */
    }

    .employee-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
    }

    .custom-row {
        gap: 2px; /* Adjust horizontal spacing between cards */
        height: 500px;
    }

   .edit-link {
        display: block;
        margin-top: 1px; /* Adds space between email and edit link */
        text-decoration: none;
        color: #007bff; /* Bootstrap primary color */
        font-weight: bold;
   }

   @media (max-width: 768px) {
    .edit-link {
        font-size: 11px;
        padding: 5px 10px;
    }
   }
    .edit-link:hover {
        text-decoration: underline;
    }
    .employee-email {
        margin-top: 0px; /* Adjust as needed */
         font-size: 11px;
         margin-bottom: 0px;
    }

    .employee-name {
        margin-top: 10px; /* Adjust as needed */
        font-size: 15px;
        margin-bottom: 0px; /* Adjust as needed */
    }

      .employee-code {
        margin-bottom: 0px; /* Adjust as needed */
    }

    .employee-items img:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4); /* Adds a more prominent shadow on hover */
    }

    @media (max-width: 767px) {
        .container {
         padding-left: 10px;
         padding-right: 10px;
        }
    }

 /* Custom column width for different screen sizes */
 .custom-col {
        width: 100%; /* Full width for small screens */
    }

    @media (min-width: 576px) {
        .custom-col {
            width: 50%; /* 50% width for small devices (tablets) */
        }
    }

    @media (min-width: 768px) {
        .custom-col {
            width: 33.33%; /* 33.33% width for medium devices (desktops) */
        }
    }

    @media (min-width: 992px) {
        .custom-col {
            width: 25%; /* 25% width for large devices (larger desktops) */
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Employee List</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    {{-- <li class="breadcrumb-item">
                        <a href="#">
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#fill-user"></use>
                            </svg>
                        </a>
                    </li> --}}
                    <li class="breadcrumb-item">Employees</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Button container -->
<div class="card">
    <div class="card-body">
        <a href="{{ route('employee.register') }}" class="btn btn-primary float-end">
            <i class="fa fa-plus"></i> Add New Employee
        </a>



    </div>
</div>

<div class="card card-body">
    <section id="employee-list">
        <div class="row">
            @foreach ($allEmployees as $employee)
                <div class="col-12 col-md-3 col-lg-2" >
                    <a href="{{ route('employee.update', $employee->id) }}" class="emp-thumbnail" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->display_name }}" target = "_blank">
                    <div class="card employee-items" style = "border-radius: 16px;">
                        <img style = "border-radius: 16px;" src="{{ url('storage/uploads/employee/' . $employee->img) }}" alt="Employee Image" title="{{$employee->display_name}}" class="img-thumbnail thumbnail-size">
                        <div class = "card-body text-center pt-1 pb-1">
                            <!-- Display employee picture -->

                            <h5><strong>{{  $employee->emp_code  }}</strong></h5>
                            <p class = "text-muted">{{$employee->display_name}}</p>
                            <!-- <p class = "text-muted" style = "font-size: 10px !important"><span>Email: {{ $employee->username }}</span></p> -->
                            <!-- <a  class="btn btn-outline-primary edit-link">Edit</a> -->
                        </div>
                    </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    <!-- Pagination -->
    {{ $allEmployees->links() }}
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>





<!-- JavaScript to handle thumbnail click and update button text -->
<script>
    document.querySelectorAll('.emp-thumbnail').forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function(e) {
            // Get employee details from data attributes
            var employeeId = this.getAttribute('data-employee-id');
            var employeeName = this.getAttribute('data-employee-name');

             // Store the employee data in localStorage
        localStorage.setItem('employeeId', employeeId);
        localStorage.setItem('employeeName', employeeName);

        // Optionally, navigate to the registration page
        // Navigate to the registration page with the correct employeeId
        var url = "{{ route('employee.update', ':id') }}".replace(':id', employeeId);
        window.location.href = url;
        });
    });
</script>

@endpush