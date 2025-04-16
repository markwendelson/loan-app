<li class="sidebar-main-title">
    <div>
      <h6>General</h6>
    </div>
  </li>
<li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title link-nav" href="{{route('home')}}">
        <svg class="stroke-icon">
            <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
        </svg>
        <svg class="fill-icon">
            <use href="../assets/svg/icon-sprite.svg#fill-home"></use>
        </svg>
        <span>Dashboard</span>
    </a>
</li>

<li class="sidebar-main-title">
    <div>
        <h6 class="">Transaction</h6>
    </div>
</li>

<li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title" href="#">
        <svg class="stroke-icon">
            <use href="../assets/svg/icon-sprite.svg#stroke-project"></use>
        </svg>
        <svg class="fill-icon">
            <use href="../assets/svg/icon-sprite.svg#fill-project"></use>
        </svg>
        <span>Client</span>
        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
    </a>
    <ul class="sidebar-submenu" style="display: none;">
        <li><a href="/client-list">Client List</a></li>
        <li><a href="/client-registration">Client Register</a></li>
    </ul>
</li>
<li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title" href="#">
    <svg class="stroke-icon">
        <use href="../assets/svg/icon-sprite.svg#stroke-user"></use>
    </svg>
    <svg class="fill-icon">
        <use href="../assets/svg/icon-sprite.svg#fill-user"></use>
    </svg>
    <span>Employee</span><div class="according-menu"><i class="fa fa-angle-right"></i></div></a>
    <ul class="sidebar-submenu" style="display: none;">
        <li><a href="/employee-list">Employee List</a></li>
        <li><a href="/employee-registration">Register</a></li>
    </ul>
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
        <li><a href="/branches">Branch</a></li>
        <li><a href="/departments">Department</a></li>
        <li><a href="/job-titles">Job Title</a></li>
        <li><a href="/insurance-fee">Insurance Fee</a></li>
        <li><a href="/notary-fee">Notary Fee</a></li>
        <li><a href="/loan-type">Loan Types</a></li>
        <li><a href="/loan-product">Loan Products</a></li>
        <li><a href="/charge">Charges</a></li>
        <li><a href="#">Repayment Methods</a></li>
    </ul>
</li>
