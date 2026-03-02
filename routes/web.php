<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ApprovalFlowController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitBrandController;
use App\Http\Controllers\UnitModelController;
use App\Http\Controllers\ClientVendorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\MechanicalInspectionController;
use App\Http\Controllers\MroItemController;
use App\Http\Controllers\P2hController;
use App\Http\Controllers\UnitRateController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'check_login'])->name('check_login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    /**
     * Routenya permission
     */
    Route::resource('permission', PermissionController::class)
        ->middleware('role:superadmin')
        ->names('permission');

    /**
     * Routenya role
     */
    Route::resource('role', RoleController::class)
        ->middleware('role:superadmin|role')
        ->names('role');

    Route::get('role-get-permission-all', [RoleController::class, 'get_permission_all'])
        ->middleware('role:superadmin')
        ->name('role.get_permission_all');

    /**
     * Routenya user
     */
    Route::resource('user', UserController::class)
        ->middleware('role:superadmin')
        ->names('user');

    Route::get('user-get-role-all', [UserController::class, 'get_role_all'])
        ->middleware('role:superadmin')
        ->name('user.get_role_all');

    Route::delete('user-delete-sign/{user}', [UserController::class, 'delete_sign'])
        ->middleware('role:superadmin')
        ->name('user.delete_sign');

    /**
     * Routenya approval flow
     */
    Route::resource('approval_flow', ApprovalFlowController::class)
        ->middleware('role:superadmin')
        ->names('approval_flow');

    Route::get('approval_flow-get-user-all', [ApprovalFlowController::class, 'get_user_all'])
        ->middleware('role:superadmin')
        ->name('approval_flow.get_user_all');

    Route::get('approval_flow-get-step-list', [ApprovalFlowController::class, 'get_step_list'])
        ->middleware('role:superadmin')
        ->name('approval_flow.get_step_list');

    /**
     * Routenya service
     */
    Route::resource('service', ServiceController::class)
        ->middleware('role_or_permission:superadmin')
        ->names('service');

    Route::get('service-get-service-item-list', [ServiceController::class, 'get_service_item_list'])
        ->middleware('role_or_permission:superadmin')
        ->name('service.get_service_item_list');

    /**
     * Routenya location
     */
    Route::resource('location', LocationController::class)
        ->middleware('role_or_permission:superadmin')
        ->names('location');

    /**
     * Routenya unit
     */
    Route::resource('unit', UnitController::class)
        ->middleware('role_or_permission:superadmin')
        ->names('unit');

    Route::get('unit-get-location-all', [UnitController::class, 'get_location_all'])
        ->middleware('role:superadmin')
        ->name('unit.get_location_all');

    Route::get('unit-get-brand-all', [UnitController::class, 'get_brand_all'])
        ->middleware('role:superadmin')
        ->name('unit.get_brand_all');

    Route::get('unit-get-model-all', [UnitController::class, 'get_model_all'])
        ->middleware('role:superadmin')
        ->name('unit.get_model_all');


    /**
     * Routenya unit brand
     */
    Route::resource('unitbrand', UnitBrandController::class)
        ->parameters(['unitbrand' => 'unit_brand'])
        ->middleware('role_or_permission:superadmin')
        ->names('unitbrand');

    /**
     * Routenya unit model
     */
    Route::resource('unitmodel', UnitModelController::class)
        ->parameters(['unitmodel' => 'unit_model'])
        ->middleware('role_or_permission:superadmin')
        ->names('unitmodel');

    Route::get('unitmodel-get-brand-all', [UnitModelController::class, 'get_brand_all'])
        ->middleware('role:superadmin')
        ->name('unitmodel.get_brand_all');

    /**
     * Routenya Client & Vendor
     */
    Route::resource('clientvendor', ClientVendorController::class)
        ->parameters(['clientvendor' => 'client_vendor'])
        ->middleware('role_or_permission:superadmin')
        ->names('clientvendor');

    Route::get('clientvendor-get-location-all', [ClientVendorController::class, 'get_location_all'])
        ->middleware('role:superadmin')
        ->name('clientvendor.get_location_all');

    /**
     * Routenya Contract
     */
    Route::resource('contract', ContractController::class)
        ->middleware('role_or_permission:superadmin')
        ->names('contract');

    Route::get('contract-get-client-all', [ContractController::class, 'get_client_all'])
        ->middleware('role:superadmin')
        ->name('contract.get_client_all');

    Route::get('contract-get-service-all', [ContractController::class, 'get_service_all'])
        ->middleware('role:superadmin')
        ->name('contract.get_service_all');

    Route::put('contract-update-status/{contract}', [ContractController::class, 'update_status'])
        ->middleware('role:superadmin')
        ->name('contract.update_status');


    /**
     * Routenya Unit rate buat perhitungan proforma invoice
     */
    Route::resource('unitrate', UnitRateController::class)
        ->parameters(['unitrate' => 'unit_rate'])
        ->middleware('role_or_permission:superadmin')
        ->names('unitrate');

    Route::get('unitrate-get-client-all', [UnitRateController::class, 'get_client_all'])
        ->middleware('role:superadmin')
        ->name('unitrate.get_client_all');

    Route::get('unitrate-get-unit-all', [UnitRateController::class, 'get_unit_all'])
        ->middleware('role:superadmin')
        ->name('unitrate.get_unit_all');

    Route::get('unitrate-get-contract', [UnitRateController::class, 'get_contract'])
        ->middleware('role:superadmin')
        ->name('unitrate.get_contract');

    /**
     * Routenya MRO Item / Item barang buat pesan2
     */
    Route::resource('mroitem', MroItemController::class)
        ->parameters(['mroitem' => 'mro_item'])
        ->middleware('role_or_permission:superadmin')
        ->names('mroitem');

    Route::get('mroitem-get-unit-all', [MroItemController::class, 'get_unit_all'])
        ->middleware('role:superadmin')
        ->name('mroitem.get_unit_all');


    /**
     * Routenya P2H
     */
    Route::resource('p2h', P2hController::class)
        ->middleware('role_or_permission:superadmin|p2h')
        ->names('p2h');

    Route::get('p2h-get-unit-all', [P2hController::class, 'get_unit_all'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.get_unit_all');

    /**
     * buat load table inputan p2h waktu mau tambah data
     */
    // Route::get('p2h-load-table-add', function () {
    //     return view('p2h.table-add');
    // })
    //     ->middleware('role_or_permission:superadmin|p2h')
    //     ->name('p2h.load_table_add');

    Route::get('p2h-load-table-add', [P2hController::class, 'get_table_add'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.get_table_add');

    Route::get('p2h-load-table-edit/{p2h}', [P2hController::class, 'get_table_edit'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.get_table_edit');

    Route::get('p2h-get-detail/{p2h}', [P2hController::class, 'get_detail'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.get_detail');

    Route::get('p2h-print/{p2h}', [P2hController::class, 'print'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.print');

    Route::get('p2h-export-pdf/{p2h}', [P2hController::class, 'export_pdf'])
        ->middleware('role:superadmin|p2h')
        ->name('p2h.export_pdf');

    /**
     * Routenya mechanical inspection
     */
    Route::resource('mechanicalinspection', MechanicalInspectionController::class)
        ->middleware('role:superadmin')
        ->names('mechanicalinspection');

    Route::get('mechanicalinspection-get-unit-all', [MechanicalInspectionController::class, 'get_unit_all'])
        ->middleware('role:superadmin|mechanical inspection')
        ->name('mechanicalinspection.get_unit_all');

    Route::get('mechanicalinspection-print/{mechanical_inspection}', [MechanicalInspectionController::class, 'print'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.print');

    Route::get('mechanicalinspection-export-pdf/{mechanical_inspection}', [MechanicalInspectionController::class, 'export_pdf'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.export_pdf');

    Route::get('mechanicalinspection-load-table-add', [MechanicalInspectionController::class, 'get_table_add'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.get_table_add');

    Route::get('mechanicalinspection-load-table-edit/{mechanical_inspection}', [MechanicalInspectionController::class, 'get_table_edit'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.get_table_edit');

    Route::get('mechanicalinspection-get-detail/{mechanical_inspection}', [MechanicalInspectionController::class, 'get_detail'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.get_detail');

    Route::get('mechanicalinspection-print/{mechanical_inspection}', [MechanicalInspectionController::class, 'print'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.print');

    Route::get('mechanicalinspection-export-pdf/{mechanical_inspection}', [MechanicalInspectionController::class, 'export_pdf'])
        ->middleware('role:superadmin|mecahincal inspection')
        ->name('mechanicalinspection.export_pdf');
});
