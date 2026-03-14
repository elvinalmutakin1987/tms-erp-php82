 <!-- search modal -->
 <div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
         <div class="modal-content">
             <div class="modal-header" id="modal-header">
             </div>
             <div class="modal-body">
                 <form enctype="multipart/form-data">
                     @csrf
                     <div class="col-md-12 mb-2">
                         <label for="part_number" class="form-label">Part Number</label>
                         <input type="text" class="form-control" id="part_number" name="part_number">
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="name" class="form-label">Name</label>
                         <input type="text" class="form-control" id="name" name="name">
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="unit_id" class="form-label">Unit</label>
                         <select class="form-select" id="unit_id" name="unit_id[]" data-placeholder="Choose unit"
                             multiple>
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
