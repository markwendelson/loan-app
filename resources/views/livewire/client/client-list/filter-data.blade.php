<div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2 align-items-center">
            <span>Show</span>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="d-flex gap-2 align-items-center">

            <!-- search textbox -->
            <input type="search"
                class="form-control"
                wire:model.lazy="q"
                placeholder="Search..."
            >

            <select class="form-select" wire:model.live="branch_id">
                <option value="">-- Branch --</option>
                @if(isset($branchData) && $branchData)
                    @foreach($branchData as $key => $val)
                        @if(in_array($val->id, $allowedBranches))
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>

            <select class="form-select" wire:model.live="group_type">
                <option value="">-- Tag Type --</option>
                @if(isset($groupTypes) && $groupTypes)
                    @foreach($groupTypes as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
