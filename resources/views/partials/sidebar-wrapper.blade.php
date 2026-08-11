 @php
     use App\Models\Approval_flow;
     use App\Models\Approval_process;
     use App\Models\Approval_status;
     use App\Models\Approval_step;
     use App\Models\Contract;
     use App\Models\Contract_fmf;
     use App\Models\Contract_rate;
     use App\Models\Proforma_invoice;
     use App\Models\Purchase_order;
     use App\Models\Purchase_order_payment;
     use App\Models\Purchase_requisition;
     use App\Models\Request_quotation;
     use App\Models\Unit_target;

     $approval_process = Approval_process::where('user_id', Auth::user()->id)
         ->where('action', 'Open')
         ->orderBy('id', 'desc')
         ->count();
 @endphp

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
         @php
             $user = Auth::user();
             $guardName = 'web';
             $menuPermissions = [
                 'dashboard' => [
                     'dashboard.operational',
                     'dashboard.maintenance',
                     'dashboard.procurement',
                     'dashboard.survey',
                     'dashboard.safety',
                     'dashboard.finance',
                 ],
                 'equipment' => [
                     'daily_report',
                     'p2h',
                     'mechanical_inspection',
                     'maintenance',
                     'purchase_requisition',
                     'proforma_invoice',
                 ],
                 'survey' => ['progress_claim'],
                 'safety' => ['unit_expired'],
                 'purchase_requisition_general' => ['purchase_requisition_general'],
                 'procurement' => ['request_quotation', 'purchase_order'],
                 'finance' => ['purchase_order_payment', 'invoice_receipt', 'cic', 'invoice', 'invoice_payment'],
                 'approval' => ['approval'],
                 'master_data' => [
                     'service',
                     'contract',
                     'unit',
                     'unit_model',
                     'unit_brand',
                     'unit_rate',
                     'location',
                     'maintenance_item',
                     'mro_item',
                     'client_vendor',
                 ],
             ];
             $allPermissions = collect($menuPermissions)->flatten()->unique()->values()->toArray();
             $existingPermissions = \Spatie\Permission\Models\Permission::whereIn('name', $allPermissions)
                 ->where('guard_name', $guardName)
                 ->pluck('name')
                 ->toArray();
             $permissionExists = function ($permission) use ($existingPermissions) {
                 return in_array($permission, $existingPermissions, true);
             };
             $can = function ($permission) use ($user, $permissionExists) {
                 return $user->hasRole('superadmin') ||
                     ($permissionExists($permission) && $user->hasPermissionTo($permission));
             };
             $canAny = function ($permissions) use ($user, $existingPermissions) {
                 if ($user->hasRole('superadmin')) {
                     return true;
                 }
                 $existingMenuPermissions = array_values(array_intersect($permissions, $existingPermissions));
                 return count($existingMenuPermissions) > 0 && $user->hasAnyPermission($existingMenuPermissions);
             };
         @endphp

         @if ($canAny($menuPermissions['dashboard']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class='bx bx-home-alt'></i>
                     </div>
                     <div class="menu-title">Dashboard</div>
                 </a>

                 <ul>
                     @if ($can('dashboard.operational'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'operational']) }}">
                                 <i class='bx bx-radio-circle'></i>Operational
                             </a>
                         </li>
                     @endif

                     @if ($can('dashboard.maintenance'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'maintenance']) }}">
                                 <i class='bx bx-radio-circle'></i>Repair & Maintenance
                             </a>
                         </li>
                     @endif

                     @if ($can('dashboard.procurement'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'procurement']) }}">
                                 <i class='bx bx-radio-circle'></i>Procurement
                             </a>
                         </li>
                     @endif

                     @if ($can('dashboard.survey'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'survey']) }}">
                                 <i class='bx bx-radio-circle'></i>Survey
                             </a>
                         </li>
                     @endif

                     @if ($can('dashboard.safety'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'safety']) }}">
                                 <i class='bx bx-radio-circle'></i>Safety
                             </a>
                         </li>
                     @endif

                     @if ($can('dashboard.finance'))
                         <li>
                             <a href="{{ route('dashboard', ['t' => 'finance']) }}">
                                 <i class='bx bx-radio-circle'></i>Finance
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($canAny($menuPermissions['equipment']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-wrench"></i>
                     </div>
                     <div class="menu-title">Equipment</div>
                 </a>

                 <ul>
                     @if ($can('daily_report'))
                         <li>
                             <a href="{{ route('dailyreport.index') }}">
                                 <i class='bx bx-radio-circle'></i>Daily Report
                             </a>
                         </li>
                     @endif

                     @if ($can('p2h'))
                         <li>
                             <a href="{{ route('p2h.index') }}">
                                 <i class='bx bx-radio-circle'></i>P2H
                             </a>
                         </li>
                     @endif

                     @if ($can('mechanical_inspection'))
                         <li>
                             <a href="{{ route('mechanicalinspection.index') }}">
                                 <i class='bx bx-radio-circle'></i>Mechanical Inspection
                             </a>
                         </li>
                     @endif

                     @if ($can('maintenance'))
                         <li>
                             <a href="{{ route('maintenance.index') }}">
                                 <i class='bx bx-radio-circle'></i>Repair & Maintenance
                             </a>
                         </li>
                     @endif

                     @if ($can('purchase_requisition'))
                         <li>
                             <a href="{{ route('purchaserequisition.index') }}">
                                 <i class='bx bx-radio-circle'></i>Purchase Requisition
                             </a>
                         </li>
                     @endif

                     @if ($can('proforma_invoice'))
                         <li>
                             <a href="{{ route('proformainvoice.index') }}">
                                 <i class='bx bx-radio-circle'></i>Proforma Invoice
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($canAny($menuPermissions['survey']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-user-voice"></i>
                     </div>
                     <div class="menu-title">Survey</div>
                 </a>

                 <ul>
                     @if ($can('progress_claim'))
                         <li>
                             <a href="{{ route('progressclaim.index') }}">
                                 <i class='bx bx-radio-circle'></i>Progress Claim
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($canAny($menuPermissions['safety']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-plus-medical"></i>
                     </div>
                     <div class="menu-title">Safety</div>
                 </a>

                 <ul>
                     @if ($can('unit_expired'))
                         <li>
                             <a href="{{ route('unitexpired.index') }}">
                                 <i class='bx bx-radio-circle'></i>Unit Expired
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($can('purchase_requisition_general'))
             <li>
                 <a href="{{ route('purchaserequisitiongeneral.index') }}">
                     <div class="parent-icon">
                         <i class="bx bx-file"></i>
                     </div>
                     <div class="menu-title">Purchase Requisition</div>
                 </a>
             </li>
         @endif


         @if ($canAny($menuPermissions['procurement']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-cart"></i>
                     </div>
                     <div class="menu-title">Procurement</div>
                 </a>

                 <ul>
                     @if ($can('request_quotation'))
                         <li>
                             <a href="{{ route('requestquotation.index') }}">
                                 <i class='bx bx-radio-circle'></i>Request Quotation
                             </a>
                         </li>
                     @endif

                     @if ($can('purchase_order'))
                         <li>
                             <a href="{{ route('purchaseorder.index') }}">
                                 <i class='bx bx-radio-circle'></i>Purchase Order
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($canAny($menuPermissions['finance']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-dollar-circle"></i>
                     </div>
                     <div class="menu-title">Finance</div>
                 </a>

                 <ul>
                     @if ($can('invoice_receipt') || $can('purchase_order_payment'))
                         <li>
                             <a class="has-arrow" href="javascript:;">
                                 <i class='bx bx-radio-circle'></i>Purchase
                             </a>

                             <ul>
                                 @if ($can('invoice_receipt'))
                                     <li>
                                         <a href="{{ route('invoicereceipt.index') }}">
                                             <i class='bx bx-radio-circle'></i>Invoice Receipt
                                         </a>
                                     </li>
                                 @endif

                                 @if ($can('purchase_order_payment'))
                                     <li>
                                         <a href="{{ route('purchaseorderpayment.index') }}">
                                             <i class='bx bx-radio-circle'></i>PO Payment
                                         </a>
                                     </li>
                                 @endif
                             </ul>
                         </li>
                     @endif
                     @if ($can('cic') || $can('invoice') || $can('invoice_payment'))
                         <li>
                             <a class="has-arrow" href="javascript:;">
                                 <i class='bx bx-radio-circle'></i>Sales
                             </a>

                             <ul>
                                 @if ($can('cic'))
                                     <li>
                                         <a href="{{ route('cic.index') }}">
                                             <i class='bx bx-radio-circle'></i>CIC
                                         </a>
                                     </li>
                                 @endif

                                 @if ($can('invoice'))
                                     <li>
                                         <a href="{{ route('invoice.index') }}">
                                             <i class='bx bx-radio-circle'></i>Invoice
                                         </a>
                                     </li>
                                 @endif

                                 @if ($can('invoice_payment'))
                                     <li>
                                         <a href="{{ route('invoicepayment.index') }}">
                                             <i class='bx bx-radio-circle'></i>Invoice Payment
                                         </a>
                                     </li>
                                 @endif
                             </ul>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif

         <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon">
                     <i class="bx bx-chart"></i>
                 </div>
                 <div class="menu-title">Report</div>
             </a>
             <ul>
                 <li>
                     <a href="{{ route('report', ['t' => 'breakdown-summary']) }}">
                         <i class='bx bx-radio-circle'></i>Unit Breakdown Summary
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('report', ['t' => 'monitoring-invoice']) }}">
                         <i class='bx bx-radio-circle'></i>Monitoring Invoice
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('report', ['t' => 'monitoring-po']) }}">
                         <i class='bx bx-radio-circle'></i>Monitoring PO
                     </a>
                 </li>
             </ul>
         </li>


         @if ($can('approval'))
             <li>
                 <a href="{{ route('approval.index') }}">
                     <div class="parent-icon">
                         <i class="bx bx-file"></i>
                     </div>
                     <div class="menu-title d-inline-flex align-items-center gap-2">
                         <span>Approval</span>

                         <span id="badge-approval-process">
                             @if ($approval_process > 0)
                                 <span class="badge bg-success" style="font-size: 13px;">
                                     {{ $approval_process }}
                                 </span>
                             @endif
                         </span>
                     </div>
                 </a>
             </li>
         @endif


         @if ($canAny($menuPermissions['master_data']))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-coin-stack"></i>
                     </div>
                     <div class="menu-title">Master Data</div>
                 </a>

                 <ul>
                     @if ($can('service'))
                         <li>
                             <a href="{{ route('service.index') }}">
                                 <i class='bx bx-radio-circle'></i>Service
                             </a>
                         </li>
                     @endif

                     @if ($can('contract'))
                         <li>
                             <a href="{{ route('contract.index') }}">
                                 <i class='bx bx-radio-circle'></i>Contract
                             </a>
                         </li>
                     @endif

                     @if ($can('unit'))
                         <li>
                             <a href="{{ route('unit.index') }}">
                                 <i class='bx bx-radio-circle'></i>Unit
                             </a>
                         </li>
                     @endif

                     @if ($can('unit_model'))
                         <li>
                             <a href="{{ route('unitmodel.index') }}">
                                 <i class='bx bx-radio-circle'></i>Unit Model
                             </a>
                         </li>
                     @endif

                     @if ($can('unit_brand'))
                         <li>
                             <a href="{{ route('unitbrand.index') }}">
                                 <i class='bx bx-radio-circle'></i>Unit Brand
                             </a>
                         </li>
                     @endif

                     @if ($can('unit_rate'))
                         <li>
                             <a href="{{ route('unitrate.index') }}">
                                 <i class='bx bx-radio-circle'></i>Unit Rate
                             </a>
                         </li>
                     @endif

                     @if ($can('location'))
                         <li>
                             <a href="{{ route('location.index') }}">
                                 <i class='bx bx-radio-circle'></i>Location
                             </a>
                         </li>
                     @endif

                     @if ($can('maintenance_item'))
                         <li>
                             <a href="{{ route('maintenanceitem.index') }}">
                                 <i class='bx bx-radio-circle'></i>Maintenance Item
                             </a>
                         </li>
                     @endif

                     @if ($can('mro_item'))
                         <li>
                             <a href="{{ route('mroitem.index') }}">
                                 <i class='bx bx-radio-circle'></i>MRO Item
                             </a>
                         </li>
                     @endif

                     @if ($can('client_vendor'))
                         <li>
                             <a href="{{ route('clientvendor.index') }}">
                                 <i class='bx bx-radio-circle'></i>Client & Vendor
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif


         @if ($user->hasRole('superadmin'))
             <li>
                 <a href="javascript:;" class="has-arrow">
                     <div class="parent-icon">
                         <i class="bx bx-cog"></i>
                     </div>
                     <div class="menu-title">Setting</div>
                 </a>

                 <ul>
                     <li>
                         <a href="{{ route('user.index') }}">
                             <i class='bx bx-radio-circle'></i>User
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('role.index') }}">
                             <i class='bx bx-radio-circle'></i>Role
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('permission.index') }}">
                             <i class='bx bx-radio-circle'></i>Permission
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('approval_flow.index') }}">
                             <i class='bx bx-radio-circle'></i>Approval Flow
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
     </ul>
     <!--end navigation-->
 </div>
 <!--end sidebar wrapper -->
