<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="row mb-2">
                        <div class="col">
                            <label for="unit_id" class="form-label">Unit</label>
                            <select class="form-select select-select" id="unit_id" name="unit_id">
                            </select>
                        </div>
                        <div class="col">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control datepicker" id="date" name="date">
                        </div>
                        <div class="col">
                            <label for="inspector" class="form-label">Inspector</label>
                            <input type="text" class="form-control" id="inspector" name="inspector">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table">
                            <table class="table mb-0" id="tableItem">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="width:3%">#</th>
                                        <th scope="col">Item</th>
                                        <th scope="col" class="text-center" style="width:60px;">Condition</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Inspected By</th>
                                    </tr>
                                </thead>

                                <tbody id="tbody">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="5" required></textarea>
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
