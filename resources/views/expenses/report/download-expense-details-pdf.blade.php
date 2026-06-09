@extends('layouts.pdf')

@section('content')
    <header class="header-color">
        <h1 class="mb-0">{{ env('APP_NAME') }}</h1>
        <h2 class="mb-0">EXPENSE DETAILS REPORT</h2>
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
                                    <td class="text-start">{{ date('M d Y', strtotime($expenseData['expense_date'])) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Branch</td>
                                    <td class="text-start">{{ $expenseData['branch_name'] ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Type</td>
                                    <td class="text-start">{{ $expenseData['category_name'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>

                    <td style="width:20%;"></td>

                    <td style="width:25%; vertical-align:top;">
                        <table class="table table-bordered" style="width:100%;">
                            <tbody>
                                <tr>
                                    <td class="text-start">Date Processed</td>
                                    <td class="text-start">{{ date('M d Y', strtotime($expenseData['created_at'])) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">Processed By</td>
                                    <td class="text-start">{{ $expenseData['user_name'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </section>

        <section>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vendor / Payee</th>
                        <th>Description</th>
                        <th>Reference No.</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($expenseData['expense_items']) && $expenseData['expense_items'])
                        @foreach($expenseData['expense_items'] as $key => $val)
                            <tr>
                                <td class="text-start">
                                    <p style="padding:0px !important;margin: 0px !important;">Name : {{ $val['vendor_name'] }}</p>
                                    <p style="padding:0px !important;margin: 0px !important;">TIN : {{ $val['vendor_tin'] }}</p>
                                </td>
                                <td class="text-start">{{ $val['description'] }}</td>
                                <td class="text-start">{{ $val['ref_no'] }}</td>
                                <td class="text-end">{{ $val['amount'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">
                            <div style="display: table; width: 100%;">
                                <span class="text-start fw-semibold" style="display: table-cell;">Total:</span>
                                <span class="fw-semibold" style="display: table-cell; text-align: right;">{{ $expenseData['amount'] }}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>
@endsection