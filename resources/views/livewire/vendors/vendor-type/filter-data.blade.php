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

        <div class="d-flex justify-content-end gap-2">
            <!-- search filter -->
            <div class="input-group">
                <input type="search" 
                    class="form-control" 
                    name="q" 
                    wire:model.live.debounce.300ms="q"
                    placeholder="Search vendors..."
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>

            <!-- status -->
            <select class="form-select" wire:model.live="status">
                <option value="">-- All Status --</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </div>
</div>
