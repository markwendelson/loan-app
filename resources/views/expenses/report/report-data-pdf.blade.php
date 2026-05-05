
@extends('layouts.pdf')

@section('title') Expense Report @endsection

@section('content')

    @php
        $expenseData = json_decode($expenseReport->data, true) ?? [];

        $expenseCategories = $expenseCategories->toArray();
    @endphp

    <header class="header-color">
        <h1 class="mb-0">{{ env('APP_NAME') }}</h1>
        <h2 class="mb-0">EXPENSE REPORT</h2>
    </header>

    <main>
        <section>
            <div style="display: flex; justify-content: space-between;">
                <div class="w-300px">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User ID:</td>
                                <td>Prepared By:</td>
                                <td>Date Generated:</td>
                            </tr>
                            <tr>
                                <td>{{ $expenseReport->user_id ?? '' }}</td>
                                <td>{{ $expenseReport->user_name ?? '' }}</td>
                                <td>{{ date('Y-m-d', strtotime($expenseReport->created_at ?? '')) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section>
            <table style="width: 100%;border: none !important; border-collapse: collapse !important; margin: 0px !important">
                <tbody>
                    <tr>
                        <td class="p-0" style="text-align: left;">
                            <h3>Description</h3>
                            <div class="border p-1">{{ $expenseReport->description ?? '' }}</div>
                        </td>
                        <td style="width:565px !important"></td>
                        <td class="p-0" style="text-align: right">
                            <table class="table-bordered table w-300px">
                                <thead>
                                    <tr>
                                        <th class="text-center">FROM</th>
                                        <th class="text-center">TO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $expenseReport->date_from ?? '' }}</td>
                                        <td>{{ $expenseReport->date_to ?? '' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </section>

        <section>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-white">DATE</th>
                        @foreach($expenseCategories as $val)
                            <th class="text-white text-center" title="{{ $val['name'] }}">{{ strtoupper($val['abbreviation'] ?? $val['name']) }}</th>
                        @endforeach
                        <th class="text-white text-center">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $overAllTotal = 0;
                        $colTotals = array_fill(0, count($expenseCategories), 0);
                    @endphp
                    @if(isset($expenseData) && $expenseData)
                        @foreach($expenseData as $key => $val)
                            <tr>
                                <td>{{ $key }}</td>
                                @php $colTotal = 0; @endphp
                                @foreach($expenseCategories as $key2 => $val2)
                                    @php
                                        $amount = (double) ($val[$key2]['amount'] ?? 0);
                                        $colTotal += $amount;
                                        $colTotals[$key2 - 1] += $amount;
                                    @endphp
                                    <td class="text-end">{{ number_format($amount, 2) }}</td>
                                @endforeach
                                @php
                                    $overAllTotal += $colTotal;
                                @endphp
                                <td class="text-end">{{ number_format($colTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-0">
                        <td style="border: none !important;"></td>
                        @foreach($expenseCategories as $key => $val)
                            <td class="border bg-light text-end">
                                {{ number_format($colTotals[$key - 1], 2) }}
                            </td>
                        @endforeach
                        <td class="border bg-light text-end">
                            {{ number_format($overAllTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; {{ date('Y') . ' ' .   env('APP_NAME') }}. All rights reserved.</p>
    </footer>
@endsection