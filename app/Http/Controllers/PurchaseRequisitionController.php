<?php

namespace App\Http\Controllers;

use App\Models\Purchase_requisition;
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

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_requisition = Purchase_requisition::query();
            if (request()->unit_id != 'All') {
                $purchase_requisition = $purchase_requisition->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $purchase_requisition = $purchase_requisition->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $purchase_requisition = $purchase_requisition->where('date', '<=', request()->date_end);
            }
            $purchase_requisition = $purchase_requisition->orderBy('date', 'desc')->get();
            return DataTables::of($purchase_requisition)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('dailyreport.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('dailyreport.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                                <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';

                    if ($item->status == 'Draft'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('inspection.edit')):
                            $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                        endif;
                    endif;

                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('inspection.edit')):
                        $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>';
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
                ->make();
        }
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Purchase Requisition',
            'route-sub-module' => 'purchaserequisition.index',
        ];
        return view('purchase_requisition.index', compact('breadcrum'));
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
    public function show(Purchase_requisition $purchase_requisition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase_requisition $purchase_requisition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase_requisition $purchase_requisition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase_requisition $purchase_requisition)
    {
        //
    }
}
