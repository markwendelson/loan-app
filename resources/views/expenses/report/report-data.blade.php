@extends('layouts.app')

@section('title') Expense Report @endsection

@push('css')
    <style>
        .th-custom-bg {
            background-color: #006666 !important;
        }

        .w-300px {
            width: 300px !important;
        }
    </style>
@endpush

@php
    use \Illuminate\Support\Str;
@endphp

@php
    $expenseData = json_decode($expenseReport->data, true) ?? [];
    $expenseCategories = $expenseCategories->toArray();
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="report-title d-flex justify-content-between align-items-center">
                <h3 class="txt-primary">{{ env('APP_NAME') }}</h3>
                <h4 class="txt-primary">EXPENSE REPORT</h4>
            </div>

            <div class="prepared-by">
                <div class="row">
                    <div class="col-lg-4">
                        <table class="table bordered">
                            <tbody>
                                <tr>
                                    <td>User ID</td>
                                    <td>Prepared By</td>
                                    <td>Date Generated</td>
                                </tr>
                                <tr>
                                    <td class="border">{{ $expenseReport->user_id ?? ''; }}</td>
                                    <td class="border">{{ $expenseReport->user_name ?? ''; }}</td>
                                    <td class="border">{{ date('Y-m-d', strtotime($expenseReport->created_at ?? '')) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>

            <div class="row justify-content-between">
                <div class="col-lg-8">
                    <span>Description</span>
                    <div class="border p-2">{{ $expenseReport->description ?? ''; }}</div>
                </div>
                <div class="col-lg-4 d-flex justify-content-end">
                    <table class="table table-sm table-bordered w-300px border-bottom">
                        <thead>
                            <tr>
                                <th class="text-center">FROM</th>
                                <th class="text-center">TO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $expenseReport->date_from ?? ''; }}</td>
                                <td>{{ $expenseReport->date_to ?? ''; }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>

            <!-- table data container -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm mt-2">
                    <thead>
                        <tr class="th-custom-bg">
                            <th class="text-white">DATE</th>
                            @if(isset($expenseCategories) && $expenseCategories)
                                @foreach($expenseCategories as $key => $val)
                                <th class="text-white text-center" title="{{ $val['name'] }}">{{ strtoupper($val['abbreviation'] ?? $val['name']) }}</th>
                                @endforeach
                            @endif
                            <th class="text-white text-center">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="border-bottom">
                        @php
                            $overAllTotal = 0;
                            $colTotals = array_fill(0, count($expenseCategories), 0);
                        @endphp
                        @if(isset($expenseData) && $expenseData)
                            @foreach($expenseData as $key => $val)
                                <tr>
                                    <td>{{ $key }}</td>
                                    @php $colTotal = 0; @endphp
                                    @if(isset($expenseCategories) && $expenseCategories)
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
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfooter>
                        <tr class="border-0">
                            <td class="border-0"></td>
                            @if(isset($expenseCategories) && $expenseCategories)
                                @foreach($expenseCategories as $key => $val)
                                    <td class="border bg-light text-end">
                                        {{ number_format($colTotals[$key - 1], 2) }}
                                    </td>
                                @endforeach
                            @endif
                            <td class="border bg-light text-end">
                                {{ number_format($overAllTotal, 2) }}
                            </td>
                        </tr>
                    </tfooter>
                </table>
            </div>


        </div>
    </div>
@endsection