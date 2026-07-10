<div>
    <div class="position-relative">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="checksTable">
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Date</th>
                        <th>Customer Name</th>
                        <th>Check No</th>
                        <th>Bank</th>
                        <th>Amount</th>
                        <th>Charge</th>
                        <th>Net Proceeds</th>

                        <!-- NEW FIELDS -->
                        <th class="d-none">ID Type</th>
                        <th class="d-none">ID Number</th>
                        <th>ID Picture</th>

                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody>
                    @if($checks)
                        @foreach($checks as $check)
                        <tr id="checkRow{{ $check->id }}">
                            <td class="d-none">{{ $check->id }}</td>
                            <td>{{ $check->transDate->format('M-d-Y') }}</td>
                            <td>{{ $check->customer_name }}</td>
                            <td>{{ $check->checkNo }}</td>
                            <td>{{ $check->bank_name }}</td>
                            <td class="text-end">{{ number_format($check->checkAmount, 2) }}</td>
                            <td class="text-end">{{ number_format($check->charge, 2) }}</td>
                            <td class="text-end">{{ number_format($check->net_proceeds, 2) }}</td>

                            <td class="d-none">{{ $check->id_type }}</td>
                            <td class="d-none">{{ $check->id_number }}</td>
                            <td>
                                @if($check->id_picture_path && file_exists(public_path('storage/' . $check->id_picture_path)))
                                    <a href="{{ asset('storage/' . $check->id_picture_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $check->id_picture_path) }}" width="50" class="img-thumbnail">
                                    </a>
                                @else
                                    No Image
                                @endif
                            </td>

                            <td class="text-center">
                                {{-- <button class="btn btn-sm btn-warning" onclick="editCheck({{ $check->id }})"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCheck({{ $check->id }})"><i class="fa fa-trash"></i></button> --}}

                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="editCheck({{ $check->id }})"><i class="fa fa-edit me-1"></i>Edit</a></li>
                                        <li><a class="dropdown-item" href="{{ url('/check-encashments/'. $check->id .'/receipt') }}" target="_blank"><i class="fa fa-print me-1"></i>Print Receipt</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="deleteCheck({{ $check->id }})"><i class="fa fa-trash me-1 txt-danger"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
                @if($checks->hasPages())
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <div class="d-flex justify-content-end">
                                {{ $checks->links('partials.pagination.default-livewire') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
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
