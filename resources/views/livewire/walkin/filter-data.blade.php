<div>
    <div class="d-flex justify-content-between align-items-center">
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
                    wire:model.live.debounce.300ms="q"
                    placeholder="Search Walk in Clients..."
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
        </div>
    </div>
</div>
