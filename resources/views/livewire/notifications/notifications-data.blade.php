<div>
    <div class="notification-box">
        <svg>
            <use href="{{url('assets/svg/icon-sprite.svg#notification-header')}}"></use>
        </svg>
        @if($notifications->count() > 0)
        <span class="badge rounded-pill badge-secondary">{{ $notifications->count() }}</span>
        @endif
    </div>
    <div class="onhover-show-div notification-dropdown" style="width: 350px !important;">
        <div class="card mb-0">
            <div class="card-header">
                <div class="common-space">
                    <h4 class="text-start f-w-600">Notifications</h4>
                    @if($notifications->count() > 0)
                    <div><span>{{$notifications->count() }} New</span></div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="notitications-bar">
                    <!-- Notifications Dropdown -->
                    <div class="notification-dropdown">
                        <ul class="nav nav-pills nav-primary p-0" id="pills-tab" role="tablist">
                            <li class="nav-item p-0"> <a class="nav-link active" id="pills-aboutus-tab" data-bs-toggle="pill" href="#pills-aboutus" role="tab" aria-controls="pills-aboutus" aria-selected="true">All({{ $notifications->count() }})</a></li>
                            <li class="nav-item p-0"> <a class="nav-link" id="pills-blog-tab" data-bs-toggle="pill" href="#pills-blog" role="tab" aria-controls="pills-blog" aria-selected="false">
                                Messages</a></li>
                        </ul>
                        <div class="tab-content overflow-y-auto" id="pills-tabContent" style="max-height: 400px !important;">
                            <div class="tab-pane fade show active" id="pills-aboutus" role="tabpanel" aria-labelledby="pills-aboutus-tab">
                                <div class="user-message"> 
                                    <ul> 
                                        @if ($notifications->count() > 0)
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
</div>
