<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
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
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control datepicker" id="date" name="date">
                        </div>
                        <div class="col">
                            <label for="client_vendor_id" class="form-label">Vendor</label>
                            <select class="form-select select-select" id="client_vendor_id" name="client_vendor_id">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="mechanic" class="form-label">Mechanic</label>
                            <input type="text" class="form-control" id="mechanic" name="mechanic">
                        </div>
                        <div class="col">
                            <label for="km_hm" class="form-label">Hour Meter</label>
                            <input type="hidden" class="form-control" id="hour_meter" name="hour_meter">
                            <input type="text" class="form-control" id="_hour_meter" name="_hour_meter">
                        </div>
                        <div class="col">
                            <label for="km_hm" class="form-label">KM/HM</label>
                            <input type="hidden" class="form-control" id="km_hm" name="km_hm">
                            <input type="text" class="form-control" id="_km_hm" name="_km_hm">
                        </div>
                    </div>
                    <div class="row mb-2">

                        <div class="col">
                            <label for="start" class="form-label">Start</label>
                            <input type="text" class="form-control timepicker" id="start" name="start">
                        </div>
                        <div class="col">
                            <label for="finish" class="form-label">Finish</label>
                            <input type="text" class="form-control timepicker" id="finish" name="finish">
                        </div>
                        <div class="col">
                            <label for="work_duration" class="form-label">Work Duration</label>
                            <input type="text" class="form-control" id="work_duration" name="work_duration" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col" id="div-table">
                            <table class="table mb-0" id="tableItem">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" style="width: 2%">#</th>
                                        <th scope="col" style="width: 20%">Action</th>
                                        <th scope="col">Item</th>
                                        <th scope="col" style="width:2%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <tr class="fixed-row">
                                        <td class="p-1 align-middle" style="width: 2%">
                                        </td>
                                        <td class="p-1 align-middle" style="width: 20%">
                                            <select class="form-select select-select" id="act" name="act">
                                                <option value="Repair" selected>Repair</option>
                                                <option value="Replace">Replace</option>
                                                <option value="Washing">Washing</option>
                                                <option value="Add">Add</option>
                                                <option value="Flushing">Flushing</option>
                                                <option value="Commisioning">Commisioning</option>
                                                <option value="Welding">Welding</option>
                                                <option value="Adjust">Adjust</option>
                                            </select>
                                        </td>
                                        <td class="p-1 align-middle">
                                            <select class="form-select select-select" id="main_item_id"
                                                name="main_item_id">
                                                @foreach ($maintenance_item as $d)
                                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-1 align-middle" style="width:2%">
                                            <div class="row row-cols-auto g-3">
                                                <div class="col">
                                                    <button type="button"
                                                        class="btn btn-lg btn-primary bx bx-plus mr-1"
                                                        id="addItemButton"></button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-3">
                    <button type="button" class="btn btn-success" id="saveButton">Save</button>
                    <button type="button" class="btn btn-light" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
