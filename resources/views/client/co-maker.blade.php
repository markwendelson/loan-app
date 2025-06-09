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
</style>
@endpush
<div class="card">
    <div class="card-header">
        <h5>Co-Maker</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="col mb-2">
                    <input type="text" class="form-control" name="comaker_search" id="comaker_search" placeholder="Search"/>
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
                                <th class="text-center" width="80"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($client->coMakers))
                                @foreach($client->coMakers as $comaker)
                                    <tr data-id="{{$comaker->id}}">
                                        <td>{{$comaker->details->fullname}}</td>
                                        <td>{{$comaker->details->present_full_address}}</td>
                                        <td>
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
        const match = window.location.pathname.match(/^\/client\/(\d+)\/edit$/);
        const clientId = match ? match[1] : null;

        if (clientId) {
            
            dataUrl = `{{ route('client.edit',':client') }}`.replace(':client',clientId)
        } else {
            dataUrl = "{{ route('client.index') }}"
        }
        const table = $('#comaker_table').DataTable({
            dom: 't',
            paging: false,
            info: false,
            ordering: false,
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: dataUrl,
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
            table.search(this.value).draw();
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