
@php
    use \Illuminate\Support\Str;
@endphp

<div class="card">
    <div class="card-body">
        <div class="report-title d-flex justify-content-between align-items-center">
            <h3 class="txt-primary">{{ env('APP_NAME') }}</h3>
            <h4 class="txt-primary">EXPENSE REPORT</h4>
        </div>
        
        {{-- <div class="d-flex justify-content-end">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="border p-1 w-120px text-center">FROM</span>
                    <span class="border p-1 w-120px text-center">TO</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="border p-1 w-120px text-center">04/16/2026</span>
                    <span class="border p-1 w-120px text-center">04/30/2026</span>
                </div>
            </div>
        </div> --}}

        <div class="d-flex justify-content-end">
            <table class="table table-sm table-bordered w-300px border-bottom">
                <thead>
                    <tr>
                        <th class="text-center">FROM</th>
                        <th class="text-center">TO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $dateValues['from'] ?? ''; }}</td>
                        <td>{{ $dateValues['to'] ?? ''; }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- table data container -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm mt-2">
                <thead>
                    <tr class="th-custom-bg">
                        <th class="text-white">DATE</th>
                        @if(isset($expenseCategory) && $expenseCategory)
                            @foreach($expenseCategory as $key => $val)
                            <th class="text-white" title="{{ $val->name }}">{{ strtoupper($val->abbreviation ?? $val->name) }}</th>
                            @endforeach
                        @endif
                        <th class="text-white">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $overAllTotal = 0;
                        $colTotals = array_fill(0, count($expenseCategory), 0);
                    @endphp
                    @if(isset($expenseData) && $expenseData)
                        @foreach($expenseData as $key => $val)
                            <tr>
                                <td>{{ $key }}</td>
                                @php $colTotal = 0; @endphp
                                @if(isset($expenseCategory) && $expenseCategory)
                                    @foreach($expenseCategory as $key2 => $val2)
                                        @php
                                            $amount = (double) ($val[$key2]['amount'] ?? 0);
                                            $colTotal += $amount;
                                            $colTotals[$key2] += $amount;
                                        @endphp
                                        <td class="text-end">{{ number_format($amount, 2) }}</td>
                                    @endforeach
                                    @php
                                        $overAllTotal += $colTotal;
                                    @endphp
                                    <td class="text-end">{{ number_format($colTotal, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td class="border-0"></td>
                        @if(isset($expenseCategory) && $expenseCategory)
                            @foreach($expenseCategory as $key => $val)
                                <td class="border-bottom bg-light text-end">
                                    {{ number_format($colTotals[$key], 2) }}
                                </td>
                            @endforeach
                        @endif
                        <td class="border-bottom bg-light text-end">
                            {{ number_format($overAllTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>


    </div>
</div>