<style>
    #formUpdate .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>


<!-- search modal -->
<div class="modal" id="formUpdate" aria-labelledby="formUpdateLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-update-header">
            </div>
            <div class="modal-body" id="modal-update-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <div class="row mb-2">
                        <div class="col">
                            <label for="update_contract_no" class="form-label">Contract No.</label>
                            <input type="text" class="form-control" id="update_contract_no" name="update_contract_no"
                                readonly>
                        </div>
                        <div class="col">
                            <label for="update_unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="update_unit" name="update_unit" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cut_off_date" class="form-label">Cut Of Date</label>
                            <input type="text" class="form-control datepicker" id="cut_off_date" name="cut_off_date">
                        </div>
                        <div class="col">
                            <label for="consolidation_date" class="form-label">Konsolidasi Data TMS & CMD</label>
                            <input type="text" class="form-control datepicker" id="consolidation_date"
                                name="consolidation_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="progress_claim_date" class="form-label">Kirim Progress Claim Approval</label>
                            <input type="text" class="form-control datepicker" id="progress_claim_date"
                                name="progress_claim_date">
                        </div>
                        <div class="col">
                            <label for="ops_received_date" class="form-label">Data Diterima Dari OPS</label>
                            <input type="text" class="form-control datepicker" id="ops_received_date"
                                name="ops_received_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="prof_inv_app_date" class="form-label">Proforma Invoice Approved</label>
                            <input type="text" class="form-control datepicker" id="prof_inv_app_date"
                                name="prof_inv_app_date">
                        </div>
                        <div class="col">
                            <label for="cic_request_date" class="form-label">CIC Request Date</label>
                            <input type="text" class="form-control datepicker" id="cic_request_date"
                                name="cic_request_date">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-primary saveUpdateButton" id="saveUpdateButton1"
                        name="status" value="Approved">Save</button>
                    <button type="button" class="btn btn-success saveUpdateButton" id="saveUpdateButton2"
                        name="status" value="Done">Proceed to Invoice</button>
                    <button type="button" class="btn btn-light" id="cancelUpdateButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
