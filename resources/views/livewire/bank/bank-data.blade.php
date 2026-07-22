<div>
    <div class="position-relative">
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="150" class="text-center">Bank Code</th>
                    <th>Name</th>
                    <th class="text-center" width="80">Status</th>
                    <th width="50" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                @if($banks && $banks->isNotEmpty())
                    @foreach($banks as $key => $val)
                        <tr wire:key="{{ $val->bank_code }}">
                            <td class="text-center">{{ $val->bank_code }}</td>
                            <td>{{ $val->name }}</td>
                            <td class="text-center"><span class="{{ config('constants.status_badge')[$val->status] }}">{{ config('constants.status_value')[$val->status] }}</span></td>
                            <td>
                                <button type="button" class="btn btn-light btn-sm"><i class="fa fa-ellipsis-v"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            @if($banks->hasPages())
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="d-flex justify-content-end">
                            {{ $banks->links('partials.pagination.default-livewire') }}
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
