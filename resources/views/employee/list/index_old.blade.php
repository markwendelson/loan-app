

@include('partials.__header')

<style>


</style>

<div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Employee List </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="index.html">                                       
                            <svg class="stroke-icon">
                                <use href="../assets/svg/icon-sprite.svg#fill-user"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Employees'</li>
                        {{-- <li class="breadcrumb-item active">Project List</li> --}}
                    </ol>
                </div>
            </div>
        </div>
 </div>

 <!-- button container -->
 <div class="card">
        <div class="card-body">
            <a href="{{ route('employee.register') }}" class="btn btn-primary float-end">
                <i class="fa fa-plus"></i> Add New Employee
            </a>
        </div>
 </div>

    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">   
                <table class="table table-bordered table-hover" id = "tblEmployees">
                    <thead class = "bg-primary">
                        <tr>
                            <th class = "text-center" width = "120">No.</th>
                            <th class = "text-center" width = "150">E-Code</th>
                            <th>Employee Name</th>                
                            <!-- <th>User Type</th>  -->
                            <th class = "text-center" width = "180">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                    <tbody>
                        @include('employee.list.index_data')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   









@include('partials.__footer')