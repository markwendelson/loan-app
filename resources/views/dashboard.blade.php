@extends('layouts.app')

@section('title') Dashboard @endsection

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
        @php
            $user = auth()->user();
            
            $rolePriority = [
                'admin',
                'supervisor',
                'teller',
                'staff',
                'user',
            ];

            $views = collect($rolePriority)
                ->filter(fn ($role) => $user->hasRole($role))
                ->map(fn ($role) => "partials.dashboard.$role")
                ->toArray();
        @endphp

        @includeFirst($views)
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
    <script src="{{url('assets/js/dashboard-filter.js')}}"></script>
@endpush