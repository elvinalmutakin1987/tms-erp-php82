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
                            <label for="purchase_requisition_id" class="form-label">Requisition No.</label>
                            <select class="form-select select-select" id="purchase_requisition_id"
                                name="purchase_requisition_id">
                                {{-- @foreach ($purchase_requisition as $d)
                                    <option value="{{ $d->id }}">{{ $d->requisiiton_no }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control datepicker" id="date" name="date">
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
