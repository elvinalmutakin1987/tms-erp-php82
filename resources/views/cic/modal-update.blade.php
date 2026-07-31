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
                            <label for="invoice_no" class="form-label">Invoice No.</label>
                            <input type="text" class="form-control" id="invoice_no" name="invoice_no" readonly>
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
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_created_date" class="form-label">Pembuatan CIC</label>
                            <input type="text" class="form-control datepicker" id="cic_created_date"
                                name="cic_created_date">
                        </div>
                        <div class="col">
                            <label for="cic_received_date" class="form-label">Terima CIC</label>
                            <input type="text" class="form-control datepicker" id="cic_received_date"
                                name="cic_received_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="inv_date" class="form-label">Tanggal Invoice</label>
                            <input type="text" class="form-control datepicker" id="inv_date" name="inv_date">
                        </div>
                        <div class="col">
                            <label for="inv_create_date" class="form-label">Pembuatan Invoice</label>
                            <input type="text" class="form-control datepicker" id="inv_create_date"
                                name="inv_create_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_send_date" class="form-label">Kirim CIC Ke KPC</label>
                            <input type="text" class="form-control datepicker" id="cic_send_date"
                                name="cic_send_date">
                        </div>
                        <div class="col">
                            <label for="cic_ready_to_pick_date" class="form-label">Informasi CIC Bisa Diambil</label>
                            <input type="text" class="form-control datepicker" id="cic_ready_to_pick_date"
                                name="cic_ready_to_pick_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_pick_up_date" class="form-label">CIC Diambil TMS</label>
                            <input type="text" class="form-control datepicker" id="cic_pick_up_date"
                                name="cic_pick_up_date">
                        </div>
                        <div class="col">
                            <label for="inv_send_date" class="form-label">Invoice Terima KPC</label>
                            <input type="text" class="form-control datepicker" id="inv_send_date"
                                name="inv_send_date">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="cic_number" class="form-label">CIC Number</label>
                            <input type="text" class="form-control" id="cic_number" name="cic_number">
                        </div>
                        <div class="col">
                            <label for="cic_path" class="form-label">Attachment</label>
                            <input class="form-control" type="file" id="cic_path" name="cic_path">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col"></div>
                        <div class="col" id="div-file">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-primary saveUpdateButton" id="saveUpdateButton1"
                        name="status" value="Save">Save</button>
                    <button type="button" class="btn btn-success saveUpdateButton" id="saveUpdateButton2"
                        name="status" value="Done">Done</button>
                    {{-- <button type="button" class="btn btn-success saveUpdateButton" id="saveUpdateButton3"
                        name="status" value="CIC Approval">Proceed to Invoice</button> --}}
                    <button type="button" class="btn btn-light" id="cancelUpdateButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
