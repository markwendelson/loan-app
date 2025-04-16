@include('partials.__header')
@section('content')
<div class="notification-list">
    <h3>Unread Notifications</h3>

    @if($notifications->isEmpty())
        <p>No new notifications.</p>
    @else
        <ul>
            @foreach ($notifications as $notification)
                <li>
                    {{ $notification->message }}
                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection

@include('partials.__footer')