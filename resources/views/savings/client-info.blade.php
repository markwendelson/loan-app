<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>{{$client->details->fullname}}</h3>
                <span>{{$client->details->present_full_address}}</span>
            </div>
            <a href="{{route('client.ledger.principal', $client->id)}}" target="_blank" class="btn btn-sm btn-primary">Ledger</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h2 id="client_balance">Balance: {{$client->getBalance()}}</h2>
            @if(request()->routeIs('savings.transactions'))
            @canany(['deposit-transactions','withdraw-transactions','cash-transactions'])
            <a class="btn btn-primary float-end" href="{{ route('savings.transactions.create',['client_id' => $client->id]) }}"><i class="fa fa-plus"></i> New Transaction</a>
            @endcan
            @endif
        </div>
    </div>
</div>