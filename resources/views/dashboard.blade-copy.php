@extends('layouts.app')

@section('content')

    @if (isset($notifications) && $notifications->isNotEmpty())
        <ul>
            @foreach ($notifications as $notification)
                <li>{{ $notification->data['message'] ?? 'No message available' }}</li>
            @endforeach
        </ul>
    @else
        {{-- <p>No new notifications.</p> --}}
    @endif

    <div class="container-fluid">
        <div class="page-title">
        <div class="row">
            <div class="col-6">
            <h4>Dashboard</h4>
            </div>
            <div class="col-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">
                    <svg class="stroke-icon">
                    <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                    </svg></a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            </div>
        </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
            <div class="card-header sales-chart card-no-border">
                <h4>Revenue Growth  </h4>
                <div class="sales-chart-dropdown">
                <ul class="balance-data">
                    <li> <span class="circle bg-warning"> </span><span class="ms-1 f-w-400">Disbursement</span></li>
                    <li>  <span class="circle bg-primary"> </span><span class="ms-1 f-w-400">Repayment</span></li>
                </ul>
                <div class="sales-chart-dropdown-select">
                    <div class="card-header-right-icon online-store">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle dropdown-toggle-store" id="dropdownMenuButtonToggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">This Year</button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButtonToggle"><span class="dropdown-item">Last Month</span><span class="dropdown-item">Last Week</span><span class="dropdown-item">Today   </span></div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                <div class="col-xxl-8 col-xl-12">
                    <div class="revenuegrowth">
                    <div class="revenuegrowth-chart" id="revenuegrowth"></div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 d-xxl-block d-none ">
                    <div class="revenuegrowth-details">
                    <div class="growth-details"><span class="f-light f-12  text-uppercase">Total Repayment</span>
                        <h4 class="f-w-500 mb-2">$56.265.08 </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                        <p class="mb-0 f-w-500 f-12">Compared to  </p><span class="f-light f-12 f-w-500 ">(+40.15% than)</span>
                        <p class="mb-0 f-w-500 f-12">last year </p>
                        </div>
                    </div>
                    <div class="growth-details"><span class="f-light f-12  text-uppercase">Total Disbursement </span>
                        <h4 class="f-w-500 mb-2">$42,256.26  </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
                        <p class="mb-0 f-w-500 f-12">Compared to </p><span class="txt-secondary f-12 f-w-500">(-20.25% than) </span>
                        <p class="mb-0 f-w-500 f-12">last year  </p>
                        </div>
                    </div>
                    <div class="growth-details"> <span class="f-light f-12  text-uppercase">Estimated Profit</span>
                        <h4 class="f-w-500 mb-2">$5,215.62  </h4>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                        <p class="mb-0 f-w-500 f-12">Compared to </p><span class=" f-w-500 f-light f-12">(+18.15% than)  </span>
                        <p class="mb-0 f-w-500 f-12">last year </p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6 box-col-6">
            <div class="card height-equal">
            <div class="card-header card-no-border total-revenue">
                <h4>Top Product </h4><a href="{{route('loan-product.index')}}">View All  </a>
            </div>
            <div class="card-body pt-0">
                <div class="top-product-card">
                <ul>
                    @foreach ($top_products as $top_product)
                    <li class="d-flex top-product gap-2">
                    <div><img class="img-fluid product-img" src="../assets/images/dashboard-3/product/1.png" alt="product"/></div>
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div class="product-details">
                        <div><span class="badge rounded-pill badge-light text-dark">{{$top_product->loanType->name}}</span></div><a class="f-10 f-w-500  line-clamp" href="{{route('loan-product.show',$top_product->id)}}">{{$top_product->name}}</a><span class="f-10 f-w-500 txt-primary"></span>
                        </div>
                        <div class="product-items">
                        <div class="common-space gap-1"><span class="f-10 f-w-500 f-light">QTY :</span><span class="f-10 f-w-500"></span></div>
                        <div class="common-space gap-1"><span class="f-10 f-w-500 f-light">Revenue : </span><span class="f-10 f-w-500  "></span></div>
                        <div class="common-space gap-1"><span class="f-10 f-w-500 f-light">Profit :</span><span class="f-10 f-w-500 "></span></div>
                        </div>
                    </div>
                    </li>
                    @endforeach
                </ul>
                </div>
            </div>
            </div>
        </div>

        <div class="col-xxl-6 col-sm-6 box-col-6">
            <div class="card height-equal">
            <div class="card-header card-no-border total-revenue pb-0">
                <h4>Team Activity </h4><a href="#">View All </a>
            </div>
            <div class="card-body pt-0">
                <div class="activity-table table-responsive custom-scrollbar">
                <table class="order-table overflow-hidden project-table w-100 activity-log">
                    <tbody>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/50.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Floyd Miles</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">5 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Floyd has moved to the warehouse.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/51.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Ralph Edwards</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">6 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Ralph has solved Mr.williams project.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/33.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Esther Howard</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">10 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Esther has changed his to active, now.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/52.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Jacob Jones</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">11 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Jacob has make changes in sold it.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/53.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Theresa Webb</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">12 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Theresa has complete old task and  new one.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="team-activity">
                            <div class="activity-data d-flex align-items-center gap-3">
                            <div class="common-space gap-2 ">
                                <div class="user-activity me-3"><img class="rounded-circle p-1 img-fluid me-3 img-50" src="../assets/images/user/54.png" alt="user"><a class="f-10 f-w-500 username" href="edit-profile.html">Annette Black</a></div>
                                <div class="activity-time"><span class="f-light f-w-500 f-10">12 min ago</span></div>
                            </div>
                            <div class="subtitle">
                                <p class="f-w-400 f-10">Annette has send all the stock to department.</p>
                            </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xxl-4 col-xl-6 box-col-6">
            <div class="card">
            <div class="card-header card-no-border total-revenue pb-0">
                <h4>User Visits by Day </h4>
                <div class="sales-chart-dropdown">
                <ul class="balance-data">
                    <li> <span class="circle bg-primary"></span><span class="f-light ms-1">Chrome </span></li>
                    <li><span class="circle bg-primary-1"> </span><span class="f-light ms-1">Firefox </span></li>
                    <li> <span class="circle bg-primary-2"> </span><span class="f-light ms-1">Safari</span></li>
                </ul>
                </div>
            </div>
            <div class="card-body pt-0 pb-0">
                <div class="user-visitsCharts">
                <div id="visitsCharts"> </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="common-space">
                <div><a class="f-w-600 f-14 " href="index.html">Most Visited Day</a><span class="f-light f-w-500 f-14 d-block">Total 59.6k visits on Sunday </span></div>
                <div class="visited-dropdown">
                    <svg class="mb-0">
                    <use href="../assets/svg/icon-sprite.svg#arrow-down"></use>
                    </svg>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xxl-5 col-xl-6 box-col-6">
            <div class="card">
            <div class="card-header card-no-border total-revenue">
                <h4>Latest Transaction</h4><a href="#">View All</a>
            </div>
            <div class="card-body pt-0">
                <div class="table-order table-responsive custom-scrollbar">
                <table class=" w-100 tranaction-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Darrell Steward</a></div>
                        </td>
                        <td class="f-14 f-w-500">16 Nov, 2024</td>
                        <td class="f-14 f-w-500">$456.23</td>
                        <td>
                        <div class="txt-primary"><span class="f-w-500 f-13">Complete</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Floyd Miles</a></div>
                        </td>
                        <td class="f-14 f-w-500">22 Jan, 2024</td>
                        <td class="f-14 f-w-500">$550.73</td>
                        <td>
                        <div class="txt-secondary"><span class="f-w-500 f-13">Failed</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Ralph Edwards</a></div>
                        </td>
                        <td class="f-14 f-w-500">31 Dec, 2024</td>
                        <td class="f-14 f-w-500">$785.26</td>
                        <td>
                        <div class="txt-primary"><span class="f-w-500 f-13">Complete</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Jerome Bell</a></div>
                        </td>
                        <td class="f-14 f-w-500">16 Nov, 2024</td>
                        <td class="f-14 f-w-500">$458.14</td>
                        <td>
                        <div class="txt-secondary"><span class="f-w-500 f-13">Failed</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Theresa Webb</a></div>
                        </td>
                        <td class="f-14 f-w-500">16 Feb, 2024</td>
                        <td class="f-14 f-w-500">$263.24</td>
                        <td>
                        <div class="txt-primary"><span class="f-w-500 f-13">Complete</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div class="product-name"><a class=" f-14 f-w-500" href="#">Courtney Henry</a></div>
                        </td>
                        <td class="f-14 f-w-500">01 Nov, 2024</td>
                        <td class="f-14 f-w-500">$785.14</td>
                        <td>
                        <div class="txt-primary"><span class="f-w-500 f-13">Complete</span></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6 box-col-6">
            <div class="card">
            <div class="card-header card-no-border total-revenue">
                <h4>Best Selling Product   </h4>
            </div>
            <div class="card-body pt-0">
                <div class="selling-product">
                <div class="apache-cotainer" id="echart-polar"> </div>
                <div class="sales-chart-dropdown">
                    <ul class="balance-data">
                    <li> <span class="circle bg-primary"></span><span class="f-light ms-1">Digital</span></li>
                    <li><span class="circle bg-warning"></span><span class="f-light ms-1">Clothes</span></li>
                    <li> <span class="circle bg-secondary"></span><span class="f-light ms-1">Electric</span></li>
                    <li> <span class="circle bg-light"> </span><span class="f-light ms-1">Footwear</span></li>
                    </ul>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->

@endsection

@push('js')
<script src="{{url('assets/js/chart/apex-chart/apex-chart.js')}}"></script>
<script src="{{url('assets/js/chart/apex-chart/stock-prices.js')}}"></script>
<script src="{{url('assets/js/chart/apex-chart/moment.min.js')}}"></script>
<script src="{{url('assets/js/chart/echart/esl.js')}}"></script>
<script src="{{url('assets/js/chart/echart/config.js')}}"></script>
<script src="{{url('assets/js/chart/echart/pie-chart/facePrint.js')}}"></script>
<script src="{{url('assets/js/chart/echart/pie-chart/testHelper.js')}}"></script>
<script src="{{url('assets/js/chart/echart/pie-chart/custom-transition-texture.js')}}"></script>
<script src="{{url('assets/js/chart/echart/data/symbols.js')}}"></script>
<script src="{{url('assets/js/dashboard/dashboard_3.js')}}"></script>
@endpush