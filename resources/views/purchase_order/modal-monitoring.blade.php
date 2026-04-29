<!-- search modal -->
<div class="modal" id="formReceive" aria-labelledby="formReceiveLabel" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header" id="modal-receive-header">
            </div>
            <div class="modal-body" id="modal-receive-body">
                <form enctype="multipart/form-data">
                    <input type="hidden" name="request_token" id="request_token">
                    <div id="divReceive">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-md-flex d-grid align-items-center gap-3">
                    <button type="button" class="btn btn-warning monitorignButton" id="monitoringSaveButton"
                        name="status" value="Received">Save</button>
                    <button type="button" class="btn btn-light" id="cancelReceiveButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
