@extends('layouts.app')

@section('title') Walk-in Clients @endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/walkin.css') }}">
@endpush

@section('content')


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
    <div class="card d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-fixed" id="walkinTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">
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
                    <tbody>
                        @foreach($walkins as $w)
                        <tr>
                            <td class="text-center"><input type="checkbox"></td>
                            <td>{{ $w->date->format('M-d-Y') }}</td>
                            <td>{{ $w->last_name }}</td>
                            <td>{{ $w->first_name }}</td>
                            <td class="text-center">{{ $w->middle_initial }}</td>
                            <td class="text-end">{{ number_format($w->amount_withdrawn,2) }}</td>
                            <td class="text-end">{{ number_format($w->charge,2) }}</td>
                            <td class="text-end">{{ number_format($w->net_proceeds,2) }}</td>
                            <td class="actions-cell text-center">
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-warning editBtn"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger deleteBtn"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                                <!-- <button class="btn btn-sm btn-warning editBtn">Edit</button> -->
                                <!-- <button class="btn btn-sm btn-danger deleteBtn">Delete</button> -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-md-none">
        @foreach($walkins as $w)
        <div class="card mb-2">
            <div class="card-body">

                <div class="fw-semibold">
                    {{ $w->date->format('M-d-Y') }}
                </div>

                <div class="text-muted">
                    {{ $w->last_name }}, {{ $w->first_name }} {{ $w->middle_initial }}
                </div>

                <div class="mt-2">
                    <strong>Amount:</strong>
                    {{ number_format($w->amount_withdrawn,2) }}
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-warning editBtn w-50"
                            data-id="{{ $w->id }}">
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger deleteBtn w-50"
                            data-id="{{ $w->id }}">
                        Delete
                    </button>
                    <!-- <button class="btn btn-sm btn-warning editBtn w-50">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn w-50">Delete</button> -->
                </div>

            </div>
        </div>
        @endforeach
    </div>



{{-- Include Walk-in Modal --}}
@include('others.walkin.modal')
@endsection

@section('scripts')
<script>
const modal = new bootstrap.Modal(document.getElementById('walkinModal'));

// Select/Deselect all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.selectItem').forEach(cb => cb.checked = checked);
});

document.addEventListener('click', e => {
       const printBtn = e.target.closest('.printBtn');
    if (!printBtn) return;

    const id = printBtn.dataset.id;
    if (!id) return;

    window.open(`/walkin/${id}/receipt`, '_blank');
    // if (e.target.classList.contains('printBtn')) {
    //     const id = e.target.closest('tr').dataset.id;
    //     window.open(`/walkin/${id}/receipt`, '_blank');
    // }
});

document.getElementById('btnPrint').addEventListener('click', () => {
    const selectedIds = Array.from(document.querySelectorAll('.selectItem:checked'))
        .map(cb => cb.value);

    if(selectedIds.length === 0) {
        alert('Please select at least one record to print.');
        return;
    }

    // Open new window for each selected record
    selectedIds.forEach(id => {
        window.open(`/walkin/${id}/receipt`, '_blank');
    });
});

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

        // Reset checkbox + hidden value
    const noChargeCheckbox = document.getElementById('no_service_charge');
    const noChargeValue = document.getElementById('no_service_charge_value');
    noChargeCheckbox.checked = false;
    noChargeCheckbox.disabled = false;
    noChargeValue.value = 0;
}

// Auto-calculate charge & net proceeds
document.getElementById('amount_withdrawn').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    const noChargeCheckbox = document.getElementById('no_service_charge');
    const chargeField = document.getElementById('charge');
    const netField = document.getElementById('net_proceeds');

    if (noChargeCheckbox.checked) {
        chargeField.value = '0.00';
        netField.value = amount.toFixed(2);
    } else {
        // Use your existing logic or DB percentage
        let charge = amount <= 3000 ? amount*0.035 : amount <= 5000 ? amount*0.03 : amount*0.02;
        chargeField.value = charge.toFixed(2);
        netField.value = (amount - charge).toFixed(2);
    }
    // const amount = parseFloat(this.value) || 0;
    //  const noChargeCheckbox = document.getElementById('no_service_charge');
    // let charge = amount <= 3000 ? amount*0.035 : amount <= 5000 ? amount*0.03 : amount*0.02;
    // document.getElementById('charge').value = charge.toFixed(2);
    // document.getElementById('net_proceeds').value = (amount - charge).toFixed(2);
});

