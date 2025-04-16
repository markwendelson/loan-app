
<!-- Page Header Start-->
<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
            <div class="Typeahead Typeahead--twitterUsers">
                <div class="u-posRelative">
                <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Riho .." name="q" title="" autofocus>
                <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading... </span></div><i class="close-search" data-feather="x"></i>
                </div>
                <div class="Typeahead-menu"> </div>
            </div>
            </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"> <a href="{{route('home')}}"><img class="img-fluid for-light" src="{{url('assets/images/logo/guiuan-logo.png')}}" alt="logo-light"><img class="img-fluid for-dark" src="{{url('assets/images/logo/guiuan-logo.png')}}" alt="logo-dark"></a></div>
            <div class="toggle-sidebar"> <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div> <a class="toggle-sidebar" href="#"> <i class="iconly-Category icli"> </i></a>
                <div class="d-flex align-items-center gap-2 ">
                    <h4 class="f-w-600">Welcome  Guest</h4><img class="mt-0" src="../assets/images/hand.gif" alt="hand-gif">
                </div>
            </div>
            <div class="welcome-content d-xl-block d-none"><span class="text-truncate col-12">Here’s what’s happening with your Lending today. </span></div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <li class="d-md-block d-none">
                    <div class="form search-form mb-0">
                    <div class="input-group"><span class="input-icon">
                        <svg>
                            <use href="../assets/svg/icon-sprite.svg#search-header"></use>
                        </svg>
                        <input class="w-100" type="search" placeholder="Search"></span></div>
                    </div>
                </li>
                <li class="d-md-none d-block">
                    <div class="form search-form mb-0">
                    <div class="input-group"> <span class="input-show">
                        <svg id="searchIcon">
                            <use href="../assets/svg/icon-sprite.svg#search-header"></use>
                        </svg>
                        <div id="searchInput">
                            <input type="search" placeholder="Search">
                        </div></span></div>
                    </div>
                </li>
                <li>
                    <div class="mode"><i class="moon" data-feather="moon"> </i></div>
                </li>
                <li class="onhover-dropdown notification-down">
                    <div class="notification-box">
                        <svg>
                            <use href="../assets/svg/icon-sprite.svg#notification-header"></use>
                        </svg><span class="badge rounded-pill badge-secondary">4 </span>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <div class="card mb-0">
                            <div class="card-header">
                                <div class="common-space">
                                    <h4 class="text-start f-w-600">Notitications</h4>
                                    <div><span>{{ isset($notifications) && $notifications ? $notifications->count() : '' }} New</span></div>
                                </div>
                            </div>
                            <div class="card-body">
                            <div class="notitications-bar">
                                <!-- Notifications Dropdown -->
                                <div class="notification-dropdown">
                                <div class="notification-dropdown">
                                    @if (isset($notifications) && $notifications->count() > 0)
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Notifications ({{ isset($notifications) && $notifications ? $notifications->count() : '' }})
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="notificationDropdown">
                                                @foreach ($notifications as $notification)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ $notification->data['updateLink'] }}">
                                                            {{ $notification->data['message'] }}
                                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Mark All as Read Button -->
                                        <form action="{{ route('notifications.markAsRead') }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-link">Mark All as Read</button>
                                        </form>
                                    @else
                                        <p>No new notifications</p>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                </li>

                <li class="profile-nav onhover-dropdown">
                    <div class="media profile-media">
                        <img class="b-r-10" src="{{ isset($profileImageUrl) && $profileImageUrl ? $profileImageUrl : url('/assets/images/dashboard/profile.png') }}" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 10px;">
                        <div class="media-body d-xxl-block d-none box-col-none">
                            <div class="d-flex align-items-center gap-2"> <span>{{ Auth::user()->name }}</span><i class="middle fa fa-angle-down"> </i></div>
                            <p class="mb-0 font-roboto">Admin</p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="#"><i data-feather="user"></i><span>My Profile</span></a></li>
                        <li> <a href="#"> <i data-feather="settings"></i><span>Settings</span></a></li>
                        <li><a class="btn btn-pill btn-outline-primary btn-sm" href="{{ url()->route('logout') }}">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">
                <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
                <div class="ProfileCard-details">
                    <div class="ProfileCard-realName">HARHAR</div>
                </div>
            </div>
        </script>
        <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>
<!-- Page Header Ends -->

