@extends('layouts.app')

@push('css')
<style>
    .client-card-items {
        width: 220px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .client-card-items:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.2); /* Optional: Add shadow for a pop effect */
    }
</style>
@endpush


@section('content')


    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Clients List </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="index.html">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Client List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

	<!-- list container -->
	<div class = "container-fluid">
		<div class = "row">

			<!-- client list container -->
			<div class = "col-sm-12 col-md-12 col-lg-12">
				<div class = "card">
					<div class = "card-body client-list d-flex justify-content-start gap-3">
						@if(isset($client_data) && $client_data)
							@foreach($client_data as $val)
								@php
									$clietDetail = $val['client_details'];
								@endphp
							<a href = "{{url('client-edit/'. $val['client_code'])}}">
							<div class = "card client-card-items">
								<img src = "{{url('assets/images/avtar/3.jpg')}}" class = "card-img-top">
								<div class = "card-body text-center">
									<h5><b>{{$val['client_code']}}</b></h5>
									<p>{{$clietDetail['lname'] . ', ' . $clietDetail['fname'] . ' ' . $clietDetail['mname']}}</p>
								</div>
							</div>
							</a>
							@endforeach
						@endif

					</div>
				</div>

			</div>
		</div>
	</div>

@endsection