// Edit
document.addEventListener('click', async e => {
    const editBtn = e.target.closest('.editBtn');
    if (!editBtn) return;

    const id = editBtn.dataset.id;
    if (!id) return;

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

    // Checkbox logic
    const noChargeCheckbox = document.getElementById('no_service_charge');
    const noChargeValue = document.getElementById('no_service_charge_value');

    noChargeCheckbox.checked = data.no_service_charge == 1;
    noChargeValue.value = data.no_service_charge == 1 ? 1 : 0;

    document.getElementById('charge').disabled = noChargeCheckbox.checked;

    document.getElementById('walkinModalLabel').textContent = 'Edit Walk-in';
    modal.show();
    // if (e.target.classList.contains('editBtn')) {
    //     const id = e.target.closest('tr').dataset.id;
    //     const res = await fetch(`/walkin/${id}`);
    //     const data = await res.json();

    //     document.getElementById('walkinId').value = data.id;
    //     document.getElementById('date').value = data.date;
    //     document.getElementById('last_name').value = data.last_name;
    //     document.getElementById('first_name').value = data.first_name;
    //     document.getElementById('middle_initial').value = data.middle_initial;
    //     document.getElementById('amount_withdrawn').value = data.amount_withdrawn;
    //     document.getElementById('charge').value = data.charge;
    //     document.getElementById('net_proceeds').value = data.net_proceeds;

    //   // Set checkbox & hidden field
    //     const noChargeCheckbox = document.getElementById('no_service_charge');
    //     const noChargeValue = document.getElementById('no_service_charge_value');
    //     noChargeCheckbox.checked = data.no_service_charge == 1;
    //     noChargeValue.value = data.no_service_charge == 1 ? 1 : 0;

    //     // Disable charge if no service charge
    //     document.getElementById('charge').disabled = noChargeCheckbox.checked ? true : false;

    //     document.getElementById('walkinModalLabel').textContent = 'Edit Walk-in';
    //     modal.show();
    // }
});

// Save (Create/Update)
document.getElementById('walkinForm').addEventListener('submit', async e => {
    e.preventDefault();

    const form = e.target;
    const data = Object.fromEntries(new FormData(form));
    const id = data.id;

    const url = id ? `/walkin/${id}` : '/walkin';
    const method = id ? 'PUT' : 'POST';

    const csrfToken =
        document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // ← REQUIRED
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
});

  // First, parse the JSON
    const result = await response.json();

    // Then, if successful, do modal hide, open receipt, reload table
    if(response.ok){
        modal.hide();
        resetForm();
            window.open(result.print_url, '_blank'); //  open in new tab
         location.reload();

    
    } else {
        alert(result.message || "Failed to save");
        console.error(result);
    }
   
});

// Delete
document.addEventListener('click', async e => {
    const deleteBtn = e.target.closest('.deleteBtn');
    if (!deleteBtn) return;

    if (!confirm('Are you sure?')) return;

    const id = deleteBtn.dataset.id;
    if (!id) return;

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    await fetch(`/walkin/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    });

    location.reload();
    // if (e.target.classList.contains('deleteBtn')) {
    //     if (!confirm('Are you sure?')) return;
    //     const id = e.target.closest('tr').dataset.id;
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    //     await fetch(`/walkin/${id}`, { method:'DELETE', headers:{ 'X-CSRF-TOKEN': csrfToken } });
    //     location.reload();
    // }
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
