<style>
    #formModal .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>

<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-2">
                        <div class="col">
                            <label for="unit_id" class="form-label">Unit</label>
                            <select class="form-select select-select" id="unit_id" name="unit_id">
                            </select>
                        </div>
                        <div class="col">
                            <label for="driver" class="form-label">Driver</label>
                            <input type="text" class="form-control" id="driver" name="driver">
                        </div>
                        <div class="col">
                            <label for="shift" class="form-label">Shift</label>
                            <select class="form-select select-select" id="shift" name="shift">
                                <option value="Day">Day</option>
                                <option value="Night">Night</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control datepicker" id="date" name="date">
                        </div>
                        <div class="col">
                            <label for="km_start" class="form-label">KM Start</label>
                            <input type="hidden" class="form-control" id="km_start" name="km_start">
                            <input type="text" class="form-control" id="_km_start" name="_km_start">
                        </div>
                        <div class="col">
                            <label for="km_finish" class="form-label">KM Finish</label>
                            <input type="hidden" class="form-control" id="km_finish" name="km_finish">
                            <input type="text" class="form-control" id="_km_finish" name="_km_finish">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table">
                            <table class="table mb-0" id="tableItem">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="width:3%">#</th>
                                        <th scope="col">Item</th>
                                        <th scope="col" class="text-center" style="width:60px;">Broken</th>
                                        <th scope="col">Defect Listed</th>
                                        <th scope="col">Action Taken</th>
                                    </tr>
                                </thead>

                                <tbody id="tbody">

                                </tbody>
                            </table>

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
