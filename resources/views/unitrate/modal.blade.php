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
                        <select class="form-select select-select" id="client_vendor_id" name="client_vendor_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="contract_id" class="form-label">Contract</label>
                        <select class="form-select select-select" id="contract_id" name="contract_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="unit_id" class="form-label">Unit</label>
                        <select class="form-select select-select" id="unit_id" name="unit_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="_rate" class="form-label">Rate</label>
                        <input type="text" class="form-control" id="_rate" name="_rate">
                        <input type="hidden" class="form-control" id="rate" name="rate">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="_target" class="form-label">Target PA (%)</label>
                        <input type="text" class="form-control" id="_target" name="_target">
                        <input type="hidden" class="form-control" id="target" name="target">
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
