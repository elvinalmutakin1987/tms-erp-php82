<style>
    #formEdit .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>


<!-- search modal -->
<div class="modal" id="formEdit" aria-labelledby="formEditLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-edit-header">
            </div>
            <div class="modal-body" id="modal-edit-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">
                    <div class="row mb-4">
                        <div class="col">
                            <label for="edit_year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="edit_year" name="edit_year" readonly>
                        </div>
                        <div class="col">
                            <label for="edit_month_name" class="form-label">Month</label>
                            <input type="hidden" class="form-control" id="edit_month" name="edit_month">
                            <input type="text" class="form-control" id="edit_month_name" name="edit_month_name"
                                readonly>
                        </div>
                        <div class="col">
                            <label for="edit_contract_id" class="form-label">Contract</label>
                            <input type="hidden" class="form-control" id="edit_contract_id" name="edit_contract_id">
                            <input type="text" class="form-control" id="edit_contract_no" name="edit_contract_no"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table-edit">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-secondary saveEditButton" id="saveEditButton1" name="status"
                        value="Draft">Draft</button>
                    <button type="button" class="btn btn-success saveEditButton" id="saveEditButton2" name="status"
                        value="Open">Save</button>
                    <button type="button" class="btn btn-light" id="cancelEditButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
