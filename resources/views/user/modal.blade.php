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
                         <label for="username" class="form-label">Username</label>
                         <input type="text" class="form-control" id="username" name="username">
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="name" class="form-label">Name</label>
                         <input type="text" class="form-control" id="name" name="name">
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="email" class="form-label">Email</label>
                         <input type="email" class="form-control" id="email" name="email">
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="role_id" class="form-label">Role</label>
                         <select class="form-select" id="role_id" name="role_id">
                         </select>
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="password" class="form-label">Password</label>
                         <div class="input-group" id="show_hide_password">
                             <input type="password" class="form-control border-end-0" id="password" name="password"> <a
                                 href="javascript:;" class="input-group-text bg-transparent"><i
                                     class="bx bx-hide"></i></a>
                         </div>
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="password" class="form-label">Password Confirmation</label>
                         <div class="input-group" id="show_hide_password_confirmation">
                             <input type="password" class="form-control border-end-0" id="password_confirmation"
                                 name="password_confirmation"> <a href="javascript:;"
                                 class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
                         </div>
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="sign_path" class="form-label">Sign</label>
                         <input class="form-control" type="file" id="sign_path" name="sign_path">
                     </div>

                     <div class="col-md-12 mb-1">
                         <button type="button" class="btn btn-danger" id="deleteSignButton">Delete Sign</button>
                     </div>

                     <div class="col-md-12" id="divSignPath" style="display: none">
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
