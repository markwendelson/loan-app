@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3 mt-4">Walk-in Clients</h4>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <input type="text" id="searchWalkin" class="form-control me-2" placeholder="Search walk-in..." style="max-width: 250px;">
        <button class="btn btn-primary" id="btnAddWalkin">Add Walk-in</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="walkinTable">
            <thead class="table-light">
                <tr>
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

{{-- Include Walk-in Modal --}}
@include('others.walkin.modal')
@endsection

@section('scripts')
<script>
const modal = new bootstrap.Modal(document.getElementById('walkinModal'));

// Open modal for Add
document.getElementById('btnAddWalkin').addEventListener('click', () => {
    resetForm();
    document.getElementById('walkinModalLabel').textContent = 'Add Walk-in';
    modal.show();
});

// Reset form & default date
function resetForm() {
    const form = document.getElementById('walkinForm');
    form.reset();
    document.getElementById('walkinId').value = '';
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    document.getElementById('charge').value = '';
    document.getElementById('net_proceeds').value = '';
}

// Auto-calculate charge & net proceeds
document.getElementById('amount_withdrawn').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    let charge = amount <= 3000 ? amount*0.035 : amount <= 5000 ? amount*0.03 : amount*0.02;
    document.getElementById('charge').value = charge.toFixed(2);
    document.getElementById('net_proceeds').value = (amount - charge).toFixed(2);
});

// Edit
document.addEventListener('click', async e => {
    if (e.target.classList.contains('editBtn')) {
        const id = e.target.closest('tr').dataset.id;
        const res = await fetch(`/walkin/${id}`);
        const data = await res.json();

        document.getElementById('walkinId').value = data.id;
        document.getElementById('date').value = data.date;
        document.getElementById('last_name').value = data.last_name;
        document.getElementById('first_name').value = data.first_name;
        document.getElementById('middle_initial').value = data.middle_initial;
        document.getElementById('amount_withdrawn').value = data.amount_withdrawn;
        document.getElementById('charge').value = data.charge;
        document.getElementById('net_proceeds').value = data.net_proceeds;

        document.getElementById('walkinModalLabel').textContent = 'Edit Walk-in';
        modal.show();
    }
});

// Save (Create/Update)
document.getElementById('walkinForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form));
    const id = data.id;
    const url = id ? `/walkin/${id}` : '/walkin';
    const method = id ? 'PUT' : 'POST';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    await fetch(url, {
        method,
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data)
    });

    resetForm();
    modal.hide();
    location.reload(); // reload table to reflect changes
});

// Delete
document.addEventListener('click', async e => {
    if (e.target.classList.contains('deleteBtn')) {
        if (!confirm('Are you sure?')) return;
        const id = e.target.closest('tr').dataset.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch(`/walkin/${id}`, { method:'DELETE', headers:{ 'X-CSRF-TOKEN': csrfToken } });
        location.reload();
    }
});

// Search filter
document.getElementById('searchWalkin').addEventListener('keyup', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('#walkinGrid tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
    });
});
</script>
@endsection
