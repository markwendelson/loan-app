@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3 mt-4" >Walk-in Clients</h4>

    <!-- Search + Add Button Row -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <input 
                type="text" 
                id="searchWalkin" 
                class="form-control me-2" 
                placeholder="Search walk-in..."
                style="max-width: 250px;"
            >
        </div>

        <!-- Button -->
        <button class="btn btn-primary" id="btnAdd" data-bs-toggle="modal" data-bs-target="#walkinModal">
          <i class="fa fa-plus me-1"></i> Add Walk-in
        </button>
        
        <!-- <button class="btn btn-primary" id="btnAdd">
            <i class="fa fa-plus me-1"></i> Add Walk-in
        </button> -->
    </div>

    <!-- ✅ Table -->
    <table class="table table-bordered" id="walkinTable">
        <thead>
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
        <tbody id="walkinGrid"><!-- will be filled by JS --></tbody>
    </table>
</div>

<!-- ✅ Modal -->
<div class="modal fade" id="walkinModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="walkinForm">
        <div class="modal-header">
          <h5 class="modal-title">Walk-in Info</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="walkinId" name="id">
          <div class="mb-2">
            <label>Date</label>
            <input type="date" class="form-control" id="date" name="date">
          </div>
          <div class="mb-2">
            <label>Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
          </div>
          <div class="mb-2">
            <label>First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name">
          </div>
          <div class="mb-2">
            <label>M.I</label>
            <input type="text" class="form-control" id="middle_initial" name="middle_initial">
          </div>
             <div class="mb-2">
            <label>Amount withdrawn</label>
            <input type="number" class="form-control" id="amount_withdrawn" name="amount_withdrawn">
          </div>
          <div class="mb-2">
            <label>Charge</label>
            <input type="number" class="form-control" id="charge" name="charge">
          </div>
          <div class="mb-2">
            <label>Net Proceeds</label>
            <input type="number" class="form-control" id="net_proceeds" name="net_proceeds">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@section('scripts')
<script>
let modal = new bootstrap.Modal(document.getElementById('walkinModal'));
    // Optional: clear form each time modal is opened
    document.getElementById('walkinModal').addEventListener('show.bs.modal', () => {
        document.getElementById('walkinForm').reset();
        document.getElementById('walkinId').value = '';
    });

    // 🔹 Open modal for Add
    document.getElementById('btnAdd').addEventListener('click', () => {
        document.getElementById('walkinForm').reset();
        document.getElementById('walkinId').value = '';
        modal.show();
    });

//  Load Walkins from your API
async function loadWalkins() {
    const res = await fetch('/api/walkin');
    const data = await res.json();
    const tbody = document.getElementById('walkinGrid');
    tbody.innerHTML = data.map(r => `
        <tr data-id="${r.id}">
            <td>${r.date}</td>
            <td>${r.last_name}</td>
            <td>${r.first_name}</td>
            <td>${r.middle_initial ?? ''}</td>
            <td>${r.amount_withdrawn}</td>
            <td>${r.charge}</td>
            <td>${r.net_proceeds}</td>
            <td>
                <button class="btn btn-sm btn-warning editBtn">Edit</button>
                <button class="btn btn-sm btn-danger deleteBtn">Delete</button>
            </td>
        </tr>
    `).join('');
}
loadWalkins();

// ✅ Add / Edit button logic
document.addEventListener('click', e => {
    if (e.target.classList.contains('editBtn')) {
        const row = e.target.closest('tr');
        document.getElementById('walkinId').value = row.dataset.id;
        document.getElementById('date').value = row.children[0].textContent;
        document.getElementById('last_name').value = row.children[1].textContent;
        document.getElementById('first_name').value = row.children[2].textContent;
        document.getElementById('middle_initial').value = row.children[3].textContent;
        document.getElementById('amount_withdrawn').value = row.children[4].textContent;
        document.getElementById('charge').value = row.children[5].textContent;
        document.getElementById('net_proceeds').value = row.children[6].textContent;
        modal.show();
    }
});

// ✅ Form submit logic (Create / Update)
document.getElementById('walkinForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form));
    const id = data.id;
    const url = id ? `/api/walkin/${id}` : '/api/walkin';
    const method = id ? 'PUT' : 'POST';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

await fetch(url, {
    method,
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken   // ✅ Laravel requires this for POST/PUT/DELETE
    },
    body: JSON.stringify(data)
});


    form.reset();
    modal.hide();
    loadWalkins();
});

// ✅ Delete record
document.addEventListener('click', async e => {
    if (e.target.classList.contains('deleteBtn')) {
        if (!confirm('Are you sure you want to delete this record?')) return;
        const id = e.target.closest('tr').dataset.id;
        await fetch(`/api/walkin/${id}`, { method: 'DELETE' });
        loadWalkins();
    }
});

// ✅ Open modal for Add
// document.getElementById('btnAdd').addEventListener('click', () => {
//     document.getElementById('walkinForm').reset();
//     document.getElementById('walkinId').value = '';
//     modal.show();
// });

// ✅ Search filter
document.getElementById('searchWalkin').addEventListener('keyup', function() {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#walkinGrid tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>
@endsection
