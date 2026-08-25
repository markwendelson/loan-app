<!-- Modal -->

@php
    $startYear = date('Y');
    $endYear = 2016;
@endphp

<div class="modal fade" id="modalGenerateReportOptions">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Generate Expense Report</h5>
            </div>
            <div class="modal-body">
                <form id="frmGenerateExpensesReport">
                    <div class="row">

                        <div class="col-lg-12">
                            Branches <span class="txt-danger">*</span>
                            <div class="card">
                                <div class="card-body row">
                                    @if(isset($branches) && $branches)
                                        @foreach($branches as $key => $val)
                                            @if(in_array($val->id, $allowedBranches))
                                                <div class="col-lg-6">
                                                    <div class="form-check checkbox checkbox-primary mb-0">
                                                        <input class="form-check-input" 
                                                            type="checkbox"
                                                            id="branch{{$val->id}}" 
                                                            name="branch[]"
                                                            value="{{$val->id}}"
                                                            checked
                                                        >
                                                        <label class="form-check-label mb-1" for="branch{{$val->id}}"><small>{{$val->name}}</small></label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <label class="w-100">
                                Report Type <span class="txt-danger">*</span>
                                <select class="form-select" name="report_type">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </label>
                        </div>

                        <!-- form report options -->
                        <div class="report-forms">
                            <!-- daily input -->
                            <div class="daily col-lg-12">
                                <label class="w-100">
                                    Date
                                    <input type="date"
                                        name="daily[date]"
                                        class="form-control"
                                    >
                                </label>
                            </div>

                            <!-- monthly input -->
                            <div class="monthly col-lg-12 d-none" style="height: auto !important;">
                                <div class="row justify-content-start">
                                    <div class="col-lg-6">
                                        <label class="w-100">
                                            From Month
                                            <input type="month"
                                                class="form-control"
                                                name="monthly[month_from]"
                                            >
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="w-100">
                                            To Month
                                            <input type="month"
                                                class="form-control"
                                                name="monthly[month_to]"
                                            >
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- quarterly input -->
                            <div class="quarterly col-lg-12 d-none">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="w-100">
                                            Quarter
                                            <select class="form-select" name="quarterly[quarter]">
                                                <option value="q1"> Q1 (Jan - Mar)</option>
                                                <option value="q2"> Q2 (Apr - Jun)</option>
                                                <option value="q3"> Q3 (Jul - Sept)</option>
                                                <option value="q4"> Q4 (Oct - Dec)</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="w-100">
                                            Year
                                            <select class="form-select" name="quarterly[year]">
                                                @for($x = $startYear; $x != $endYear; $x--)
                                                    <option value="{{ $x }}">{{ $x }}</option>
                                                @endfor
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- yearly -->
                            <div class="yearly col-lg-12 d-none">
                                <label class="w-100">
                                    Year
                                    <select class="form-select" name="yearly[year]">
                                        @for($x = $startYear; $x != $endYear; $x--)
                                            <option value="{{ $x }}">{{ $x }}</option>
                                        @endfor
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i>Generate Report</button>
                <button type="button" class="btn btn-secondary btnCancel"><i class="fa fa-times me-1"></i>Cancel</button>
            </div>  
        </div>
    </div>
</div>