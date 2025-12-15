@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-3 mt-4">Service Fees Maintenance</h4>

    <div class="card">
        <div class="card-body">
            <button class="btn btn-primary mb-3" id="addServiceFeeBtn">Add Service Fee</button>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Type</th>
                            <th style="width: 20%;">Percentage</th>
                            <th style="width: 20%;">Status</th>
                            <th style="width: 40%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fees as $f)
                        <tr data-id="{{ $f->id }}">
                            <td>{{ ucfirst($f->fee_type) }}</td>
                            <td>{{ $f->percentage }}%</td>
                            <td>
                                <span class="badge {{ $f->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $f->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning editBtn">Edit</button>
                                <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Include modal --}}
@include('service-charge.modal')

<style>
/* Make table scrollable on small screens */
.table-responsive {
    overflow-x: auto;
}

/* Optional: smaller font on small devices */
@media (max-width: 576px) {
    table.table-mobile-friendly {
        font-size: 0.85rem;
    }
    table.table-mobile-friendly td, 
    table.table-mobile-friendly th {
        white-space: nowrap;
    }
    .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
}
</style>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/service-fees.js') }}"></script>
@endsection
