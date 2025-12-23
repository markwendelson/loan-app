@push('css')
<style>
    .table .action-button{
        opacity: 0;
    }
    .table tr:hover .action-button{
        opacity: 1;
    }

    #client_comaker td, #client_comaker th {
        border: 1px solid #dee2e6; /* or whatever border color you use */
        padding: 8px;
    }
</style>
@endpush
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="client_comaker">
                        <thead>
                            <tr>
                                <th class="text-left" width="150">Name</th>
                                <th class="text-left" width="150">Address</th>
                                <th class="text-left" width="80">Balance</th>
                                <th class="text-center co-maker-action-btn-header" width="80"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Show co-makers for specific loan when editing, empty when creating --}}
                            @foreach($loan_is_co_maker as $comaker)
                                <tr data-id="{{$comaker->id}}">
                                    <td>{{$comaker->details->fullname}}</td>
                                    <td>{{$comaker->details->present_full_address}}</td>
                                    <td class="text-end">{{ number_format($comaker->loan_balance, 2) }}</td>
                                    <td class="text-center co-maker-action-btn-col">
                                    </td>
                                </tr>
                            @endforeach
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
        
    })
</script>
@endpush