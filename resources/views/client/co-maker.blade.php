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
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="card-title">Co-Maker</h4>
            </div>
            <div>
                <div class="comaker-search-section">
                    <div class="col mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" name="comaker_search" id="comaker_search"
                                placeholder="Search co-maker to add" />
                            <button class="btn btn-outline-secondary" type="button" id="clear_comaker_search">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
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

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="client_comaker">
                <thead>
                    <tr>
                        <th class="text-left" width="150">Name</th>
                        <th class="text-left" width="150">Address</th>
                        <th class="text-center co-maker-action-btn-header" width="10"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Show co-makers for specific loan when editing, empty when creating --}}
                    @if(request()->route()->getName() == 'loan.create')
                        @php
                            // Filter co-makers by this loan's ID
                            $loanCoMakers = $previous_loan->borrower->coMakers->filter(function ($comaker) use ($previous_loan) {
                                return $comaker->pivot->loan_id == $previous_loan->id;
                            });
                        @endphp
                        @foreach($loanCoMakers as $comaker)
                            <tr data-id="{{'tmp_'.$comaker->id}}" data-client-id="{{$comaker->pivot->client_id}}" data-loan-id="">
                                <td>
                                    <input type="hidden" name="client_comaker[]" value="{{ $comaker->id }}">
                                    {{$comaker->details->fullname}}
                                </td>
                                <td>{{$comaker->details->present_full_address}}</td>
                                <td class="text-center co-maker-action-btn-col">
                                    <button class="btn btn-sm btn-danger btn-remove" data-id="{{'tmp_'.$comaker->id}}" data-client-id="{{ $comaker->pivot->client_id }}" data-loan-id="">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    @if(isset($loan) && isset($loan->borrower))
                        @php
                            // Filter co-makers by this loan's ID
                            $loanCoMakers = $loan->borrower->coMakers->filter(function ($comaker) use ($loan) {
                                return $comaker->pivot->loan_id == $loan->id;
                            });
                        @endphp
                        @foreach($loanCoMakers as $comaker)
                            <tr data-id="{{$comaker->id}}">
                                <td>{{$comaker->details->fullname}}</td>
                                <td>{{$comaker->details->present_full_address}}</td>
                                <td class="text-center co-maker-action-btn-col">
                                    <button class="btn btn-sm btn-danger btn-remove" data-id="{{$comaker->id}}">
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