@extends('layouts.app')

@section('title') Expenses Report @endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/vendors/daterange-picker.css') }}">
    <style>
        #custom-dropdown-menu {
            max-height: 500px !important;
            overflow-y: auto;
        }

        .w-300px {
            width: 300px !important;
        }

        .th-custom-bg {
            background-color: #006666 !important;
        }
    </style>
@endpush

@php
    //initial date for daterangepicker
    $dateNow = date('m/d/Y');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Expenses</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <form id="frmFilter">
            <div class="card">
                <div class="card-body d-flex flex-wrap justify-content-end align-items-center gap-2">
                    <input type="text" class="form-control expenseDate" value="{{ $dateNow }} - {{ $dateNow }}" style="width: 250px;" name="expense_date">

                    <div class="dropdown">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" >
                            Select Categories &nbsp; <i class="fa fa-arrow-down"></i>
                        </button>
                        <ul class="dropdown-menu" style="width: 300px !important" id="custom-dropdown-menu">
                            @if(isset($expenseCategories) && $expenseCategories)
                                <li class="d-flex align-items-center p-1">
                                    <div class="form-check checkbox checkbox-primary mb-0">
                                        <input class="form-check-input mainAll" id="mainAll" type="checkbox" checked="" value="">
                                        <label class="form-check-label mb-1" for="mainAll"><small>All</small></label>
                                    </div>
                                </li>
                                @foreach($expenseCategories as $key => $val)
                                    <li class="d-flex align-items-center p-1">

                                        <div class="form-check checkbox checkbox-primary mb-0">
                                            <input class="form-check-input chckAll" id="chck{{ $val->id }}" type="checkbox" checked="" value="{{ $val->id }}" name="expense_categories[]">
                                            <label class="form-check-label mb-1" for="chck{{ $val->id }}"><small>{{ $val->name ?? ''; }}</small></label>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary btnGenerateReport"><i class="fa fa-repeat"></i>&nbsp; Generate Report</button>
                </div>
            </div>
        </form>
    </div>

    <div class="report-container container-fluid">
        {{-- @include('expenses.report.report-data'); --}}
    </div>
@endsection

@push('js')
    <script src="{{ url('assets/js/datepicker/date-picker/datepicker.js') }}"></script>

    <script>
        $(document).ready(function() {
            //handle all checkbox behaviour
            $(".mainAll").on("change", function() {
                if ($(this).is(":checked")) {
                    $(".chckAll").prop("checked", true);
                } else {
                    $(".chckAll").prop("checked", false);
                }
            });

            //handle categories checkbox behaviour
            $(".chckAll").on("change", function() {
                let total = $(".chckAll").length;
                let checked = $(".chckAll:checked").length;

                if (checked === total) {
                    $(".mainAll").prop("checked", true);
                } else {
                    $(".mainAll").prop("checked", false);
                }
            });

            $('.expenseDate').daterangepicker({
                opens: 'right',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Cancel',
                    format: 'MM/DD/YYYY'
                },
                singleDatePicker: false,
            });

            // Event triggered when "Apply" is clicked
            $('.expenseDate').on('apply.daterangepicker', function(ev, picker) {
                let start = picker.startDate.format('MM/DD/YYYY');
                let end = picker.endDate.format('MM/DD/YYYY');

                $(this).val(`${start} - ${end}`);

                console.log('Selected range:', start, 'to', end);
            });
        });

        $(".btnGenerateReport").click(function(e) {
            let formData = $("#frmFilter").serializeArray();

            $(".loader-wrapper").fadeIn("slow");

            $.ajax({
                type: "GET",
                url: "{{ route('expense.get-data-report') }}",
                data: formData,
                dataType: "json",
                success: function (response) {
                    //hide loading form
                    $(".loader-wrapper").fadeOut("slow", function () {
                        $(this).hide();
                    });

                    if(response.html !== 'undefined' && response.html) {
                        $('.report-container').html(response.html);
                    }
                }
            });

            return console.log(formData);
        })
    </script>
@endpush