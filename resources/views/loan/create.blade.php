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
            <label for="Borrower">Borrower</label>
            <select name="borrower_id" id="borrower_id" class="form-control">
                <option value="" disabled>-Select-</option>
            </select>
        </div>
    </div>
</div>

@endsection