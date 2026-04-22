@push('css')
<style>
    .table .action-button{
        opacity: 0;
    }
    .table tr:hover .action-button{
        opacity: 1;
    }

    #documents_table_principal,
    #documents_table_special {
        border-collapse: collapse;
        width: 100%;
    }

    #documents_table_principal td,
    #documents_table_principal th,
    #documents_table_special td,
    #documents_table_special th {
        border: 1px solid #dee2e6;
        padding: 8px;
    }
</style>
@endpush

@if(isset($principal_loan))
<div class="card principal-loan">
    <div class="card-header">
        <h4>Principal Loan Documents</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="documents_table_principal">
                <thead>
                    <tr>
                        <th>Document Type</th>
                        <th>File</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Show documents for specific loan when editing, empty when creating --}}
                    @if(isset($principal_attachments))
                        @foreach($principal_attachments as $doc)
                            <tr data-id="{{ $doc->id }}"
                                data-original-doc-type="{{ $doc->documentType->id }}"
                                data-original-remarks="{{ $doc->remarks }}"
                            >
                                <td>
                                    <select name="client_documents[doc_type][]" class="form-select">
                                        <option value="">Select Document</option>
                                        @foreach($docTypes as $type)
                                            <option value="{{ $type->id }}"
                                                @if($doc->documentType->type_name === $type->type_name) selected @endif
                                            >{{ $type->type_name }}</option>
                                        @endforeach
                                </select></td>
                                <td>
                                    @if(!empty($doc->file_path))
                                            <a
                                                href="{{ Storage::url($doc->file_path) }}"
                                                target="_blank"
                                            >
                                                View current file
                                            </a>
                                    @endif
                                </td>
                                <td><input
                                    type="text"
                                    name="client_documents[file_upload_remarks][]"
                                    class="form-control"
                                    placeholder="Remarks..."
                                    value="{{ $doc->remarks }}"
                                    readonly
                                ></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                </table>
                <button type="button" id="addFileRowPrincipal" class="btn btn-primary mt-2">Add File</button>

        </div>

    </div>
</div>
@endif

@if(isset($special_loan))
<div class="card special-loan">
    <div class="card-header">
        <h4>Special Loan Documents</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="documents_table_special">
                <thead>
                    <tr>
                        <th>Document Type</th>
                        <th>File</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Show documents for specific loan when editing, empty when creating --}}
                    @if(isset($special_attachments))
                        @foreach($special_attachments as $doc)
                            <tr data-id="{{ $doc->id }}"
                                data-original-doc-type="{{ $doc->documentType->id }}"
                                data-original-remarks="{{ $doc->remarks }}"
                            >
                                <td>
                                    <select name="client_documents[doc_type][]" class="form-select">
                                        <option value="">Select Document</option>
                                        @foreach($docTypes as $type)
                                            <option value="{{ $type->id }}"
                                                @if($doc->documentType->type_name === $type->type_name) selected @endif
                                            >{{ $type->type_name }}</option>
                                        @endforeach
                                </select></td>
                                <td>
                                    @if(!empty($doc->file_path))
                                            <a
                                                href="{{ Storage::url($doc->file_path) }}"
                                                target="_blank"
                                            >
                                                View current file
                                            </a>
                                    @endif
                                </td>
                                <td><input
                                    type="text"
                                    name="client_documents[file_upload_remarks][]"
                                    class="form-control"
                                    placeholder="Remarks..."
                                    value="{{ $doc->remarks }}"
                                    readonly
                                ></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                </table>
                <button type="button" id="addFileRowSpecial" class="btn btn-primary mt-2">Add File</button>
        </div>

    </div>
</div>
@endif

@push('js')
<script>
$(document).ready(function () {
    let docTypes = [];

    initializeDocumentTypes()

    function generateDocTypeOptions(index) {
        return `
            <select name="client_documents[${index}][doc_type]" class="form-select">
                <option value="">Select Document</option>
                ${docTypes.map(type =>
                    `<option value="${type.id}">${type.type_name}</option>`
                ).join('')}
            </select>
        `;
    }

    $('#addFileRowPrincipal').on('click', function () {
        addFileRow('principal')
    });

    $('#addFileRowSpecial').on('click', function () {
        addFileRow('special')
    });

    function addFileRow(table) {
        var tableRef = `${"#documents_table_"+table}`
        const index = $(tableRef+' tbody tr').length;

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
                        <i class="fa fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm btn-save">
                        <i class="fa fa-save"></i>
                    </button>
                </td>
            </tr>
        `;

        $(tableRef+' tbody').append(newRow);
    }

    $('#documents_table_principal').on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });

    $('#documents_table_special').on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
    });

    $('#documents_table_principal').on('click', '.btn-save', function () {
        const row = $(this).closest('tr');
        let loan_id = $('#principal_loan_id').val()
        submitFile(row, loan_id)
    });

    $('#documents_table_special').on('click', '.btn-save', function () {
        const row = $(this).closest('tr');
        let loan_id = $('#special_loan_id').val()
        submitFile(row, loan_id)
    });

    function submitFile(row, loan_id) {
        let client_id = $('#client_id').val()
        const formData = new FormData();

        // Get values from the row
        const docType = row.find('select[name*="[doc_type]"]').val();
        const fileInput = row.find('input[type="file"]')[0];
        const remarks = row.find('input[name*="[file_upload_remarks]"]').val();

        // Validation
        let errors = [];

        if (!docType) {
            errors.push("Document type is required.");
        }

        if (!fileInput || fileInput.files.length === 0) {
            errors.push("File upload is required.");
        } else {
            const file = fileInput.files[0];

            // Optional: file size limit (e.g., 5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                errors.push("File must be less than 5MB.");
            }

            // Optional: file type validation
            const allowedTypes = ["application/pdf", "image/jpeg", "image/png"];
            if (!allowedTypes.includes(file.type)) {
                errors.push("Only PDF, JPG, and PNG files are allowed.");
            }
        }

        // If there are errors, stop submission
        if (errors.length > 0) {
            showToast.error(errors.join("<br>"));
            return; // stop execution
        }

        // Append to FormData
        formData.append('doc_type', docType);
        formData.append('file_upload', fileInput.files[0]); // important
        formData.append('remarks', remarks);
        formData.append('loan_id', loan_id);
        formData.append('client_id', client_id);

        $.ajax({
            url: '/ajax/attachments/upload',
            type: 'POST',
            data: formData,
            contentType: false,   // required for FormData
            processData: false,   // required for FormData
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                showToast.success(res.message);
                lockRow(row);
            },
            error: function (error) {
                let response = error.responseJSON;

                    if (response?.errors) {
                        let messages = Object.values(response.errors).flat().join('<br>');
                        showToast.error(messages);
                    } else if (response?.message) {
                        showToast.error(response.message);
                    }
            }
        });
    }

    function initializeDocumentTypes()
    {
        $.ajax({
            type: "get",
            url: "{{url('ajax/document-types')}}",
            dataType: "json",
            success: function (response) {
                docTypes = response;
            },
            error: function (err) {
                console.error("Failed to load document types", err);
            }
        });
    }


    function lockRow(row) {
        // Disable all inputs/selects/file inputs
        row.find('input, select, textarea, button').prop('disabled', true);
    }

});
</script>

@endpush
