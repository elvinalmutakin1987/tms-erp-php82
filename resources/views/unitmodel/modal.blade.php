 <!-- search modal -->
 <div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
         <div class="modal-content">
             <div class="modal-header" id="modal-header">
             </div>
             <div class="modal-body">
                 <form action="" onsubmit="disableButton()">
                     @csrf
                     <input type="hidden" name="request_token" id="request_token">

                     <div class="col-md-12 mb-2">
                         <label for="unit_brand_id" class="form-label">Brand</label>
                         <select class="form-select" id="unit_brand_id" name="unit_brand_id">
                         </select>
                     </div>
                     <div class="col-md-12">
                         <label for="desc" class="form-label">Description</label>
                         <input type="text" class="form-control" id="desc" name="desc">
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
