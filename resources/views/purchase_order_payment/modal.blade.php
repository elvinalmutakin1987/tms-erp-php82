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
                            <div class="col-md-12 mb-2">
                                <label for="purchase_order_id" class="form-label">Purchase Order</label>
                                <select class="form-select select-select" id="purchase_order_id"
                                    name="purchase_order_id">
                                </select>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="vendor_name" class="form-label">Vendor</label>
                                <input type="hidden" id="client_vendor_id" name="client_vendor_id">
                                <input type="text" class="form-control" id="vendor_name" name="vendor_name" readonly>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="text" class="form-control" id="invoice_date" name="invoice_date"
                                    readonly>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="text" class="form-control" id="due_date" name="due_date" readonly>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="grand_total" class="form-label">Grand Total</label>
                                <input type="text" class="form-control" id="grand_total" name="grand_total" readonly>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="balance" class="form-label">Balance</label>
                                <input type="text" class="form-control" id="balance" name="balance" readonly>
                            </div>
                        </div>
                        <div class="col">

                            <div class="col-md-12 mb-2">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="total" class="form-label">Total</label>
                                <input type="hidden" class="form-control" id="total" name="total">
                                <input type="text" class="form-control" id="total_" name="total_">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="payment_path" class="form-label">Attachment</label>
                                <input class="form-control" type="file" id="payment_path" name="payment_path">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-secondary saveButton" id="saveButton1" name="status"
                        value="Draft">Draft</button>
                    <button type="button" class="btn btn-success saveButton" id="saveButton2" name="status"
                        value="Open">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
