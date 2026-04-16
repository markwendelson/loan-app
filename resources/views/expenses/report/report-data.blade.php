
@push('css')
    <style>
        .w-150px {
            width: 200px !important;
        }

        .th-custom-bg {
            background-color: #006666 !important;
        }
    </style>
@endpush


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
            <table class="table table-sm table-bordered w-150px border-bottom">
                <thead>
                    <tr>
                        <th class="text-center">FROM</th>
                        <th class="text-center">TO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>04/16/2026</td>
                        <td>04/30/2026</td>
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
                        <th class="text-white">DESCRIPTION</th>
                        <th class="text-white">AIR & TRANS</th>
                        <th class="text-white">LOGDING</th>
                        <th class="text-white">FUEL</th>
                        <th class="text-white">PHONE</th>
                        <th class="text-white">MEALS & TIPS</th>
                        <th class="text-white">ENTMT</th>
                        <th class="text-white">OTHER</th>
                        <th class="text-white">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>04/16/2026</td>
                        <td>Flight to NYC</td>
                        <td>$450.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$450.00</td>
                    </tr>
                    <tr>
                        <td>04/17/2026</td>
                        <td>Hotel accommodation</td>
                        <td></td>
                        <td>$350.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$350.00</td>
                    </tr>
                    <tr>
                        <td>04/17/2026</td>
                        <td>Fuel charges</td>
                        <td></td>
                        <td></td>
                        <td>$75.50</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$75.50</td>
                    </tr>
                    <tr>
                        <td>04/18/2026</td>
                        <td>Mobile phone bill</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$85.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$85.00</td>
                    </tr>
                    <tr>
                        <td>04/18/2026</td>
                        <td>Lunch with client</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$65.25</td>
                        <td></td>
                        <td></td>
                        <td>$65.25</td>
                    </tr>
                    <tr>
                        <td>04/19/2026</td>
                        <td>Theater show</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$120.00</td>
                        <td></td>
                        <td>$120.00</td>
                    </tr>
                    <tr>
                        <td>04/20/2026</td>
                        <td>Office supplies</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$42.00</td>
                        <td>$42.00</td>
                    </tr>
                    <tr>
                        <td>04/21/2026</td>
                        <td>Taxi fare</td>
                        <td>$95.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$95.00</td>
                    </tr>
                    <tr>
                        <td>04/22/2026</td>
                        <td>Dinner expense</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$110.50</td>
                        <td></td>
                        <td></td>
                        <td>$110.50</td>
                    </tr>
                    <tr>
                        <td>04/23/2026</td>
                        <td>Return flight</td>
                        <td>$425.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$425.00</td>
                    </tr>
                </tbody>
                <tfooter >
                    <tr >
                        <td class="border-0"></td>
                        <td class="border-0"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                        <td class="border-bottom"></td>
                    </tr>
                </tfooter>
            </table>
        </div>


    </div>
</div>