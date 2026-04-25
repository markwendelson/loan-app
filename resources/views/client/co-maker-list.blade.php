@push('css')
    <style>
        /* .table .action-button{
                                opacity: 0;
                            }
                            .table tr:hover .action-button{
                                opacity: 1;
                            } */

        #client_comaker {
            border-collapse: collapse;
            width: 100%;
        }

        #client_comaker td,
        #client_comaker th {
            border: 1px solid #dee2e6;
            /* or whatever border color you use */
            padding: 8px;
        }

        /* Hide comaker_table initially */
        #comaker_table_wrapper {
            display: none;
        }

        .comaker-search-section {
            position: relative;
            z-index: 7;
            /* Optionally adjust the stacking context */
        }

        .comaker-search-section .table-responsive {
            position: absolute;
            right: 0;
            z-index: 999;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
@endpush

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title">Client as Co-Maker</h4>
        </div>
        <div>
            <div class="comaker-search-section">
                <div class="col mb-2">
                    <input type="text" class="form-control" name="comaker_search" id="comaker_search"
                        placeholder="Search co-maker to add" />
                </div>
                <div class="row">
                    <div class="table-responsive bg-white border p-0 d-none">
                        <table class="table table-hover" id="comaker_table">
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
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="client_comaker">
                <thead>
                    <tr>
                        <th class="text-start" width="150">Name</th>
                        <th class="text-start" width="150">Address</th>
                        <th class="text-center" width="80">Balance</th>
                        <th class="text-center co-maker-action-btn-header" width="80"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Show co-makers for specific loan when editing, empty when creating --}}
                    @if(isset($loan_is_co_maker) && $loan_is_co_maker)
                        @foreach($loan_is_co_maker as $comaker)
                            <tr data-id="{{$comaker->pivot->client_id}}" data-client-id="{{$comaker->pivot->co_maker_id}}"
                                data-loan-id="{{$comaker->pivot->loan_id}}">
                                <td>{{$comaker->details->fullname}}</td>
                                <td>{{$comaker->details->present_full_address}}</td>
                                <td class="text-end">{{ $comaker->loan_balance }}</td>
                                <td class="text-center co-maker-action-btn-col">
                                    <button class="btn btn-sm btn-danger btn-remove" data-id="{{$comaker->pivot->client_id}}"
                                        data-client-id="{{$comaker->pivot->co_maker_id}}"
                                        data-loan-id="{{$comaker->pivot->loan_id}}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('js')
    <script src="{{ asset('assets/js/client-co-maker.js') }}"></script>
@endpush