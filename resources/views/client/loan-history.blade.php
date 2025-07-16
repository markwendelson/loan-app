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

    ul > li > span {
        float: right;
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
            <input type="hidden" name="client_id" id="client_id" value="{{$client->id}}" />
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
                        <li class="list-group-item d-flex justify-content-between align-items-start list-group-item-action" data-id="{{$loan->id}}">
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
            <div class="card">
                <div class="card-header">
                    <h4>Loan Details</h4>
                </div>
                <div class="card-body">
                    <div id="loanDetails"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Loan Ledger</h4>
                </div>
                <div class="card-body">
                    <div id="loanLedger"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')

<script>
    $(document).ready(function () {

        const listItems = document.querySelectorAll('#loanHistory .list-group-item');

        listItems.forEach(item => {
          item.addEventListener('click', () => {
            // Remove 'active' class from all items
            listItems.forEach(i => i.classList.remove('active'));

            // Add 'active' class to the clicked item
            item.classList.add('active');

            // Get data attribute (e.g., data-id or data-loan-id)
            const loanId = item.dataset.id;

            fetchLoanDetails(loanId)
            fetchLoanLedger(loanId)


          });
        });
    });

    function fetchLoanDetails(loanId) {
        var client_id = $("#client_id").val();
        const targetDiv = document.getElementById('loanDetails'); // target div to inject content

        let url = "{{ route('client.loan-history-details', ':client') }}";
        url = url.replace(':client', client_id);

        // Append loan_id as a query param
        url += `?loan_id=${encodeURIComponent(loanId)}`;

        // Send AJAX request using Fetch (GET) Loan Details
        fetch(url, {
            method: 'GET',
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text(); // because it's HTML
        })
        .then(html => {
            targetDiv.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            targetDiv.innerHTML = '<div class="alert alert-danger">Failed to load details.</div>';
        });
    }

    function fetchLoanLedger(loanId) {
        var client_id = $("#client_id").val();
        const targetDiv = document.getElementById('loanLedger'); // target div to inject content

        let url = "{{ route('client.partial-ledger', ':client') }}";
        url = url.replace(':client', client_id);

        // Append loan_id as a query param
        url += `?loan_id=${encodeURIComponent(loanId)}`;

        // Send AJAX request using Fetch (GET) Loan Details
        fetch(url, {
            method: 'GET',
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text(); // because it's HTML
        })
        .then(html => {
            targetDiv.innerHTML = html;

            var loan = @json($loan);

            const principal = $('#tblLedger').DataTable({
                dom: 'rtip',
                autoWidth: true,
                ordering: false,
                processing: true,
                serverSide: true,
                stateSave: true,
                searching: true,
                ajax: {
                    url: `{{ route('client.ledger.loan', [':client', ':loan_no']) }}`
                        .replace(':client', client_id)
                        .replace(':loan_no', loan.loan_no),
                },
                columns: [
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'withdraw_date', name: 'withdraw_date' },
                    { data: 'salary_month', name: 'salary_month' },
                    { data: 'loan_principal', name: 'loan_principal' },
                    { data: 'loan_interest', name: 'loan_interest' },
                    { data: 'loan_service_fee', name: 'loan_service_fee' },
                    { data: 'loan_insurance_fee', name: 'loan_insurance_fee' },
                    { data: 'others', name: 'others' },
                    { data: 'net_proceed', name: 'net_proceed' },
                    { data: 'signature', name: 'signature' },
                    { data: 'wdl', name: 'wdl' },
                    { data: 'payment_interest', name: 'payment_interest' },
                    { data: 'payment_interest_month', name: 'payment_interest_month' },
                    { data: 'payment_insurance', name: 'payment_insurance' },
                    { data: 'payment_insurance_month', name: 'payment_insurance_month' },
                    { data: 'payment_principal', name: 'payment_principal' },
                    { data: 'change', name: 'change' },
                    { data: 'balance', name: 'balance' },
                    { data: 'remarks', name: 'remarks' },
                ],
                columnDefs: [
                    // { className: "text-center", targets: [4,5] },
                ]
            });

        })
        .catch(error => {
            console.error('Error:', error);
            targetDiv.innerHTML = '<div class="alert alert-danger">Failed to load details.</div>';
        });
    }


  </script>

@endpush
