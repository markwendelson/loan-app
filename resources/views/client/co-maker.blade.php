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
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-left" width="150">Name</th>
                                <th class="text-left" width="150">Address</th>
                                <th class="text-center" width="80"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        const table = $('#comaker_table').DataTable({
            dom: 't',
            paging: false,
            info: false,
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('client.index') }}",
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, row) {
                        const badgeClass = row.status == 1 ? 'badge-success' : 'badge-danger';
                        const statusLabel = row.status == 1 ? 'Active' : 'Inactive'; // optional label

                        return `
                                <div><strong>${row.client_code}</strong></div>
                                <div>${row.details.fullname}</div>
                                <div>${row.details.present_full_address}</div>
                                <div><span class="badge ${badgeClass}">${statusLabel}</span></div>
                                <button class="btn btn-sm btn-primary action-button">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                            `;
                    }
                },
            ],

        });

        $('#comaker_search').on('keyup', function () {
            table.search(this.value).draw();
        });
    })
</script>
@endpush