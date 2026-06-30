@extends('layouts.pdf')

@section('title') Daily Expense @endsection

@section('content')
    <header class="header-color">
        <h1 class="mb-0">{{ env('APP_NAME') }}</h1>
        <h2 class="mb-0">DAILY EXPENSE REPORT</h2>
    </header>

    <main>
        <section>
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:30%; vertical-align:top;">
                        <table class="table table-bordered" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td class="text-start">Date</td>
                                    <td class="text-start">{{ date('M d Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Branch</td>
                                    <td class="text-start">{{ auth()->user()->branch->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Processed By</td>
                                    <td class="text-start">{{ auth()->user()->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>

                    <td style="width:20%;"></td>
                    <td style="width:25%;"></td>
                </tr>
            </table>
        </section>

        <section>
            @php
                $total = 0;
            @endphp
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vendor / Payee</th>
                        <th>TIN</th>
                        <th>Description</th>
                        <th>Reference No.</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($dailyReportData) && $dailyReportData->isNotEmpty())
                        @foreach($dailyReportData as $key => $val)
                            <tr>
                                <td class="text-start">{{ $val->vendor_name }}</td>
                                <td class="{{ isset($val->tin) && $val->tin ? 'text-start' : 'text-center'; }}">{{ ($val->tin .' - ' . $val->tax_classification) ?? '-' }} </td>
                                <td class="text-start">{{ $val->description }}</td>
                                <td class="text-start">{{ $val->ref_no }}</td>
                                <td class="text-end">{{ $val->amount }}</td>
                                @php
                                    $total = bcadd($total, (string) ($val->amount ?? '0.00'), 2);
                                @endphp
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2">
                            <div style="display: table; width: 100%;">
                                <span class="text-start fw-semibold" style="display: table-cell;">Total:</span>
                                <span class="fw-semibold" style="display: table-cell; text-align: right;">{{ $total }}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>
@endsection