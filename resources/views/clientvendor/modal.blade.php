<!-- search modal -->
<div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                    <div class="col-md-12 mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <input type="hidden" class="form-control" id="type" name="type">
                    </div>
                    <div class="col-md-12 mb-2" id="divLocation">
                        <label for="location_id" class="form-label">Project Location</label>
                        <select class="form-select select-select" id="location_id" name="location_id">
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="pic" class="form-label">PIC</label>
                        <input type="text" class="form-control" id="pic" name="pic">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="top" class="form-label">Term Of Payment</label>
                        <input type="number" class="form-control" id="top" name="top" placeholder="Day">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="bank" class="form-label">Bank</label>
                        <select class="form-select select-select" id="bank" name="bank">
                            <option value="" selected disabled></option>
                            @foreach ($bank as $d => $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="bank_account" class="form-label">Bank Account</label>
                        <input type="text" class="form-control" id="bank_account" name="bank_account">
                    </div>
                    <div class="col-md-12 mb-2" id="divLocation">
                        <label for="taxable" class="form-label">Taxable</label>
                        <select class="form-select select-select" id="taxable" name="taxable">
                            <option value="PKP">PKP</option>
                            <option value="Non PKP">Non PKP</option>
                        </select>
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
