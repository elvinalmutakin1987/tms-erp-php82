<style>
    #formUpdate .modal-body {
        overflow-y: auto !important;
        max-height: calc(100vh - 160px);
        scroll-behavior: auto;
    }
</style>


<!-- search modal -->
<div class="modal" id="formCreate" aria-labelledby="formCreateLabel1" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-create-header">
            </div>
            <div class="modal-body" id="modal-create-body">
                <form enctype="multipart/form-data" onsubmit="disableButton()">
                    @csrf
                    <input type="hidden" name="request_token" id="request_token">

                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <button type="button" class="btn btn-primary saveCreateButton" id="saveCreateBubtton1"
                        name="status" value="Save">Draft</button>
                    <button type="button" class="btn btn-success saveCreateButton" id="saveCreateBubtton2"
                        name="status" value="Open">Save</button>
                    <button type="button" class="btn btn-light" id="cancelCreateButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
