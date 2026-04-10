@push('css')
<style>
    .table .action-button{
        opacity: 0;
    }
    .table tr:hover .action-button{
        opacity: 1;
    }

    #documents_table {
        border-collapse: collapse;
        width: 100%;
    }

    #documents_table td, #documents_table th {
        border: 1px solid #dee2e6; /* or whatever border color you use */
        padding: 8px;
    }

     /* Add smooth transition on hover */
    .attachment-items {
        transition: transform 0.3s ease;
    }

    /* Hover effect for attachment item */
    .attachment-items:hover {
        transform: translateY(-5px); /* Moves the attachment item up slightly */
    }
</style>
@endpush

<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Documents</h4>
        
        @if(isset($loan) && isset($loan->borrower->documents))
            @foreach($loan->borrower->documents as $val) 
                <div class="border p-3 rounded attachment-items mb-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- File Info Section -->
                        <div class="d-flex justify-content-start align-items-center gap-2 flex-column flex-sm-row">
                            <!-- File Icon -->
                            <img src="{{ url('/assets/images/file.png') }}" width="40" height="40" class="rounded">
                            <div class="d-flex flex-column">
                                @if($val->file_path)
                                    <a href="{{ Storage::url($val->file_path) }}" target="_blank" class="text-wrap"><div class="text-wrap">{{ basename($val->file_path) }}</div></a>
                                @endif
                                <span>{{ $val->document_type_name ?? ''; }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-2 mt-sm-0">
                            <!-- download url -->
                            <a class="btn btn-outline-primary btn-xs" href="{{ Storage::url($val->file_path) }}" download="{{ basename($val->file_path) }}"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <button type="button" id="addFileRow" class="btn btn-primary mt-2">Add File</button>

    </div>
</div>

@push('js')
<script>
$(document).ready(function () {
    let documentTypesJson = <?=$documentTypesJson ?? '[]';?>;
    console.log(documentTypesJson);

    function generateDocTypeOptions(index) {
        let options = '';

        if(documentTypesJson.length > 0){
            for(let i = 0; i < documentTypesJson.length; i++) {
                options += `<option value="${documentTypesJson[i].id}">${documentTypesJson[i].type_name}</option>`;
            }
        }

        return `
            <select name="client_documents[${index}][doc_type]" class="form-select">
                <option value="">Select Document</option>
                ${options}
            </select>
        `;
    }

    $('#addFileRow').on('click', function () {
        const index = $('#documents_table tbody tr').length;

        const newRow = `
            <tr>
                <td>${generateDocTypeOptions(index)}</td>

                <td>
                    <input
                        type="file"
                        name="client_documents[${index}][file_upload]"
                        class="form-control"
                    >
                </td>

                <td>
                    <input
                        type="text"
                        name="client_documents[${index}][file_upload_remarks]"
                        class="form-control"
                        placeholder="Remarks..."
                    >
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                        Remove
                    </button>
                </td>
            </tr>
        `;

        $('#documents_table tbody').append(newRow);
    });

    $('#documents_table').on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });

});
</script>

@endpush