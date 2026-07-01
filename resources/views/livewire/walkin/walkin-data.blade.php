<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="walkinTable">
            <thead>
                <tr>
                    <th class="align-middle text-center" width="150">Date</th>
                    <th class="align-middle">Name</th>
                    <th class="align-middle text-center" width="150">Amount Withdrawn</th>
                    <th class="align-middle text-center" width="150">Charge</th>
                    <th class="align-middle text-center" width="150">Net Proceeds</th>
                    <th class="align-middle" width="50"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($walkins) && $walkins->isNotEmpty())
                    @foreach($walkins as $w)
                    <tr>
                        <td class="text-center">{{ $w->date->format('M-d-Y') }}</td>
                        <td>{{ Str::title($w->last_name . ' ' . $w->first_name . ', ' . $w->middle_initial) }}</td>
                        <td class="text-end">{{ number_format($w->amount_withdrawn,2) }}</td>
                        <td class="text-end">{{ number_format($w->charge,2) }}</td>
                        <td class="text-end">{{ number_format($w->net_proceeds,2) }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fa fa-pencil me-1"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="{{ url('/walkin/'. $w->id .'/receipt') }}" target="_blank"><i class="fa fa-print me-1"></i>Print Receipt</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item txt-danger" href="#"><i class="fa fa-trash me-1"></i>Delete</a></li>
                                </ul>
                            </div>
                            
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No records found...</td>
                    </tr>
                @endif
            </tbody>
            @if($walkins->hasPages())
            <tfoot>
                <tr>
                    <td colspan="8">
                        <div class="d-flex justify-content-end">
                            {{ $walkins->links('partials.pagination.default-livewire') }}
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
