@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Loan | Add Loan</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{url('')}}">
                        <svg class="stroke-icon">
                            <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                        </svg></a>
                    </li>
                    <li class="breadcrumb-item">Loan</li>
                    <li class="breadcrumb-item active">Add Loan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Add Loan</div>
    <div class="card-body">
        <div class="form-group">
            <label for="borrower_id">Borrower</label>
            <select name="borrower_id" id="borrower_id" class="form-control">
                <option value="" disabled>-Select-</option>
                @foreach ($clients as $client)
                    <option value="{{$client->id}}">{{$client->details->fullname}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="loan_product_id">Loan Product</label>
            <select name="loan_product_id" id="loan_product_id" class="form-control">
                <option value="" disabled>-Select-</option>
                @foreach ($loan_products as $loan_product)
                    <option value="{{$loan_product->id}}">{{$loan_product->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="principal">Principal</label>
            <input type="text" class="form-control" name="principal"/>
        </div>
        <div class="form-group">
            <label for="interest_method">Interest Method</label>
            <input type="text" class="form-control" name="interest_method"/>
        </div>
        <div class="form-group">
            <label for="interest_rate">Interest Rate</label>
            <input type="text" class="form-control" name="interest_rate"/>
        </div>
        <div class="form-group">
            <label for="release_date">Release Date</label>
            <input type="date" class="form-control" name="release_date"/>
        </div>
        <div class="form-group">
            <label for="interest_start_date">Interest Start Date</label>
            <input type="date" class="form-control" name="interest_start_date"/>
        </div>
        <div class="form-group">
            <label for="first_payment_date">First Payment Date</label>
            <input type="date" class="form-control" name="first_payment_date"/>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary float-end"><i class="fa fa-save"></i> Add Loan</button>
            </div>
        </div>
    </div>
</div>

@endsection