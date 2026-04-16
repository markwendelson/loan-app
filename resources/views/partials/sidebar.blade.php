<li class="sidebar-main-title">
    <div>
      <h6>General</h6>
    </div>
  </li>
@can('view-dashboard')
<li class="sidebar-list">
    <i class="fa fa-thumb-tack"></i>
    <a class="sidebar-link sidebar-title link-nav" href="{{route('home')}}">
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
        </svg>
        <svg class="fill-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#fill-home')}}"></use>
        </svg>
        <span>Dashboard</span>
    </a>
</li>
@endcan

<li class="sidebar-main-title">
    <div>
        <h6 class="">Transaction</h6>
    </div>
</li>

@can('view-client')
<li class="sidebar-list">
    <i class="fa fa-thumb-tack"></i>
    <a class="sidebar-link sidebar-title" href="#">
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#stroke-project')}}"></use>
        </svg>
        <svg class="fill-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#fill-project')}}"></use>
        </svg>
        <span>Clients</span>
        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
    </a>
    <ul class="sidebar-submenu" style="display: none;">
        @can('view-client')
        <li><a href="{{route('client.index')}}">Client List</a></li>
        @endcan
        @can('create-client')
        <li><a href="{{route('client.create')}}">Client Register</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('view-all-loans')
<li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
    <a class="sidebar-link sidebar-title" href="#">
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#stroke-user')}}"></use>
        </svg>
        <svg class="fill-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#fill-user')}}"></use>
        </svg>
        <span>Loans</span>
        <div class="according-menu">
            <i class="fa fa-angle-right"></i>
        </div>
    </a>
        <ul class="sidebar-submenu" style="display: none;">
            @can('view-all-loans')
            <li><a href="{{ route('loan.index') }}">All Loans</a></li>
            @endcan
            @can('view-pending-approval')
            <li><a href="{{ route('loan.pending') }}">Pending Approval</a></li>
            @endcan
            @can('view-awaiting-disbursement')
            <li><a href="{{ route('loan.awaiting-disbursement') }}">Awaiting Disbursement</a></li>
            @endcan
            @can('view-loans-declined')
            <li><a href="{{ route('loan.declined') }}">Loans Declined</a></li>
            @endcan
            @can('view-loans-withdrawn')
            <li><a href="{{ route('loan.withdrawn') }}">Loans Withdrawn</a></li>
            @endcan
            <!-- <li><a href="{{ route('loan.closed') }}">Loans Closed</a></li> -->
            @can('create-loan')
            <li><a href="{{ route('loan.create') }}">Add Loan</a></li>
            @endcan
        </ul>
</li>
@endcan

@can('view-savings')
<li class="sidebar-list">
    <i class="fa fa-thumb-tack"></i>
    <a class="sidebar-link sidebar-title link-nav" href="{{route('savings.index')}}">
        {{-- <i class="text-white fa fa-money"></i> --}}
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#doller-return')}}"></use>
        </svg>
        {{-- <svg class="fill-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#fill-home')}}"></use>
        </svg> --}}
        <span>Savings</span>
    </a>
</li>
@endcan


<!-- Others Menu -->
<li class="sidebar-list">
    <i class="fa fa-thumb-tack"></i>
    <a class="sidebar-link sidebar-title" href="#">
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
        </svg>
        <svg class="fill-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#fill-home')}}"></use>
        </svg>
        <span>Others</span>
        <div class="according-menu ms-auto"><i class="fa fa-angle-right"></i></div>
    </a>

    <ul class="sidebar-submenu" style="display: none;">
        @can('view-walk-in-transaction')
        <li>
            <a href="{{ route('walkin.index') }}">
                <i class="fa fa-user-walking me-2"></i> Walk-In Transactions
            </a>
        </li>
        @endcan
        @can('view-check-encashment')
        <li>
            <a href="{{ route('check-encashments.index') }}">
                <i class="fa fa-money-check-alt me-2"></i> Check Encashment
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('expenses.index') }}">
                <i class="fa fa-money-check-alt me-2"></i> Expenses
            </a>
        </li>
    </ul>
</li>

<li class="sidebar-main-title">
    <div>
        <h6 class="">Reports</h6>
    </div>
</li>
<li class="sidebar-list">

    <a class="sidebar-link sidebar-title link-nav" href="{{route('expenses.report')}}">
        <svg class="stroke-icon">
            <use href="{{url('assets/svg/icon-sprite.svg#expense')}}"></use>
        </svg>
        <span>Expenses</span>
    </a>
</li>

<li class="sidebar-main-title">
    <div>
        <h6 class="">Settings</h6>
    </div>
</li>
<li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title" href="#">
    <i class="fa fa-list text-white"></i>&nbsp;
    <span>Libraries</span><div class="according-menu"><i class="fa fa-angle-right"></i></div></a>
    <ul class="sidebar-submenu" style="display: none;">
        @can('view-branch')
        <li><a href="/branches">Branch</a></li>
        @endcan
        @can('view-document-type')
        <li><a href="{{ route('document-type.index') }}">Document Types</a></li>
        @endcan
        <!-- <li><a href="/departments">Department</a></li> -->
        @can('view-job-title')
        <li><a href="/job-titles">Job Title</a></li>
        @endcan
        @can('view-user')
        <li><a href="{{route('user.index')}}">Users</a></li>
        @endcan
        @can('view-role')
        <li><a href="{{route('role.index')}}">Roles</a></li>
        @endcan
        @can('view-insurance-fee')
        <li><a href="{{route('insurance-fee-schedule.index')}}">Insurance Fees</a></li>
        @endcan
        @can('view-notary-fee')
        <li><a href="{{route('notary-fee-schedule.index')}}">Notary Fees</a></li>
        @endcan
        @can('view-loan-type')
        <li><a href="{{route('loan-type.index')}}">Loan Types</a></li>
        @endcan
        @can('view-loan-product')
        <li><a href="{{route('loan-product.index')}}">Loan Products</a></li>

        <li><a href="{{ route('service-fees.index') }}">Service Fees (Others)</a></li>

        @endcan
        {{-- <li><a href="/charge">Charges</a></li> --}}
        {{-- <li><a href="#">Repayment Methods</a></li> --}}
    </ul>
</li>
