@push('css')
<style>
    .table .action-button{
        opacity: 0;
    }
    .table tr:hover .action-button{
        opacity: 1;
    }

    #client_comaker {
        border-collapse: collapse;
        width: 100%;
    }

    #client_comaker td, #client_comaker th {
        border: 1px solid #dee2e6; /* or whatever border color you use */
        padding: 8px;
    }

    /* Hide comaker_table initially */
    #comaker_table_wrapper {
        display: none;
    }
</style>
@endpush
<div class="card">
    <div class="card-header">Co-Maker</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 comaker-search-section">
                <div class="col mb-2">
                    <input type="text" class="form-control" name="comaker_search" id="comaker_search" placeholder="Search co-maker to add"/>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="comaker_table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="client_comaker">
                        <thead>
                            <tr>
                                <th class="text-left" width="150">Name</th>
                                <th class="text-left" width="150">Address</th>
                                <th class="text-center co-maker-action-btn-header" width="80"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Show co-makers for specific loan when editing, empty when creating --}}
                            @if(isset($loan) && isset($loan->borrower))
                                @php
                                    // Filter co-makers by this loan's ID
                                    $loanCoMakers = $loan->borrower->coMakers->filter(function($comaker) use ($loan) {
                                        return $comaker->pivot->loan_id == $loan->id;
                                    });
                                @endphp
                                @foreach($loanCoMakers as $comaker)
                                    <tr data-id="{{$comaker->id}}">
                                        <td>{{$comaker->details->fullname}}</td>
                                        <td>{{$comaker->details->present_full_address}}</td>
                                        <td class="text-center co-maker-action-btn-col">
                                            <button class="register-comaker btn btn-sm btn-danger btn-remove" data-id="{{$comaker->id}}">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        dataUrl = "{{ route('ajax.get-clients') }}"
        const table = $('#comaker_table').DataTable({
            dom: 't',
            paging: false,
            info: false,
            ordering: false,
            processing: true,
            serverSide: true,
            searching: true,
            deferLoading: 0, // Prevent initial load
            ajax: {
                url: dataUrl,
                data: function(d) {
                    // Only load if there's a search value
                    var searchValue = $('#comaker_search').val();
                    if (!searchValue || searchValue.length === 0) {
                        return false; // Cancel the request
                    }
                    
                    var $borrower = $('#borrower_id');
                    // Check element exists AND has value
                    if ($borrower.length && $borrower.val()) {
                        d.borrower_id = $borrower.val();
                    }
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, row) {
                        let badgeClass = row.status == 1 ? 'badge-success' : 'badge-danger';
                        let statusLabel = row.status == 1 ? 'Active' : 'Inactive'; // optional label
                        let client_code = row.client_code;
                        let client_name = row.details.fullname;
                        let client_address = row.details.present_full_address;

                        return `
                            <div class="d-flex gap-2 justify-content-between">
                                <div>
                                    <div><strong>${client_code}</strong></div>
                                    <div>${client_name}</div>
                                    <div>${client_address}</div>
                                    <div><span class="badge ${badgeClass}">${statusLabel}</span></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary action-button register-comaker" data-id="${row.id}" data-name="${client_name}" data-address="${client_address}">
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                            `;
                    }
                },
            ],

        });

        $('#comaker_search').on('keyup', function () {
            const searchValue = this.value;
            
            // Show table only if there's search text
            if (searchValue.length > 0) {
                $('#comaker_table_wrapper').show();
                table.search(searchValue).draw();
            } else {
                $('#comaker_table_wrapper').hide();
                table.search('').draw();
            }
        });

        table.on('click', '.register-comaker', function () {
            const id= $(this).data('id');
            const name= $(this).data('name');
            const address= $(this).data('address');

            if($(`#client_comaker tbody tr[data-id="${id}"]`).length === 0) {
                const newRow = `
                <tr data-id="${id}">
                    <td><input type="hidden" name="client_comaker[]" value="${id}">${name}</td>
                    <td>${address}</td>
                    <td>
                    <button class="register-comaker btn btn-sm btn-danger btn-remove" data-id="${id}">
                        Remove
                    </button>
                    </td>
                </tr>
                `;
    
                $('#client_comaker tbody').append(newRow);
            } else {
                showToast.error('Client already added.');
            }
        });

        $('#client_comaker').on('click', '.btn-remove', function () {
            const rowId = $(this).data('id');
            $(`#client_comaker tbody tr[data-id="${rowId}"]`).remove();
        });
    })
</script>
@endpush