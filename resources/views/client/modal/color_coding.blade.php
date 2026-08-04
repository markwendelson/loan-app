<!-- Blank Bootstrap Modal -->
<div class="modal fade" id="modalColor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Customer Color Coding</h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="frmColor">
                    <!-- color coding id -->
                    <input type="hidden" name="id">

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5">
                                    <label class="w-100">
                                        Name <span class="txt-danger">*</span>
                                        <input type="text"
                                            class="form-control"
                                            name="color_name"
                                            placeholder="Name"
                                            required
                                        >
                                    </label>
                                </div>

                                <div class="col-5">
                                    <label class="w-100">
                                        Status <span class="txt-danger">*</span>
                                        <select class="form-select" name="status">
                                            <option value="">-- Status --</option>
                                            @foreach(config('constants.status_value') as $key => $val)
                                                <option value="{{$key}}">{{$val}}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <div class="col-2 d-flex align-items-center">
                                    <label class="w-100">
                                        Color <span class="txt-danger">*</span>
                                        <input type="color"
                                            class="form-control"
                                            name="color_code"
                                        >
                                    </label>
                                </div>

                                <div class="col-12">
                                    <label class="w-100">
                                        Description (optional)
                                        <textarea class="form-control"
                                            name="color_description"
                                            placeholder="Description"
                                        ></textarea>
                                    </label>
                                </div>

                                <!-- button container -->
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary float-end btnAddColorCode"><i class="fa fa-plus me-1"></i>Submit Color Coding</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <livewire:Client.ClientList.ColorCode.ColorCodeData defer />
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times me-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>