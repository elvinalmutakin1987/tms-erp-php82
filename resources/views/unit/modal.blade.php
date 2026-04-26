<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select select-select" id="type" name="type">
                                        @foreach ($typeunit as $key => $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="unit_brand_id" class="form-label">Brand</label>
                                    <select class="form-select select-select" id="unit_brand_id" name="unit_brand_id">

                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="unit_model_id" class="form-label">Model</label>
                                    <select class="form-select select-select" id="unit_model_id" name="unit_model_id">

                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="location_id" class="form-label">Location</label>
                                    <select class="form-select select-select" id="location_id" name="location_id">
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="registration_no" class="form-label">Plate Number</label>
                                    <input type="text" class="form-control" id="registration_no"
                                        name="registration_no" placeholder="KTxxxx">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="vehicle_no" class="form-label">Vehicle Number</label>
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                                        placeholder="TMSxxx">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="certificate_no" class="form-label">Certificate Number</label>
                                    <input type="text" class="form-control" id="certificate_no"
                                        name="certificate_no">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="mechine_no" class="form-label">Mechine Number</label>
                                    <input type="text" class="form-control" id="mechine_no" name="mechine_no">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="chassis_no" class="form-label">Chassis Number</label>
                                    <input type="text" class="form-control" id="chassis_no" name="chassis_no">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="code_access" class="form-label">Code Access</label>
                                    <input type="text" class="form-control" id="code_access" name="code_access">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="plr_no" class="form-label">PLR Number</label>
                                    <input type="text" class="form-control" id="plr_no" name="plr_no">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_crane" class="form-label">Exp. Crane</label>
                                    <input type="text" class="form-control datepicker" id="exp_crane"
                                        name="exp_crane">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_fuel_issue" class="form-label">Exp. Fuel Issue</label>
                                    <input type="text" class="form-control datepicker" id="exp_fuel_issue"
                                        name="exp_fuel_issue">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_tbst" class="form-label">Exp. TBST</label>
                                    <input type="text" class="form-control datepicker" id="exp_tbst"
                                        name="exp_tbst">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_pass_road_1" class="form-label">Exp. Pass Road 1</label>
                                    <input type="text" class="form-control datepicker" id="exp_pass_road_1"
                                        name="exp_pass_road_1">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_stnk" class="form-label">Exp. STNK</label>
                                    <input type="text" class="form-control datepicker" id="exp_stnk"
                                        name="exp_stnk">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_tax" class="form-label">Exp. Tax</label>
                                    <input type="text" class="form-control datepicker" id="exp_tax"
                                        name="exp_tax">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="exp_comm" class="form-label">Exp. Commissioning</label>
                                    <input type="text" class="form-control datepicker" id="exp_comm"
                                        name="exp_comm">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-success" id="saveButton">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
