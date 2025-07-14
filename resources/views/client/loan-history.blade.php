@extends('layouts.app')

@push('css')
<style>
    .bg-default {
         background-color: #1f807f;
     }
     .bg-default th {
         color: white; /* Set white text for <th> elements */
         border: 1px solid white;
     }

     table > thead {
        text-align: center;
        vertical-align:middle !important;
     }

    .list-group-item:hover {
        cursor: pointer;
    }
 </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client | Loan History</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Loan History</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
        <div class="card-body">
            <div>Name: <span>{{$client->details->fullname}}</span></div>
            <div>Address: <span>{{$client->details->present_full_address}}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Loan History</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="loanHistory">
                        @foreach ($loans as $loan)
                        <li class="list-group-item d-flex justify-content-between align-items-start list-group-item-action">
                            <div class="ms-2 me-auto">
                                <div>{{$loan->loanProduct->name}}</div>
                                <div class="fw-bold">{{$loan->loan_no}}</div>
                                <div>{{$loan->loan_date}}</div>

                            </div>
                            @php
                                $loanStatus = \App\Enums\LoanStatus::from($loan->status);
                                switch ($loan->status) {
                                    case 1:
                                        $badge = 'badge-success';
                                        break;
                                    case 2:
                                        $badge = 'badge-danger';
                                        break;
                                    case 3:
                                        $badge = 'badge-secondary';
                                        break;
                                    default:
                                        $badge = 'badge-primary';
                                        break;
                                }
                            @endphp

                            <span class="badge {{$badge}} rounded-pill">{{$loanStatus->name}}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Loan Details</h4>
                </div>
                <div class="card-body"></div>
            </div>
        </div>
    </div>

@endsection

@push('js')

<script>
    $(document).ready(function () {
        var client_id = $("#client_id").val();
    });

    const listItems = document.querySelectorAll('#loanHistory .list-group-item');

    listItems.forEach(item => {
      item.addEventListener('click', () => {
        // Remove 'active' class from all items
        listItems.forEach(i => i.classList.remove('active'));

        // Add 'active' class to the clicked item
        item.classList.add('active');
      });
    });
  </script>

@endpush
