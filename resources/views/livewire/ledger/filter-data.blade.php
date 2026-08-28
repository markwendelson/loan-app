<div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex justify-content-betwen align-items-center gap-2">
            <span>Show</span>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="d-flex justify-content-start align-items-center gap-2">
            <div class="input-group">
                <input type="search"
                    class="form-control"
                    wire:model.live.debounce.200ms="q"
                    placeholder="Search..."
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>

            <select class="form-select" wire:model.live="branch_id">
                <option value="">All Branch</option>
                @if(isset($branchesData) && $branchesData)
                    @foreach($branchesData as $key => $val)
                        <option value="{{$val->id}}">{{$val->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
