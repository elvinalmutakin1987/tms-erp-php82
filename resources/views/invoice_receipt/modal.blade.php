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

                    <div class="col-md-12 mb-2">
                        <label for="invoice_no" class="form-label">Invoice No.</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="invoice_date" class="form-label">Date</label>
                        <input type="text" class="form-control datepicker" id="invoice_date" name="invoice_date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="invoice_path" class="form-label">Attachment</label>
                        <input class="form-control" type="file" id="invoice_path" name="invoice_path">
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-file">

                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-secondary saveButton" id="saveButton1" name="status"
                        value="Waiting Invoice">Draft</button>
                    <button type="button" class="btn btn-success saveButton" id="saveButton2" name="status"
                        value="Unpaid">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
