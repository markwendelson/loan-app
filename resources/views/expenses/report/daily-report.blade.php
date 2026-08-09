@extends('layouts.pdf')

@section('title') {{Str::title($reportType)}} Expenses @endsection

@section('content')
    {{-- <header class="header-color">
        <h1 class="mb-0">{{ env('APP_NAME') }}</h1>
        <h2 class="mb-0">DAILY EXPENSE REPORT</h2>
    </header> --}}

    <main>
        <section>
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:30%; vertical-align:top;">
                        <table class="table table-bordered" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td class="text-start">{{ strtoupper(env('app_name')) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">SCHEDULE OF {{ strtoupper($reportType)}} EXPENSES</td>
                                </tr>
                                @if(isset($reportTypeDate) && $reportTypeDate)
                                <tr>
                                    <td class="text-start">{{strtoupper($reportTypeDate)}}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-start">{{$reportTypeYear}}</td>
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
                        <th>DATE</th>
                        <th>NAME OF SUPPLIER</th>
                        <th>BRANCH</th>
                        <th>TIN</th>
                        <th>VT/NVT</th> 
                        <th>OR NO./INVOICE NO.</th>
                        <th>CLASSIFICATION</th>
                        <th>INVOICE AMOUNT</th>
                        <th>VATABLE EXPENSES</th>
                        <th>WITHHOLDING TAX</th>
                        <th>VAT INPUT</th>
                        <th>PARTICULARS</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($reportData) && $reportData->isNotEmpty())
                        @foreach($reportData as $key => $val)
                            <tr>
                                <td>{{ date('d-M-y', strtotime($val->created_at)) }}</td>
                                <td class="text-start">{{ $val->vendor_name }}</td>
                                <td>{{$val->branch_name ?? ''}}</td>
                                <td class="{{ isset($val->tin) && $val->tin ? 'text-start' : 'text-center'; }}">{{ ($val->tin) ?? '-' }} </td>
                                <td>{{$val->tax_classification}}</td>
                                <td class="text-start">{{ $val->ref_no }}</td>
                                <td>{{$val->category_name ?? ''}}</td>
                                <td class="text-end">{{ $val->amount }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-start">{{ $val->description }}</td>
                                @php
                                    $total = bcadd($total, (string) ($val->amount ?? '0.00'), 2);
                                @endphp
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10"></td>
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