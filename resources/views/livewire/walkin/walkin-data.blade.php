<div>
    <div class="position-relative">
        <table class="table table-bordered table-hover" id="walkinTable">
            <thead>
                <tr>
                    <th width="150" class="text-center">Date</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>M.I</th>
                    <th>Amount Withdrawn</th>
                    <th>Charge</th>
                    <th>Net Proceeds</th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($walkins) && $walkins->isNotEmpty())
                    @foreach($walkins as $w)
                    <tr>
                        <td class="text-center">{{ $w->date->format('M-d-Y') }}</td>
                        <td>{{ $w->last_name }}</td>
                        <td>{{ $w->first_name }}</td>
                        <td class="text-center">{{ $w->middle_initial }}</td>
                        <td class="text-end">{{ number_format($w->amount_withdrawn,2) }}</td>
                        <td class="text-end">{{ number_format($w->charge,2) }}</td>
                        <td class="text-end">{{ number_format($w->net_proceeds,2) }}</td>
                        <td class="actions-cell text-center">
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning editBtn"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger deleteBtn"><i class="fa fa-trash"></i></button>
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
