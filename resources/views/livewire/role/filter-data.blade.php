<div>
    <div class="d-flex justify-content-between gap-2">
        <div class="d-flex gap-2 align-items-center">
            <span>Show</span>
            <select wire:model.live="entries" class="form-select">
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
                placeholder="Search roles..."
                wire:model.live.debounce.300ms="q"
            >
        </div>
    </div>
</div>
