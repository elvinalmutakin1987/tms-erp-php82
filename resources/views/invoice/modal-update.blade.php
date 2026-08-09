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
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-primary saveUpdateButton" id="saveUpdateButton1"
                        name="status" value="Save">Save</button>
                    <button type="button" class="btn btn-success saveUpdateButton" id="saveUpdateButton2"
                        name="status" value="Done">Done</button>
                    <button type="button" class="btn btn-light" id="cancelUpdateButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
