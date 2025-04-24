@extends('layouts.app')

@push('css')
<style>
    /* Custom CSS for pointer cursor */
    .table-row-pointer {
         cursor: pointer;
     }

     #success-message {
     width: 20%; /* Adjust width as needed */
     margin: 0;
     height: 50px;
     text-align: left;
     padding-left: 10px;
     display: flex;
     align-items: center;

     /* Set initial opacity for fade effect */
     opacity: 0;
     background-color: #d4edda; /* Success background color */
     transition: opacity 0.5s ease, background-color 0.5s ease; /* Adjust duration as needed */
 }

  /* Trigger opacity to 1 when the success message is shown */
  #success-message.show {
     opacity: 1;
 }

       /* Custom CSS for header background color */
       .custom-header {
         background-color: #147369; /* Change to your desired color */
         color: white !important; /* Change text color for contrast */
     }

     .custom-header th {
     color: white !important; /* Change text color for contrast */
 }
</style>
@endpush

@section('content')

<!-- Success Message -->
@if(session('success'))
    <div id="success-message" class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Branch | Manage Branch</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item">Branch</li>
                    <li class="breadcrumb-item active">Manage Branch</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <!-- button container -->
    <div class="card">
        <div class="card-body">
        <button class="btn btn-primary float-end" onclick="openAddModal()"><i class = "fa fa-plus"></i> New Branch</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="custom-header">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th style="width: 400px;">Branch Name</th>
                        <th style="width: 200px;">Contact Information</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th style="width: 210px;">Actions</th> <!-- New column for Edit/Delete buttons -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $index => $branch)
                        <tr class="table-row-pointer">
                            <td>{{ $index + 1 }}</td> <!-- Displaying row counter -->
                            <td>{{ $branch->code }}</td>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->contact_info }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->status == 1 ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <!-- Edit Button -->
                                 <!-- Edit Button to trigger modal -->
                                 <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $branch }})">Edit</button>


                                <!-- Delete Button with confirmation dialog -->
                                <form action="{{ route('branch.destroy', $branch->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</button>
                                </form>
                            </td>

                            <input type="hidden" value="{{ $branch->id }}"> <!-- Hidden ID -->

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- New Branch Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addForm" method="POST" action="{{ route('branch.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newBranchCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="newBranchCode" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="newBranchName" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="newBranchName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="newContactInfo" class="form-label">Contact Information</label>
                        <input type="number" class="form-control" id="newContactInfo" name="contact_info" required>
                    </div>
                    <div class="mb-3">
                        <label for="newAddress" class="form-label">Address</label>
                        <input type="text" class="form-control" id="newAddress" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Status</label>
                        <select class="form-select" id="newStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="{{ route('branch.update', ':id') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="branchId" name="id">
                    <div class="mb-3">
                        <label for="branchCode" class="form-label">Code</label>
                        <input type="text" class="form-control" id="branchCode" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label for="branchName" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branchName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactInfo" class="form-label">Contact Information</label>
                        <input type="number" class="form-control" id="contactInfo" name="contact_info" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>




@endsection

@push('js')

<script>
    function openAddModal() {
        // Reset the form fields
        document.getElementById('addForm').reset();

        // Show the modal
        var addModal = new bootstrap.Modal(document.getElementById('addModal'), {});
        addModal.show();
    }
</script>

<script>
    // JavaScript function to open the Edit modal and populate fields
    function openEditModal(branch) {
        document.getElementById('branchId').value = branch.id;
        document.getElementById('branchCode').value = branch.code;
        document.getElementById('branchName').value = branch.name;
        document.getElementById('contactInfo').value = branch.contact_info;
        document.getElementById('address').value = branch.address;
        document.getElementById('status').value = branch.status;

        // Update the form action URL to include the branch ID
        document.getElementById('editForm').action = `/branches/${branch.id}`;

        // Show the modal
        var editModal = new bootstrap.Modal(document.getElementById('editModal'), {});
        editModal.show();
    }
</script>


<script>
    // Check if the success message exists
    window.onload = function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            // Set a timer to hide the message after 10 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 2000); // 10000 milliseconds = 10 seconds
        }
    };
</script>

    <!-- Include Bootstrap 5 JS (optional, for tooltips, modals, etc.) -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
@endpush