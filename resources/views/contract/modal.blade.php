<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-12 mb-2">
                        <label for="client_vendor_id" class="form-label">Client</label>
                        <select class="form-select select-form" id="client_vendor_id" name="client_vendor_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="service_id" class="form-label">Service</label>
                        <select class="form-select select-form" id="service_id" name="service_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="contract_no" class="form-label">Contract Number</label>
                        <input type="text" class="form-control" id="contract_no" name="contract_no">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="_value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="_value" name="_value">
                        <input type="hidden" class="form-control" id="value" name="value">
                    </div>
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-3">
                    <button type="button" class="btn btn-success" id="saveButton">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
