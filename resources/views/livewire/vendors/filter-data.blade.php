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

            @if(auth()->user()->hasRole(['admin', 'super_admin'])) 
            <div class="form-check checkbox checkbox-primary mb-0">
                <input class="form-check-input " id="chckDeleted" type="checkbox"  value="1" name="is_deleted" {{ isset($filterData['is_deleted']) && $filterData['is_deleted'] ? 'checked' : ''; }}>
                <label class="form-check-label mb-1" for="chckDeleted"><small>Deleted</small></label>
            </div>
            @endif
        </div>

        <div class="d-flex justify-content-end gap-2">
            <!-- search filter -->
            <div class="input-group">
                <input type="search" 
                    class="form-control" 
                    name="q" 
                    wire:model.live.debounce.300ms="q"
                    placeholder="Search vendors..."
                    style="width: 250px !important;"
                >
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            

            <!-- vendor type -->
            <select class="form-select"
                name="vendor_type"
                wire:model.live="vendor_type"
                style="width: 250px !important;"

            >
                <option value="">-- All Types --</option>
                @if($vendorTypes)
                    @foreach($vendorTypes as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
                @endif
            </select>

            @if(auth()->user()->hasRole(['admin', 'super-admin']) && (isset($usersData) && $usersData))
                <select class="form-select" name="author" wire:model.live="author" style="width: 200px !important;">
                    <option value="">-- Author --</option>
                    @foreach($usersData as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name; }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>
</div>
