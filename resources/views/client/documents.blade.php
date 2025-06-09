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
</style>
@endpush

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="documents_table">
                <thead>
                    <tr>
                    <th>Document Type</th>
                    <th>File</th>
                    <th>Remarks</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $docTypes = [
                            'Valid ID',
                            'Payslip',
                            'Cedula',
                            'Bank Statement',
                            'Appointment Letter/COE',
                            'Promisory Note',
                            'Affidavit',
                            'SRA',
                            'Auto Debit'
                        ];
                    @endphp
                    @if(isset($client->documents))
                        @foreach($client->documents as $doc)
                            <tr>
                                <td>
                                    <select name="client_documents[doc_type][]" class="form-select">
                                        <option value="">Select Document</option>
                                        @foreach($docTypes as $type)
                                            <option value="{{ $type }}"
                                                @if($doc->document_type === $type) selected @endif
                                            >
                                                {{ $type }}
                                            </option>
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
                                ></td>
                                <td><button type="button" class="btn btn-danger btn-sm btn-remove">Remove</button></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                </table>
                <button type="button" id="addFileRow" class="btn btn-primary mt-2">Add File</button>

        </div>

    </div>
</div>

@push('js')
<script>
$(document).ready(function () {
    const docTypes = [
        "Valid ID",
        "Payslip",
        "Cedula",
        "Bank Statement",
        "Appointment Letter/COE",
        "Promisory Note",
        "Affidavit",
        "SRA",
        "Auto Debit"
    ];

    function generateDocTypeOptions() {
        return `
            <select name="client_documents[doc_type][]" class="form-select">
                <option value="">Select Document</option>
                ${docTypes.map(type => `<option value="${type}">${type}</option>`).join('')}
            </select>
        `;
    }

    function updateRowIndices() {
        $('#documents_table tbody tr').each(function (index) {
            const rowIndex = index + 1;
            $(this).find('select').attr('name', `doc_type_${rowIndex}`);
            $(this).find('input[type="file"]').attr('name', `file_upload_${rowIndex}`);
            $(this).find('input[type="text"]').attr('name', `file_upload_remarks_${rowIndex}`);
        });
    }

    $('#addFileRow').on('click', function () {
        const rowCount = $('#documents_table tbody tr').length + 1;

        const newRow = `
            <tr>
                <td>${generateDocTypeOptions()}</td>
                <td><input type="file" name="client_documents[file_upload][]" class="form-control"></td>
                <td><input type="text" name="client_documents[file_upload_remarks][]" class="form-control" placeholder="Remarks..."></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove">Remove</button></td>
            </tr>
        `;

        $('#documents_table tbody').append(newRow);
    });

    $('#documents_table').on('click', '.btn-remove', function () {
        $(this).closest('tr').remove();
        updateRowIndices();
    });
});
</script>

@endpush