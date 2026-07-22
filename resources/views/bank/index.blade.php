@extends('layouts.app')

@section('title') Banks @endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Banks</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item">Libraries</li>
                        <li class="breadcrumb-item active">Banks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!--button container -->
    <div class="card">
        <div class="card-body">
            <button type="button" class="btn btn-primary btn-sm float-end"><i class="fa fa-plus me-1"></i> New Bank</button>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <livewire:Bank.FilterData />
        </div>
        <div class="card-body p-0">
            <livewire:Bank.BankData defer />
        </div>
    </div>
@endsection