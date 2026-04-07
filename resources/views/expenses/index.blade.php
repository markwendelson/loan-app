@extends('layouts.app')

@section('title') Expenses @endsection

@push('css')
    <style>
        .bg-default {
            background-color: #1f807f;
        }
        .bg-default th {
            color: white; /* Set white text for <th> elements */
            border: 1px solid white;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Expenses</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body float-end">
            <a href="javascript:void(0)" id="btnAddExpense" data-container="body"
                                    data-bs-toggle="popover" data-placement="top" title=""
                                    data-original-title="Add Expense"><i data-feather="plus"></i></a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expenseTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->creator->name ?? 'System' }}</td>
                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-warning editBtn"
                                                    data-id="{{ $expense->id }}"><i class="fa fa-pencil"></i></button>
                                                <button class="btn btn-sm btn-danger deleteBtn"
                                                    data-id="{{ $expense->id }}"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modal --}}
    @include('expenses.modal')

@endsection

@push('js')
    <script>
        const modal = new bootstrap.Modal(document.getElementById('expenseModal'));

        $('#expenseTable').DataTable();

        // Open modal for Add
        document.getElementById('btnAddExpense').addEventListener('click', () => {
            resetForm();
            document.getElementById('expenseModalLabel').textContent = 'Add Expense';
            modal.show();
        });

        // Reset form
        function resetForm() {
            const form = document.getElementById('expenseForm');
            form.reset();
            document.getElementById('expenseId').value = '';
            document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
        }

        // Edit
        document.addEventListener('click', async e => {
            const editBtn = e.target.closest('.editBtn');
            if (!editBtn) return;

            const id = editBtn.dataset.id;
            if (!id) return;

            const res = await fetch(`/expenses/${id}`);
            const data = await res.json();

            document.getElementById('expenseId').value = data.id;
            document.getElementById('expense_date').value = data.expense_date;
            document.getElementById('category_id').value = data.category_id;
            document.getElementById('amount').value = data.amount;
            document.getElementById('description').value = data.description;

            document.getElementById('expenseModalLabel').textContent = 'Edit Expense';
            modal.show();
        });

        // Save (Create/Update)
        document.getElementById('expenseForm').addEventListener('submit', async e => {
            e.preventDefault();

            const form = e.target;
            const data = Object.fromEntries(new FormData(form));
            const id = data.id;

            const url = id ? `/expenses/${id}` : '/expenses';
            const method = id ? 'PUT' : 'POST';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    modal.hide();
                    resetForm();
                    showToast.success(result.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast.error(result.message || "Failed to save");
                    console.error(result);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast.error('An error occurred');
            }
        });

        // Delete
        document.addEventListener('click', async e => {
            const deleteBtn = e.target.closest('.deleteBtn');
            if (!deleteBtn) return;

            if (!confirm('Are you sure you want to delete this expense?')) return;

            const id = deleteBtn.dataset.id;
            if (!id) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/expenses/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    showToast.success(result.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast.error('Failed to delete');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast.error('An error occurred');
            }
        });
    </script>
@endpush