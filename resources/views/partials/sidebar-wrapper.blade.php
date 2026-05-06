 <!--sidebar wrapper -->
 <div class="sidebar-wrapper" data-simplebar="true">
     <div class="sidebar-header">
         <div>
             <img src="assets/images/tms_logo.png" class="logo-icon" alt="logo icon">
         </div>
         <div>
             <h4 class="logo-text">PT. TMS</h4>
         </div>
         <div class="mobile-toggle-icon ms-auto"><i class='bx bx-x'></i>
         </div>
     </div>
     <!--navigation-->
     <ul class="metismenu" id="menu">
         @if (Auth::user()->hasRole('superadmin') ||
                 Auth::user()->hasAnyPermission([
                     'dashboard.equipment',
                     'dashboard.procurement',
                     'dashboard.survey',
                     'dashboard.finance',
                 ]))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class='bx bx-home-alt'></i>
                     </div>
                     <div class="menu-title">Dashboard</div>
                 </a>
                 <ul>
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('dashboard.equipment'))
                         <li> <a href="{{ route('dashboard', ['t' => 'equipment']) }}"><i
                                     class='bx bx-radio-circle'></i>Equipment</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('dashboard.procurement'))
                         <li> <a href="index2.html"><i class='bx bx-radio-circle'></i>Procurement</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('dashboard.survey'))
                         <li> <a href="index3.html"><i class='bx bx-radio-circle'></i>Survey</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('dashboard.safety'))
                         <li> <a href="index3.html"><i class='bx bx-radio-circle'></i>Safety</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('dashboard.finance'))
                         <li> <a href="index3.html"><i class='bx bx-radio-circle'></i>Finance</a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif
         @if (Auth::user()->hasRole('superadmin') ||
                 Auth::user()->hasAnyPermission([
                     'p2h',
                     'mechanical_inspection',
                     'maintenance',
                     'purchase_requisition',
                     'proforma_invoice',
                 ]))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="bx bx-wrench"></i>
                     </div>
                     <div class="menu-title">Equipment</div>
                 </a>
                 <ul>
                     <li>
                         <a href="{{ route('dailyreport.index') }}"><i class='bx bx-radio-circle'></i>Daily Report</a>
                     </li>
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('p2h'))
                         <li>
                             <a href="{{ route('p2h.index') }}"><i class='bx bx-radio-circle'></i>P2H</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('mechanical_inspection'))
                         <li>
                             <a href="{{ route('mechanicalinspection.index') }}"><i
                                     class='bx bx-radio-circle'></i>Mechanical
                                 Inspection</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('maintenance'))
                         <li>
                             <a href="{{ route('maintenance.index') }}"><i class='bx bx-radio-circle'></i>Repair &
                                 Maintenance</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchase_requisition'))
                         <li>
                             <a href="{{ route('purchaserequisition.index') }}"><i
                                     class='bx bx-radio-circle'></i>Purchase
                                 Requisition</a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('proforma_invoice'))
                         <li>
                             <a href="{{ route('proformainvoice.index') }}"><i class='bx bx-radio-circle'></i>Proforma
                                 Invoice</a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif
         <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon"><i class="bx bx-user-voice"></i>
                 </div>
                 <div class="menu-title">Survey</div>
             </a>
             <ul>
                 <li> <a href="app-emailbox.html"><i class='bx bx-radio-circle'></i>Progress Claim</a>
                 </li>
             </ul>
         </li>
         @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('unit_expired'))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="bx bx-plus-medical"></i>
                     </div>
                     <div class="menu-title">Safety</div>
                 </a>
                 <ul>
                     <li> <a href="{{ route('unitexpired.index') }}"><i class='bx bx-radio-circle'></i>Unit Expired</a>
                     </li>
                 </ul>
             </li>
         @endif
         @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchase_requisition_general'))
             <li>
                 <a href="{{ route('purchaserequisitiongeneral.index') }}">
                     <div class="parent-icon"><i class="bx bx-file"></i>
                     </div>
                     <div class="menu-title">Purchase Requisition</div>
                 </a>
             </li>
         @endif

         @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasAnyPermission(['request_quotation', 'purchase_order']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="bx bx-dollar-circle"></i>
                     </div>
                     <div class="menu-title">Procurement</div>
                 </a>
                 <ul>
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('request_quotation'))
                         <li>
                             <a href="{{ route('requestquotation.index') }}">
                                 <i class='bx bx-radio-circle'></i>Request Quotation
                             </a>
                         </li>
                     @endif
                     @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchase_order'))
                         <li>
                             <a href="{{ route('purchaseorder.index') }}">
                                 <i class='bx bx-radio-circle'></i>Purchase
                                 Order</a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif
         <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon"><i class="bx bx-cart"></i>
                 </div>
                 <div class="menu-title">Finance</div>
             </a>
             <ul>
                 <li> <a href="app-emailbox.html"><i class='bx bx-radio-circle'></i>CIC</a>
                 </li>
                 <li> <a href="app-emailbox.html"><i class='bx bx-radio-circle'></i>Invoice</a>
                 </li>
                 <li> <a href="app-emailbox.html"><i class='bx bx-radio-circle'></i>PO Payment</a>
                 </li>
                 <li> <a href="app-emailbox.html"><i class='bx bx-radio-circle'></i>Invoice Payment</a>
                 </li>
             </ul>
         </li>
         <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon"><i class="bx bx-coin-stack"></i>
                 </div>
                 <div class="menu-title">Master Data</div>
             </a>
             <ul>
                 <li> <a href="{{ route('service.index') }}"><i class='bx bx-radio-circle'></i>Service</a>
                 </li>
                 <li> <a href="{{ route('contract.index') }}"><i class='bx bx-radio-circle'></i>Contract</a>
                 </li>
                 <li> <a href="{{ route('unit.index') }}"><i class='bx bx-radio-circle'></i>Unit</a>
                 </li>
                 <li> <a href="{{ route('unitmodel.index') }}"><i class='bx bx-radio-circle'></i>Unit Model</a>
                 </li>
                 <li> <a href="{{ route('unitbrand.index') }}"><i class='bx bx-radio-circle'></i>Unit Brand</a>
                 </li>
                 <li> <a href="{{ route('unitrate.index') }}"><i class='bx bx-radio-circle'></i>Unit Rate</a>
                 </li>
                 <li> <a href="{{ route('location.index') }}"><i class='bx bx-radio-circle'></i>Location</a>
                 </li>
                 <li> <a href="{{ route('maintenanceitem.index') }}"><i class='bx bx-radio-circle'></i>Maintenance
                         Item</a>
                 </li>
                 <li> <a href="{{ route('mroitem.index') }}"><i class='bx bx-radio-circle'></i>MRO Item</a>
                 </li>
                 <li> <a href="{{ route('clientvendor.index') }}"><i class='bx bx-radio-circle'></i>Client & Vendor
                     </a>
                 </li>
             </ul>
         </li>
         @if (Auth::user()->hasRole('superadmin'))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon"><i class="bx bx-cog"></i>
                     </div>
                     <div class="menu-title">Setting</div>
                 </a>
                 <ul>
                     <li>
                         <a href="{{ route('user.index') }}"><i class='bx bx-radio-circle'></i>User</a>
                     </li>
                     <li>
                         <a href="{{ route('role.index') }}"><i class='bx bx-radio-circle'></i>Role</a>
                     </li>
                     <li> <a href="{{ route('permission.index') }}"><i class='bx bx-radio-circle'></i>Permission
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('approval_flow.index') }}"><i class='bx bx-radio-circle'></i>Approval
                             Flow</a>
                     </li>
                 </ul>
             </li>
         @endif
     </ul>
     <!--end navigation-->
 </div>
 <!--end sidebar wrapper -->
