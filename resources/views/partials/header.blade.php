@push('css')
<style>
    .branch-dropdown {
        widows: 210px !important;
        left: 0 !important;
    }
</style>
@endpush

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
                    <h4 class="f-w-600">Welcome  {{ Auth::user()->name }}</h4><img class="mt-0" src="{{url('/assets/images/hand.gif')}}" alt="hand-gif">
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
                            <use href="{{url('assets/svg/icon-sprite.svg#search-header')}}"></use>
                        </svg>
                        <input class="w-100" type="search" placeholder="Search"></span></div>
                    </div>
                </li>
                <li class="d-md-none d-block">
                    <div class="form search-form mb-0">
                    <div class="input-group"> <span class="input-show">
                        <svg id="searchIcon">
                            <use href="{{url('assets/svg/icon-sprite.svg#search-header')}}"></use>
                        </svg>
                        <div id="searchInput">
                            <input type="search" placeholder="Search">
                        </div></span></div>
                    </div>
                </li>
                <li>
                    <div class="mode" data-test="harhar"><i class="moon" data-feather="moon"> </i></div>
                </li>
                <li class="onhover-dropdown notification-down">
                    <div class="notification-box">
                        <svg>
                            <use href="{{url('assets/svg/icon-sprite.svg#notification-header')}}"></use>
                        </svg><span class="badge rounded-pill badge-secondary">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    </div>
                    <div class="onhover-show-div notification-dropdown" style="width: 350px !important;">
                        <div class="card mb-0">
                            <div class="card-header">
                                <div class="common-space">
                                    <h4 class="text-start f-w-600">Notifications</h4>
                                    <div><span>{{ auth()->user()->unreadNotifications()->count() }} New</span></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="notitications-bar">
                                    <!-- Notifications Dropdown -->
                                    <div class="notification-dropdown">
                                        <ul class="nav nav-pills nav-primary p-0" id="pills-tab" role="tablist">
                                            <li class="nav-item p-0"> <a class="nav-link active" id="pills-aboutus-tab" data-bs-toggle="pill" href="#pills-aboutus" role="tab" aria-controls="pills-aboutus" aria-selected="true">All({{ auth()->user()->unreadNotifications()->count() }})</a></li>
                                            <li class="nav-item p-0"> <a class="nav-link" id="pills-blog-tab" data-bs-toggle="pill" href="#pills-blog" role="tab" aria-controls="pills-blog" aria-selected="false">
                                                Messages</a></li>
                                        </ul>
                                        <div class="tab-content overflow-y-auto" id="pills-tabContent" style="max-height: 400px !important;">
                                            <div class="tab-pane fade show active" id="pills-aboutus" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                                                <div class="user-message"> 
                                                    <ul> 
                                                        @php
                                                            $user = auth()->user();
                                                            $notifications = $user ? $user->unreadNotifications : collect();
                                                        @endphp

                                                        @if ($user && $notifications->count() > 0)
                                                            @foreach ($notifications as $key => $val)
                                                                <li>
                                                                    <div class="user-alerts">
                                                                        @php
                                                                            $photo = $val->data['user_image'];
                                                                            if ($photo && file_exists(public_path('storage/' . $photo))) {
                                                                                $photoUrl = asset('storage/' . $photo);
                                                                            } else {
                                                                                $photoUrl = asset('assets/images/user/user.png');
                                                                            }
                                                                        @endphp

                                                                        <img class="user-image rounded-circle img-fluid me-2" src="{{ $photoUrl }}" alt="user"/>
                                                                        <div class="user-name">
                                                                            <div> 
                                                                                <h6><a class="f-w-500 f-14" href="{{ $val->data['url'] }}">{{ $val->data['user_name']; }}</a></h6>
                                                                                <span class="f-light f-w-500 f-12">{{ $val->data['message'] }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-blog" role="tabpanel" aria-labelledby="pills-blog-tab">
                                                <div class="notification-card"> 
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 200px">
                                                        <h5>Coming Soon!...</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer pb-0 pr-0 pl-0"> 
                                            <div class="text-center"> <a class="f-w-700" href="private-chat.html">
                                                <button class="btn btn-primary" type="button" title="btn btn-primary">Check all</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="branch-nav onhover-dropdown" style="background: transparent">
                    <div class="media profile-media">
                        <img class="b-r-10" src="{{ url('/assets/images/user/6.jpg') }}" alt="Branch Picture" style="width: 40px; height: 40px; border-radius: 10px;">
                        <div class="media-body d-xxl-block d-none box-col-none">
                            <div class="d-flex align-items-center gap-2"> <span>Current Branch:</span><i class="middle fa fa-angle-down"> </i></div>
                            <p class="mb-0 font-roboto">{{ Auth::user()->roles()->first()->name ?? '[NO_ROLE]' }}</p>
                            <p class="mb-0 font-roboto">{{ Auth::user()->branch->name ?? '[NO_BRANCH]' }}</p>
                        </div>
                    </div>
                    <ul class="profile-dropdown branch-dropdown onhover-show-div">
                        @php
                            $userBranches = getUserBranch(Auth::user());
                        @endphp
                        @foreach ($userBranches as $branch)
                            <li><a href="javascript:void(0)" onclick="updateBranch({{ $branch->branch_id }})"><i data-feather="home"></i><span>{{ $branch->branch->name }}</span></a></li>
                        @endforeach
                    </ul>
                </li>

                <li class="profile-nav onhover-dropdown">
                    <div class="media profile-media d-flex align-items-center">
                        <img class="b-r-10" src="{{ Auth::user()->photo ? asset('storage/'.Auth::user()->photo) : url('/assets/images/user/6.jpg') }}" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 10px;">
                        <div class="media-body d-xxl-block d-none box-col-none">
                            <div class="d-flex align-items-center gap-2"> <span>{{ Auth::user()->name }}</span><i class="middle fa fa-angle-down"> </i></div>
                            {{-- <p class="mb-0 font-roboto">{{ Auth::user()->roles()->first()->name ?? '[NO_ROLE]' }}</p> --}}
                            {{-- <p class="mb-0 font-roboto">{{ Auth::user()->branch->name ?? '[NO_BRANCH]' }}</p> --}}
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="#"><i data-feather="user"></i><span>My Profile</span></a></li>
                        <li> <a href="#"> <i data-feather="settings"></i><span>Settings</span></a></li>
                        <li><a class="btn btn-pill btn-outline-primary btn-sm" href="{{ url()->route('custom.logout') }}">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Header Ends -->

