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
                            <label for="unit_id" class="form-label">Maintenance No.</label>
                            <select class="form-select select-select" id="maintenance_id" name="maintenance_id">
                            </select>
                        </div>
                        <div class="col">
                            <label for="urgency" class="form-label">Urgency</label>
                            <select class="form-select select-select" id="urgency" name="urgency">
                                <option value="P4">P4</option>
                                <option value="P3">P3</option>
                                <option value="P2">P2</option>
                                <option value="P1">P1</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table">
                            <table class="table mb-0" id="tableItem">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="width:3%">#</th>
                                        <th scope="col" style="width:20%">Maintenance Item</th>
                                        <th scope="col">MRO Item</th>
                                        <th scope="col" style="width:12%">Uom</th>
                                        <th scope="col" style="width:10%">Qty</th>
                                        <th scope="col" style="width:15%">Price</th>
                                        <th scope="col" style="width:15%">Amount</th>
                                        <th scope="col" style="width:2%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td scope="col" colspan="6" class="text-end p-1 align-middle"><b>Total</b>
                                        </td>
                                        <td scope="col" class="p-1 align-middle">
                                            <input type="hidden" id="total" name="total" readonly>
                                            <input type="text" class="form-control" id="total_" name="total_"
                                                readonly>
                                        </td>
                                        <td scope="col" class="p-1 align-middle"></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" colspan="6" class="text-end p-1 align-middle"><b>Tax</b>
                                        </td>
                                        <td scope="col" class="p-1 align-middle">
                                            <input type="hidden" id="tax" name="tax" readonly>
                                            <input type="text" class="form-control" id="tax_" name="tax_"
                                                readonly>
                                        </td>
                                        <td scope="col" class="p-1 align-middle"></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" colspan="6" class="text-end p-1 align-middle"><b>Grand
                                                Total</b></td>
                                        <td scope="col" class="p-1 align-middle">
                                            <input type="hidden" id="grand_total" name="grand_total" readonly>
                                            <input type="text" class="form-control" id="grand_total_"
                                                name="grand_total_" readonly>
                                        </td>
                                        <td scope="col" class="p-1 align-middle"></td>
                                    </tr>
                                </tfoot>
                            </table>
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
