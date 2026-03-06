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
                         <label for="action" class="form-label">Action</label>
                         <select class="form-select select-select" id="action" name="action"
                             data-placeholder="Choose unit">
                             <option value="Repair">Repair</option>
                             <option value="Replace">Replace</option>
                             <option value="Washing">Washing</option>
                             <option value="Add">Add</option>
                             <option value="Flushing">Flushing</option>
                             <option value="Commisioning">Commisioning</option>
                             <option value="Welding">Welding</option>
                             <option value="Adjust">Adjust</option>
                         </select>
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="name" class="form-label">Name</label>
                         <input type="text" class="form-control" id="name" name="name">
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
