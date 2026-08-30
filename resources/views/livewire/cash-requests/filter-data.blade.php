<div>
    <div class="d-flex justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span>Show</span>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <div class="d-flex align-items-center gap-2">
                <select class="form-select" wire:model.live="branch_id">
                    <option value="">-- All Branch --</option>
                    @if(isset($branches) && $branches)
                        @foreach($branches as $key => $val)
                            <option value="{{ $val->id }}">{{ Str::title($val->name) }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <select class="form-select" wire:model.live="status">
                    <option value="">-- All Status --</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>
</div>
