<div>
    <div class="d-flex justify-content-between gap-2">
        <div class="d-flex gap-2 align-items-center">
            <span>Show</span>
            <select name="entries" class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <!-- branch select -->
        <div class="d-flex justify-content-start align-items-center gap-2">
            <input type="search"
                class="form-control"
                placeholder="Search users..."
                wire:model.live.debounce.500ms="q"
            >

            <select wire:model.live="branch_id"
                class="form-select"
            >
                <option value=""> -- All Branch --</option>
                @if(isset($branchData) && $branchData)
                    @foreach($branchData as $key => $val)
                        @if(in_array($val->id, $allowedBranches))
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    </div>
            
</div>
