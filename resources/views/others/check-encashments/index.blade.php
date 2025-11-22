@extends('layouts.app')

@section('content')
<div class="container">
     <!-- Header + Add button card -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">Check Encashments</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkModal" onclick="openCreateModal()">
                Add Check
            </button>
        </div>
    </div>

    <!-- Table card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="checksTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
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
                            <td>{{ $check->id }}</td>
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

/* ===========================
   LOAD CHECK INTO EDIT MODAL
   =========================== */
function editCheck(id) {
    if (!id) return;

    $.get('{{ url("check-encashments") }}/' + id, function(data){
        $('#checkId').val(data.id);
        $('#transDate').val(data.transDate);
        $('#checkNo').val(data.checkNo);
        $('#bankName').val(data.bankName);
        $('#checkAmount').val(data.checkAmount);
        $('#charge').val(data.charge);
        $('#netProceeds').val(data.net_proceeds);

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
    formData.append('checkAmount', $('#checkAmount').val());
    formData.append('charge', $('#charge').val());
    formData.append('net_proceeds', $('#netProceeds').val());
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
