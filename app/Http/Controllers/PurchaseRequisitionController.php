<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Maintenance;
use App\Models\Maintenance_item;
use App\Models\Mro_item;
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

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_requisition = Purchase_requisition::query();
            if (request()->status != 'All') {
                $purchase_requisition = $purchase_requisition->where('status', request()->status);
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
            if (request()->urgency != 'All') {
                $purchase_requisition = $purchase_requisition->where('urgency', request()->urgency);
            }
            $purchase_requisition = $purchase_requisition->where('type', 'Equipment');
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
                                    <a class="dropdown-item exportPdfButton" href="' . route('purchaserequisition.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('purchaserequisition.print', $item->id) . '" target="_blank">Print</a>
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
                     * status approved
                     * buat edit status jadi done. sambil check penerimaan barang
                     */
                    if ($item->status == 'Approved' || $item->status == 'Received'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->id == $item->user_id):
                            $button .= '<li>
                                     <a class="dropdown-item receiveButton" href="#" data-bs-toggle="modal" data-bs-target="#formReceive"
                                    data-id="' . $item->id . '">Receive</a>
                                </li>';
                        endif;
                    endif;

                    /**
                     * status bukan done, bisa di hapus.
                     * user superadmin dan yang punya akses delete aja yang bisa muncul
                     */
                    if ($item->status != 'Done' && $item->status != 'Approved' && $item->status != 'Approval' && $item->status != 'Received'):
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
                ->addColumn('maintenance_no', function ($item) {
                    $maintenance = Maintenance::find($item->maintenance_id);
                    return $maintenance->maintenance_no ?? '';
                })
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Purchase Requisition',
            'route-sub-module' => 'purchaserequisition.index',
        ];
        return view('purchase_requisition.index', compact('breadcrum', 'uom', 'system_setting'));
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
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'unit_id',
                    'date',
                    'maintenance_id',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'urgency',
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'type' => 'Equipment',
                    'Department' => 'Equipment'
                ]
            );
            $purchase_requisition = Purchase_requisition::firstOrCreate(['request_token' => $request->request_token], $data);
            if ($request->has('maintenance_item_id')) {
                foreach ($request->maintenance_item_id as $i => $item) {
                    $purchase_requisition->purchase_requisition_detail()->create(
                        [
                            'request_token' => $purchase_requisition->request_token,
                            'maintenance_item_id' => $item,
                            'mro_item_id' => $request->mro_item_id[$i],
                            'uom' => $request->uom[$i],
                            'qty' => $request->qty[$i],
                            'price' => $request->price[$i],
                            'tax' => $system_setting['tax'],
                            'amount' => $request->amount[$i]
                        ]
                    );
                }
            }
            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_requisition';
            $department = 'Equipment';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_requisition->status = 'Approval';
                    $purchase_requisition->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_requisition->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_requisition->status = 'Approved';
                    $purchase_requisition->save();
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
        $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_requisition,
            'purchase_requisition_detail' => $purchase_requisition_detail
        ], 200);
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
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'unit_id',
                    'date',
                    'maintenance_id',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'urgency'
                ]),
                [
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id
                ]
            );
            $lockPurchase_requisition = Purchase_requisition::where('id', $purchase_requisition->id)->lockForUpdate()->first();
            $lockPurchase_requisition->update($data);
            $purchase_requisition->purchase_requisition_detail()->delete();
            if ($request->has('maintenance_item_id')) {
                foreach ($request->maintenance_item_id as $i => $item) {
                    $purchase_requisition->purchase_requisition_detail()->create(
                        [
                            'request_token' => $purchase_requisition->request_token,
                            'maintenance_item_id' => $item,
                            'mro_item_id' => $request->mro_item_id[$i],
                            'uom' => $request->uom[$i],
                            'qty' => $request->qty[$i],
                            'price' => $request->price[$i],
                            'tax' => $system_setting['tax'],
                            'amount' => $request->amount[$i]
                        ]
                    );
                }
            }
            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_requisition';
            $department = 'Equipment';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_requisition->status = 'Approval';
                    $purchase_requisition->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_requisition->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_requisition->status = 'Approved';
                    $purchase_requisition->save();
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

    /**
     * Ngambil tabel list requisition nya
     */
    public function get_table_add(Request $request, Purchase_requisition $purchase_requisition)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $view = 'purchase_requisition.table-add';
            $uom = config('uom');
            $system_setting = config('system_setting');
            $requisition_prev_no = Generator::make()
                ->type('pr')
                ->formatter($presenter)
                ->preview();
            $html = view($view, compact('uom', 'system_setting'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'requisition_prev_no' => $requisition_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list requisition nya
     */
    public function get_table_edit(Request $request, Purchase_requisition $purchase_requisition)
    {
        try {
            $view = 'purchase_requisition.table-edit';
            $uom = config('uom');
            $system_setting = config('system_setting');
            $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
            $html = view($view, compact('purchase_requisition', 'purchase_requisition_detail', 'uom', 'system_setting'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'requisition_no' => $purchase_requisition->requisition_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data maintenance item
     */
    public function get_maintenance_item(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $maintenance_item = Maintenance_item::selectRaw("id, name as text")
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($maintenance_item);
            $morePages = true;
            $pagination_obj = json_encode($maintenance_item);
            if (empty($maintenance_item->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $maintenance_item->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * Ngambil data mro item
     */
    public function get_mro_item(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $mro_item = Mro_item::selectRaw("id, name as text")
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($mro_item);
            $morePages = true;
            $pagination_obj = json_encode($mro_item);
            if (empty($mro_item->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $mro_item->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * ngeprint
     */
    public function print(Request $request, Purchase_requisition $purchase_requisition)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_requisition')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $pdf = Pdf::loadView('purchase_requisition.print', [
            'purchase_requisition' => $purchase_requisition,
            'purchase_requisition_detail' => $purchase_requisition->purchase_requisition_detail,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            255, // X (geser kiri/kanan kalau perlu)
            58,  // Y (geser atas/bawah kalau perlu)
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $font,
            10,
            [0, 0, 0]
        );

        /**
         * Buat check statusnya, kalo draft, open, approval, cancel
         * nanti ada watermarknya
         */
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received', 'Done'];
        if (in_array($purchase_requisition->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_requisition->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_requisition->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_requisition->requisition_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Purchase_requisition $purchase_requisition)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_requisition')->first();
        $approval_step = $approval_flow  ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow  ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow  ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $pdf = Pdf::loadView('purchase_requisition.print', [
            'purchase_requisition' => $purchase_requisition,
            'purchase_requisition_detail' => $purchase_requisition->purchase_requisition_detail,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            255, // X (geser kiri/kanan kalau perlu)
            58,  // Y (geser atas/bawah kalau perlu)
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $font,
            10,
            [0, 0, 0]
        );
        /**
         * Buat check statusnya, kalo draft, open, approval, cancel
         * nanti ada watermarknya
         */
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received', 'Done'];
        if (in_array($purchase_requisition->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_requisition->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_requisition->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_requisition->requisition_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $pr_id)
    {
        try {
            $purchase_requisition = Purchase_requisition::find($pr_id);
            $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
            $view = 'purchase_requisition.detail';
            return response()->view($view, compact('purchase_requisition', 'purchase_requisition_detail'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngambil receive purchase requisition
     */
    public function get_receive(Request $request, Purchase_requisition $purchase_requisition)
    {
        try {
            $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
            $view = 'purchase_requisition.table-receive';
            return response()->view($view, compact('purchase_requisition', 'purchase_requisition_detail'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function receive(Request $request, Purchase_requisition $purchase_requisition)
    {
        DB::beginTransaction();
        try {
            $lockPurchase_requisition = Purchase_requisition::where('id', $purchase_requisition->id)->lockForUpdate()->first();
            $lockPurchase_requisition->update([
                'status' => $request->status
            ]);
            if ($request->has('purchase_requisition_detail_id')) {
                foreach ($request->purchase_requisition_detail_id as $i => $item) {
                    $lockPurchase_requisition->purchase_requisition_detail()->updateOrCreate(
                        [
                            'id' => $item,
                        ],
                        [
                            'received_at' => $request->received_at[$i],
                            'received_by' => $request->received_by[$i],
                            'received_note' => $request->received_note[$i]
                        ]
                    );
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
}
