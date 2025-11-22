@extends('layouts.app')

@section('content')
<div class="container">
     <!-- Header + Add button card -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">Check Encashments</h4>
             <div class="d-flex gap-2">
            <!-- PRINT BUTTON -->
            <button class="btn btn-secondary" id="btnPrint">
                Print Receipt
            </button>

            <!-- ADD BUTTON -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkModal" onclick="openCreateModal()">
                Add Check
            </button>
        </div>
        </div>
    </div>

    <!-- Table card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="checksTable">
                    <thead class="table-light">
                        <tr>
                             <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th class="d-none">ID</th>
                            <th>Date</th>
                            <th>Check No</th>
                            <th>Bank</th>
                            <th>Amount</th>
                            <th>Net Proceeds</th>

                            <!-- NEW FIELDS -->
                            <th>ID Type</th>
                            <th>ID Number</th>
                            <th>ID Picture</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checks as $check)
                        <tr id="checkRow{{ $check->id }}">
                            <td>
                                <input type="checkbox" class="selectItem" value="{{ $check->id }}">
                            </td>
                            <td class="d-none">{{ $check->id }}</td>
                            <td>{{ $check->transDate->format('M-d-Y') }}</td>
                            <td>{{ $check->checkNo }}</td>
                            <td>{{ $check->bankName }}</td>
                            <td>{{ number_format($check->checkAmount, 2) }}</td>
                            <td>{{ number_format($check->net_proceeds, 2) }}</td>

                            <td>{{ $check->id_type }}</td>
                            <td>{{ $check->id_number }}</td>
                            <td>
                                @if($check->id_picture_path)
                                    <a href="{{ asset('storage/' . $check->id_picture_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $check->id_picture_path) }}" width="50" class="img-thumbnail">
                                    </a>
                                @else
                                    No Image
                                @endif
                            </td>

                            <td class="d-flex flex-column flex-md-row gap-1">
                                <button class="btn btn-sm btn-warning" onclick="editCheck({{ $check->id }})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCheck({{ $check->id }})">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Include modal --}}
@include('others.check-encashments.modal')
@endsection

@section('scripts')
<script>
function openCreateModal() {

    $('#checkForm')[0].reset();
    $('#checkId').val('');
    $('#idPicturePreview').attr('src', ''); // clear preview
    $('#checkModalLabel').text('Add Check');

     // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("transDate").value = today;
    
    // Reset image preview
    const preview = document.getElementById("idPicturePreview");
    preview.src = '';
    preview.style.display = "none";
}

// function formatNumber(num) {
//    if (num === null || num === undefined || num === '') return '';
//     if (isNaN(num)) return '';

//     return parseFloat(num).toLocaleString('en-US', {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2
//     });
// }

// Format a number with commas
function formatNumberWithCommas(num) {
    if (!num) return '';
    let parts = num.toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts.join('.');
}

// Remove commas before saving
function unformatNumber(num) {
    return num ? num.replace(/,/g, '') : 0;
}

const checkAmountInput = document.getElementById('checkAmount');

// Select all on focus
checkAmountInput.addEventListener('focus', function() {
    this.select();
});

// Prevent mouse click from deselecting
checkAmountInput.addEventListener('mouseup', function(e){
    e.preventDefault();
});

// Live formatting on key input
['checkAmount', 'charge', 'netProceeds'].forEach(id => {
    const input = document.getElementById(id);
    input.addEventListener('input', function() {
        // Remove any non-digit and non-dot characters
        let clean = this.value.replace(/[^0-9.]/g, '');
        // Format with commas
        this.value = formatNumberWithCommas(clean);
    });
});

