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
            if (request()->type != 'All') {
                $purchase_requisition = $purchase_requisition->where('type', request()->type);
            }
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
                    /**
                     * status draft
                     * user superadmin dan yang punya akses edit aja yang bisa muncul
                     */
                    if ($item->status == 'Draft'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaserequisition.edit')):
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
                    if ($item->status != 'Done'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaserequisition.delete')):
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
                ->make();
        }
        $uom = config('uom');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Purchase Requisition',
            'route-sub-module' => 'purchaserequisition.index',
        ];
        return view('purchase_requisition.index', compact('breadcrum', 'uom'));
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
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'maintenance_item_id',
                    'maintenance_item',
                    'mro_item_id',
                    'mro_item',
                    'uom',
                    'qty',
                ),
                ['input_method' => 'Web']
            );
            $purchase_requisition = Purchase_requisition::create($data);
            foreach ($request->maintenance_item_id as $i => $item) {
                $purchase_requisition->purchase_requisition_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                        'mro_item_id' => $request->mro_item_id[$i],
                        'uom' => $request->uom[$i],
                        'qty' => $request->qty[$i]
                    ],
                    [
                        'mro_item_id' => $request->mro_item_id[$i],
                        'uom' => $request->uom[$i],
                        'qty' => $request->qty[$i]
                    ]
                );
            }
            if ($purchase_requisition->status == 'Open') {
                $model = 'Purchase_requisition';
                if (checkHasApproval($model)) {
                    $approval_flow_id = getApprovalFlowId($model);
                    createApprovalProcess($approval_flow_id, $purchase_requisition->id);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data saved!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'title' => 'Opps..',
                'message' => $th->getMessage()
            ], 400);
        }
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
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'maintenance_item_id',
                    'maintenance_item',
                    'mro_item_id',
                    'mro_item',
                    'uom',
                    'qty',
                ),
                ['input_method' => 'Web']
            );
            $purchase_requisition->update($data);
            foreach ($request->maintenance_item_id as $i => $item) {
                $purchase_requisition->purchase_requisition_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                        'mro_item_id' => $request->mro_item_id[$i],
                        'uom' => $request->uom[$i],
                        'qty' => $request->qty[$i]
                    ],
                    [
                        'mro_item_id' => $request->mro_item_id[$i],
                        'uom' => $request->uom[$i],
                        'qty' => $request->qty[$i]
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data saved!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'title' => 'Opps..',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase_requisition $purchase_requisition)
    {
        DB::beginTransaction();
        try {
            $purchase_requisition->purchase_requisition_detail()->delete();
            $purchase_requisition->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Deleted!',
                'message' => 'Data Deleted'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data unit
     */
    public function get_unit_all(Request $request)
    {
        try {
            $unit = Unit::all();
            return response()->json([
                'success' => true,
                'data' => $unit
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
