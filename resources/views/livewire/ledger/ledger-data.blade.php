<div>
    <div class="position-relative">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Account No.</th>
                        <th>Account Name</th>
                        @if(isset($loanProducts) && $loanProducts)
                            @foreach($loanProducts as $key => $val)
                                <th>{{$val->name}}</th>
                            @endforeach
                        @endif
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($ledgerData) && $ledgerData)
                        @foreach($ledgerData as $key => $val)
                            <tr wire:key="{{$val->id}}">
                                <td>{{$val->client_code}}</td>
                                <td>{{Str::title($val->fullname)}}</td>
                                <!-- mockup data -->
                                @if(isset($loanProducts) && $loanProducts)
                                    @foreach($loanProducts as $key => $val)
                                        <td class="text-end">0.00</td>
                                    @endforeach
                                @endif
                                <td class="text-end">0.00</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @if($ledgerData->hasPages())
                <tfoot>
                    <tr>
                        <td colspan="{{$colCount}}">
                            <div class="d-flex justify-content-end">
                                {{ $ledgerData->links('partials.pagination.default-livewire') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div
            wire:loading.delay
            wire:loading.class.remove="d-none"
            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-50 d-none"
            style="z-index: 10;"
        >
        
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="custom-loader" role="status"></div>
                        <span>Please wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
