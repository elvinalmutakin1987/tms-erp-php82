<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-12 mb-2">
                        <label for="client_vendor_id" class="form-label">Client</label>
                        <select class="form-select select-select" id="client_vendor_id" name="client_vendor_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="service_id" class="form-label">Service</label>
                        <select class="form-select select-select" id="service_id" name="service_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="contract_no" class="form-label">Contract Number</label>
                        <input type="text" class="form-control" id="contract_no" name="contract_no">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="_value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="_value" name="_value">
                        <input type="hidden" class="form-control" id="value" name="value">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                    <div class="col-md-12 mb-2" id="div-rate">
                        <label for="_value" class="form-label">Service Rate</label>
                        <table class="table mb-0" id="tableItem">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" style="width:3%">#</th>
                                    <th scope="col" style="width:25%">Item No.</th>
                                    <th scope="col">Description</th>
                                    <th scope="col" style="width:25%">Rate (Rp.)</th>
                                    <th scope="col" style="width:5%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tableItem">
                                <tr class="fixed-row">
                                    <td class="p-1 align-middle">

                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="text" class="form-control" id="_item_no" name="_item_no">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="text" class="form-control" id="_description"
                                            name="_description">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="hidden" class="form-control" id="_rate_t" name="_rate_t">
                                        <input type="text" class="form-control" id="_rate_t_" name="_rate_t_">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <div class="row row-cols-auto g-3">
                                            <div class="col">
                                                <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                                                    id="addItemButton"></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mb-2" id="div-target">
                        <label for="_value" class="form-label">Unit Rate</label>
                        <table class="table mb-0" id="tableTarget">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" style="width:3%">#</th>
                                    <th scope="col" style="width:25%">Unit</th>
                                    <th scope="col">Target PA (%)</th>
                                    <th scope="col" style="width:25%">Price (Rp.)</th>
                                    <th scope="col" style="width:5%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tableTarget">
                                <tr class="fixed-row">
                                    <td class="p-1 align-middle">

                                    </td>
                                    <td class="p-1 align-middle">
                                        <select class="form-select select-select" id="unit" name="unit">
                                        </select>
                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="hidden" class="form-control" id="target_" name="target_">
                                        <input type="text" class="form-control" id="_target_" name="_target_">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="hidden" class="form-control" id="price_" name="price_">
                                        <input type="text" class="form-control" id="_price_" name="_price_">
                                    </td>
                                    <td class="p-1 align-middle" style="width:2%">
                                        <div class="row row-cols-auto g-3">
                                            <div class="col">
                                                <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                                                    id="addTargetButton"></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mb-2" id="div-fmf">
                        <label for="_value" class="form-label">Fix Monthly Fee</label>
                        <table class="table mb-0" id="tableFmf">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" style="width:3%">#</th>
                                    <th scope="col">Year</th>
                                    <th scope="col" style="width:25%">Value (Rp.)</th>
                                    <th scope="col" style="width:5%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tableFmf">
                                <tr class="fixed-row">
                                    <td class="p-1 align-middle">

                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="number" class="form-control" id="_year" name="_year">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <input type="hidden" class="form-control" id="fmf_value" name="fmf_value">
                                        <input type="text" class="form-control" id="_fmf_value"
                                            name="_fmf_value">
                                    </td>
                                    <td class="p-1 align-middle">
                                        <div class="row row-cols-auto g-3">
                                            <div class="col">
                                                <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                                                    id="addFmfButton"></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
