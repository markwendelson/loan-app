<div>
    <div class="d-flex justify-content-between">
        <div class="d-flex justify-content-start align-items-center gap-2">
            <span>Show</span>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="10">10</option>
                <option value="10">10</option>
            </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <div class="input-group">
                <input type="search"
                    class="form-control"
                    placeholder="Search encashments..."
                    wire:model.live.debounce.300ms="q"
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>

            <select class="form-select" wire:model.live="bank">
                <option value="">-- All Banks --</option>
                @if($bankData)
                    @foreach($bankData as $key => $val)
                        <option value="{{ $val->bank_code }}">{{ $val->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    
</div>
