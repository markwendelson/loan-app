@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3 mt-4">Walk-in Clients</h4>

    <!-- Search + Buttons Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Left side: Search input -->
                <input type="text" id="searchWalkin" class="form-control me-2" placeholder="Search walk-in..." style="max-width: 250px;">

                <!-- Right side: Buttons grouped -->
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary" id="btnPrint">Print</button>
                    <button class="btn btn-primary" id="btnAddWalkin">Add Walk-in</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="walkinTable">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Date</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>M.I</th>
                            <th>Amount Withdrawn</th> 
                            <th>Charge</th>
                            <th>Net Proceeds</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="walkinGrid">
                        @foreach($walkins as $w)
                        <tr data-id="{{ $w->id }}">
                            <td>
                                <input type="checkbox" class="selectItem" value="{{ $w->id }}">
                            </td>
                            <td>{{ $w->date->format('M-d-Y') }}</td>
                            <td>{{ $w->last_name }}</td>
                            <td>{{ $w->first_name }}</td>
                            <td>{{ $w->middle_initial }}</td>
                            <td>{{ number_format($w->amount_withdrawn,2) }}</td>
                            <td>{{ number_format($w->charge,2) }}</td>
                            <td>{{ number_format($w->net_proceeds,2) }}</td>
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

    {{-- Include Walk-in Modal --}}
    @include('others.walkin.modal')
</div>
@endsection