// Automatically calculate charge and net proceeds on checkAmount input
document.getElementById('checkAmount').addEventListener('input', function() {
    let amount = parseFloat(unformatNumber(this.value)) || 0;
    let charge = 0;

    if (amount <= 3000) charge = amount * 0.035;
    else if (amount <= 5000) charge = amount * 0.03;
    else charge = amount * 0.02;

    charge = Math.round(charge * 100) / 100;
    const netProceeds = Math.round((amount - charge) * 100) / 100;

    document.getElementById('charge').value = formatNumberWithCommas(charge);
    document.getElementById('netProceeds').value = formatNumberWithCommas(netProceeds);
});


/* ===========================
   LOAD CHECK INTO EDIT MODAL
   =========================== */
function editCheck(id) {
    if (!id) return;

    $.get('{{ url("check-encashments") }}/' + id, function(data){
    console.log("FULL DATA:", data);
    console.log("checkAmount:", data.checkAmount);
    console.log("charge:", data.charge);
    console.log("net_proceeds:", data.net_proceeds);

        $('#checkId').val(data.id);
        $('#transDate').val(data.transDate);
        $('#checkNo').val(data.checkNo);
        $('#bankName').val(data.bankName);
        $('#checkAmount').val(formatNumberWithCommas(data.checkAmount));
        $('#charge').val(formatNumberWithCommas(data.charge));
        $('#netProceeds').val(formatNumberWithCommas(data.net_proceeds));

        $('#idType').val(data.id_type);
        $('#idNumber').val(data.id_number);

        const preview = $('#idPicturePreview');
        if (data.id_picture_path) {
            preview.attr('src', '/storage/' + data.id_picture_path);
            preview.show();
        } else {
            preview.attr('src', '');
            preview.hide();
        }

        $('#checkModalLabel').text('Edit Check');
        $('#checkModal').modal('show');
    });
}


document.getElementById('btnPrint').addEventListener('click', () => {
    const selectedIds = Array.from(document.querySelectorAll('.selectItem:checked'))
        .map(cb => cb.value);

    if(selectedIds.length === 0) {
        alert('Please select at least one record to print.');
        return;
    }

    // Open new window for each selected record
    selectedIds.forEach(id => {
        window.open(`/check-encashments/${id}/receipt`, '_blank');
    });
});



/* ===========================
   SAVE CHECK (CREATE/UPDATE)
   =========================== */
$('#checkForm').submit(function(e){
    e.preventDefault();

    let formData = new FormData();
    let id = $('#checkId').val();

    formData.append('transDate', $('#transDate').val());
    formData.append('checkNo', $('#checkNo').val());
    formData.append('bankName', $('#bankName').val());
    formData.append('checkAmount', unformatNumber($('#checkAmount').val()));
    formData.append('charge', unformatNumber($('#charge').val()));
    formData.append('net_proceeds', unformatNumber($('#netProceeds').val()));
    // formData.append('checkAmount', $('#checkAmount').val());
    // formData.append('charge', $('#charge').val());
    // formData.append('net_proceeds', $('#netProceeds').val());
    formData.append('id_type', $('#idType').val());       // corrected
    formData.append('id_number', $('#idNumber').val());   // corrected

    // IMPORTANT: file upload
    if ($('#idPicture')[0].files.length > 0) {            // corrected
        formData.append('id_picture', $('#idPicture')[0].files[0]);
    }

    formData.append('_token', '{{ csrf_token() }}');

    let method = id ? 'POST' : 'POST';
    let url = id ? '/check-encashments/' + id : '/check-encashments';

    if (id) {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data){
            location.reload();
        },
        error: function(err){
            console.log(err);
            alert('Error saving check');
        }
    });
});



/* ===========================
   DELETE CHECK
   =========================== */
function deleteCheck(id) {
    if(confirm('Are you sure you want to delete this check?')) {
        $.ajax({
            url: '/check-encashments/' + id,
            method: 'POST',
            data: { 
                _method: 'DELETE',
                _token: '{{ csrf_token() }}' 
            },
            success: function(data) {
                $('#checkRow' + id).remove();
            },
            error: function(err) {
                alert('Error deleting check');
            }
        });
    }
}
</script>
@endsection
