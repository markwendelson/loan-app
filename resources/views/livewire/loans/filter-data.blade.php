<div>
    <div class="d-flex justify-content-between">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <span>Show</span>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="d-flex justify-content-end align-items-center gap-2">

            <div class="input-group">
                <input type="search"
                    class="form-control"
                    placeholder="Search Loans..."
                    wire:model.live.debounce.300ms="q"
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>

            <select class="form-select" wire:model.live="prod_id">
                <option value="">-- All Product --</option>
                @if($products->isNotEmpty())
                    @foreach($products as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
                @endif
            </select>

            <select class="form-select" wire:model.live="status">
                <option value="">-- All Status --</option>
                @foreach(config('constants.loan_status') as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
            </select>

            <select class="form-select" wire:model.live="branch_id">
                <option value="">-- All Branch --</option>
                @if($branch->isNotEmpty())
                    @foreach($branch as $key => $val)
                        <option value="{{ $val->id }}">{{ Str::title($val->name) }}</option>
                    @endforeach
                @endif
            </select>
            <div class="d-flex justify-content-start align-items-center gap-2">
                <input type="date" class="form-control" wire:model.live.debounce.300ms="from">
                <span>-</span>
                <input type="date" class="form-control" wire:model.live.debounce.300ms="to">
            </div>
            
        </div>
    </div>
</div>


