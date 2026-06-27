<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="expenseTable">
            <thead>
                <tr>
                    <th class="align-middle">Date</th>
                    <th class="align-middle">Branch</th>
                    <th class="align-middle text-center" width="150">Payment Method</th>
                    <th class="align-middle">Description</th>
                    <th class="align-middle text-center">Amount</th>
                    <th class="align-middle">Created By</th>
                    <th class="text-center align-middle" width="50"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($expenses) && $expenses->isNotEmpty())
                    @foreach($expenses as $key => $val)
                        <tr data-id="{{ $val->id }}">
                            <td>{{ $val->expense_date }}</td>
                            <td class="{{ isset($val->branch_name) && $val->branch_name ? '' : 'text-center'; }}">{{ $val->branch_name ?? '-' }}</td>
                            <td class="text-center">{{ config('constants.payment_methods')[$val->payment_method]}}</td>
                            <td>{{ $val->description ?? ''; }}</td>
                            <td class="text-end">{{ number_format($val->amount, 2) ?? ''; }}</td>
                            <td class="{{ (isset($val->user_name) && $val->user_name) && (isset($val->email) && $val->email) ? '' : 'text-center'; }}">{{ ($val->user_name ?? $val->email) ?? '-'; }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu" style="opacity: 1 !important;">
                                        <li><a class="dropdown-item btnViewExpense" href="#"><i class="fa fa-eye"></i> View Expenses Data</a></li>
                                        @can('edit-expense')
                                        <li><a class="dropdown-item btnEdit" href="#"><i class="fa fa-edit"></i> Edit</a></li>
                                        @endcan
                                        @can('delete-expense')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item btnDelete" href="#"><i class="fa fa-trash txt-danger"></i> Delete</a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No Items Found!</td>
                    </tr>
                @endif
            </tbody>
            @if($expenses->hasPages())
            <tfoot>
                <tr>
                    <td colspan="7">
                        <div class="d-flex justify-content-end">
                            {{ $expenses->links('partials.pagination.default-livewire') }}
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
