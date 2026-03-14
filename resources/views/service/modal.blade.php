 <!-- search modal -->
 <div class="modal" id="formModal" aria-labelledby="formModalLabel" tabindex="-1">
     <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
         <div class="modal-content">
             <div class="modal-header" id="modal-header">
             </div>
             <div class="modal-body">
                 <form enctype="multipart/form-data">
                     @csrf
                     <div class="col-md-12 mb-2">
                         <label for="type" class="form-label">Type</label>
                         <select class="form-select" id="type" name="type">
                             @foreach ($servicetype as $key => $value)
                                 <option value="{{ $value }}">{{ $value }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="col-md-12 mb-2">
                         <label for="name" class="form-label">Name</label>
                         <input type="text" class="form-control" id="name" name="name">
                     </div>
                     <div class="col-md-12 mb-2">
                         <table class="table mb-0" id="tableStep">
                             <thead class="table-light">
                                 <tr>
                                     <th scope="col" width="5%">#</th>
                                     <th scope="col" width="25%">Item No</th>
                                     <th scope="col">Description</th>
                                     <th scope="col" width="10%">Action</th>
                                 </tr>
                             </thead>
                             <tbody id='bodyTableStep'>
                                 <tr class="fixed-row">
                                     <td class="p-1 align-middle"></td>
                                     <td class="p-1 align-middle">
                                         <input type="text" class="form-control" id="txt_item_no" name="txt_item_no">
                                     </td>
                                     <td class="p-1 align-middle">
                                         <input type="text" class="form-control" id="txt_item_des"
                                             name="txt_item_des">
                                     </td>
                                     <td class="text-center p-1 align-middle">
                                         <div class="row row-cols-auto g-3">
                                             <div class="col">
                                                 <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                                                     id="addItemButton"></button>
                                             </div>
                                         </div>
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
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
