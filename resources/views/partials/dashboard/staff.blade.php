<div class="row">
    <!-- For Released -->
    <div class="col-xl-3 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <h4 class="mb-1">{{ $counts['for_released'] ?? 0 }}</h4>
                        <span class="f-light">For Released</span>
                    </div>
                    <div class="widget-round success position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-to-do') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- For Approval -->
    <div class="col-xl-3 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <h4 class="mb-1">{{ $counts['for_approval'] ?? 0 }}</h4>
                        <span class="f-light">For Approval</span>
                    </div>
                    <div class="widget-round secondary position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement of Account -->
    <div class="col-xl-3 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <h4 class="mb-1">{{ $counts['statement_of_account'] ?? 0 }}</h4>
                        <span class="f-light">Statement of Account</span>
                    </div>
                    <div class="widget-round warning position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Endlisted Clients -->
    <div class="col-xl-3 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <h4 class="mb-1">{{ $counts['endlisted_clients'] ?? 0 }}</h4>
                        <span class="f-light">Endlisted Clients</span>
                    </div>
                    <div class="widget-round danger position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Transaction -->
    <div class="col-xl-4 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <div class="d-flex gap-1 mb-2 filter-group" data-metric="loan_transaction">
                            <button class="btn btn-xs btn-primary p-1 filter-btn active" data-period="daily"
                                style="font-size: 10px;">Daily</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="weekly"
                                style="font-size: 10px;">Weekly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="monthly"
                                style="font-size: 10px;">Monthly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="yearly"
                                style="font-size: 10px;">Yearly</button>
                        </div>
                        <h4 class="mb-1 metric-value">{{ $counts['daily_loan_transaction'] ?? 0 }}</h4>
                        <span class="f-light">Loan Transaction</span>
                    </div>
                    <div class="widget-round info position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-charts') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Transaction -->
    <div class="col-xl-4 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <div class="d-flex gap-1 mb-2 filter-group" data-metric="payment_transaction">
                            <button class="btn btn-xs btn-primary p-1 filter-btn active" data-period="daily"
                                style="font-size: 10px;">Daily</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="weekly"
                                style="font-size: 10px;">Weekly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="monthly"
                                style="font-size: 10px;">Monthly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="yearly"
                                style="font-size: 10px;">Yearly</button>
                        </div>
                        <h4 class="mb-1 metric-value">{{ $counts['daily_payment_transaction'] ?? 0 }}</h4>
                        <span class="f-light">Payment Transaction</span>
                    </div>
                    <div class="widget-round success position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses -->
    <div class="col-xl-4 col-sm-6 box-col-6">
        <div class="card widget-1 widget-with-icon h-100">
            <div class="card-body"
                style="background-image: url('{{ url('assets/images/dashboard/widget-bg.png') }}'); background-size: cover; background-position: center;">
                <div class="widget-content position-relative w-100">
                    <div>
                        <div class="d-flex gap-1 mb-2 filter-group" data-metric="expense_transaction">
                            <button class="btn btn-xs btn-primary p-1 filter-btn active" data-period="daily"
                                style="font-size: 10px;">Daily</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="weekly"
                                style="font-size: 10px;">Weekly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="monthly"
                                style="font-size: 10px;">Monthly</button>
                            <button class="btn btn-xs btn-outline-primary p-1 filter-btn" data-period="yearly"
                                style="font-size: 10px;">Yearly</button>
                        </div>
                        <h4 class="mb-1 metric-value">{{ $counts['daily_expenses'] ?? 0 }}</h4>
                        <span class="f-light">Expenses</span>
                    </div>
                    <div class="widget-round primary position-absolute end-0 top-50 translate-middle-y">
                        <div class="bg-round">
                            <svg class="stroke-icon">
                                <use href="{{ url('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>