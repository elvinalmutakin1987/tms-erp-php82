<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="row mb-2">
                        <div class="col">
                            <label for="client_vendor_id" class="form-label">Vendor</label>
                            <select class="form-select select-select" id="client_vendor_id" name="client_vendor_id">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="quotation_path" class="form-label">Attachment</label>
                            <input class="form-control" type="file" id="quotation_path" name="quotation_path">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="5"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-success saveButton" id="saveButton">Save</button>
                    <button type="button" class="btn btn-light cancelButton" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
