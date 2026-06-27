<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="tblLoan">
            <thead>
                <tr>
                    <th width="200">Loan No</th>
                    <th>Borrower</th>
                    <th>Product</th>
                    <th class="text-center" width="80">Status</th>
                    <th class="text-center" width="150">Loan Date</th>

                    <th class="text-center" width="50">
                        <i class="fa fa-cog"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
                @if($loans->isNotEmpty())
                    @foreach($loans as $key => $val)
                        <tr wire:key="loan-{{$val->id}}">
                            <td class="fw-semibold">{{ $val->loan_no }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ Str::title($val->lastname . ', ' . $val->firstname . ' ' . $val->middlename) ?? '' }}</span>
                                    <span class="fw-light">{{ $val->client_code }}</span>
                                </div>
                                
                            </td>
                            <td>{{ $val->product_name ?? '' }}</td>
                            <td>{!! config('constants.loan_status_labels')[$val->status] ?? '' !!}</td>
                            <td class="text-center">
                                {{ date('M d, Y', strtotime($val->loan_date)) }}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class=" fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @can('view-loan')
                                            @if($current_module !== 'declined')
                                            <li><a class="dropdown-item" href="{{ url('loan/'  . $val->id) }}"><i class="fa fa-eye me-1"></i>View Loan</a></li>
                                            @endif
                                            @if(!in_array($val->status, [0,2,3,4]) && !empty($val->disbursed_date))
                                            <li><a class="dropdown-item" href="{{ url('loan/'. $val->id . '/print' ) }}" target="_blank"><i class="fa fa-print me-1"></i>Print Receipt</a></li>
                                            @endif
                                        @endcan

                                        @if(!in_array($val->status, [1,5,3]))
                                            @can('edit-loan')
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ url('loan/'. $val->id . '/edit') }}" target="_blank"><i class="fa fa-pencil me-1"></i>Edit Loan</a></li>
                                            @endcan
                                            @can('delete-loan')
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" onclick="deleteData({{ $val->id }})"><i class="fa fa-trash txt-danger me-1"></i>Delete Loan</a></li>
                                            @endcan
                                        @endif
                                        @can('request-loan-cancellation')
                                            @if($val->status == 1 && $current_module !== 'disbursement')
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item btnWithdraw" data-id="{{ $val->id }}" href="#"><i class="fa fa-exchange me-1"></i>Withdraw Loan</a></li>
                                            @endcan
                                        @endcan
                                        @if(empty($val->disbursement_date) && $current_module == 'disbursement')
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item btn-cash-out" href="#" data-id="{{ $val->id }}" data-amount="{{ $val->net_proceeds ?? ''; }}"><i class="fa fa-money me-1"></i>Disburse Loan</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="6">
                            No data found.
                        </td>
                    </tr>
                @endif
            </tbody>
            @if($loans->hasPages())
            <tfoot>
                <tr>
                    <td colspan="6">
                        <div class="d-flex justify-content-end">
                            {{ $loans->links('partials.pagination.default-livewire') }}
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div
            wire:loading.delay
            wire:loading.class.remove="d-none"
            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-50 d-none"
            style="z-index: 10;"
        >
        
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="custom-loader" role="status"></div>
                        <span>Please wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
