<?php

namespace App\Http\Controllers;

use App\Models\Purchase_order;
use App\Models\Purchase_requisition;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_order = Purchase_order::query();
            if (request()->status != 'All') {
                $purchase_order = $purchase_order->where('status', request()->status);
            }
            if (request()->date_start != '') {
                $purchase_order = $purchase_order->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $purchase_order = $purchase_order->where('date', '<=', request()->date_end);
            }
            if (request()->urgency != 'All') {
                $purchase_order = $purchase_order->where('urgency', request()->urgency);
            }
            $purchase_order = $purchase_order->orderBy('date', 'desc')->get();
            return DataTables::of($purchase_order)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('purchaseorder.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('purchaseorder.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                                <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    /**
                     * status draft
                     * user superadmin dan yang punya akses edit aja yang bisa muncul
                     */
                    if ($item->status == 'Draft'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaseorder.edit')):
                            $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                        endif;
                    endif;

                    /**
                     * status bukan done, bisa di hapus.
                     * user superadmin dan yang punya akses delete aja yang bisa muncul
                     */
                    if ($item->status != 'Done' && $item->status != 'Approved' && $item->status != 'Approval'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaseorder.delete')):
                            $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>';
                        endif;
                    endif;

                    $button .= '</ul>
                        </div>
                    </div>
                    ';

                    return $button;
                })
                ->addColumn('unit', function ($item) {
                    $unit = Unit::find($item->unit_id);
                    return $unit->vehicle_no;
                })
                ->addColumn('requisition_no', function ($item) {
                    $purchase_requisition = Purchase_requisition::find($item->purchase_requisition_id);
                    return $purchase_requisition->requisition_no ?? '';
                })
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $breadcrum = [
            'module' => 'Procurement',
            'route-module' => null,
            'sub-module' => 'Purchase Order',
            'route-sub-module' => 'purchaseorder.index',
        ];
        return view('purchase_order.index', compact('breadcrum', 'uom', 'system_setting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase_order $purchase_order)
    {
        //
    }
}
