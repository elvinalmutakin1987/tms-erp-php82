<?php

namespace App\Http\Controllers;

use App\Models\Proforma_invoice;
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

class ProformaInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $proforma_invoice = Proforma_invoice::query();
            if (request()->status != 'All') {
                $proforma_invoice = $proforma_invoice->where('status', request()->status);
            }
            if (request()->unit_id != 'All') {
                $proforma_invoice = $proforma_invoice->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $proforma_invoice = $proforma_invoice->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $proforma_invoice = $proforma_invoice->where('date', '<=', request()->date_end);
            }
            $proforma_invoice = $proforma_invoice->orderBy('date', 'desc')->get();
            return DataTables::of($proforma_invoice)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('proformainvoice.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('proformainvoice.print', $item->id) . '" target="_blank">Print</a>
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
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('proformainvoice.edit')):
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
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('proformainvoice.delete')):
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
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Proforma Invoice',
            'route-sub-module' => 'proformainvoice.index',
        ];
        return view('proforma_invoice.index', compact('breadcrum'));
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
                    '_uom',
                    '__qty',
                    'maintenance_item_id',
                    'maintenance_item',
                    'mro_item_id',
                    'mro_item',
                    'uom',
                    'qty',
                ),
                [
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id
                ]
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
            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_requisition';
            if (checkHasApproval($model)) {
                if ($purchase_requisition->status == 'Open') {
                    $purchase_requisition->status = 'Approval';
                    $purchase_requisition->save();
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
    public function show(Proforma_invoice $proforma_invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proforma_invoice $proforma_invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proforma_invoice $proforma_invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proforma_invoice $proforma_invoice)
    {
        //
    }
}
