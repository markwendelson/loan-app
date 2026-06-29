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
                <select class="form-select"
                    wire:model.live="branch_id"
                >
                    <option value="">-- All Branch --</option>
                    @if(isset($branches) && $branches)
                        @foreach($branches as $key => $val)
                            @if(in_array($val->id, $allowedBranches))
                            <option value="{{ $val->id }}">{{ Str::title($val->name) }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <select class="form-select" wire:model.live="payment_method">
                    <option value="">-- All Payment Method --</option>
                    @foreach(config('constants.payment_methods') as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                @if(auth()->user()->hasAnyRole(['admin', 'super-admin']) )
                <select class="form-select" name="author">
                    <option value="">-- All Author --</option>
                    @foreach($userData as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
    </div>
</div>
