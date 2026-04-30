<!-- search modal -->
<div class="modal" id="formReceive" aria-labelledby="formReceiveLabel" tabindex="-1">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
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
                    <button type="button" class="btn btn-warning receiveButton" id="receiveSaveButton1" name="status"
                        value="Received">Save</button>
                    <button type="button" class="btn btn-success receiveButton" id="receiveSaveButton2" name="status"
                        value="Done">Save & Close </button>
                    <button type="button" class="btn btn-light" id="cancelReceiveButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search modal -->
