<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Client_vendor;
use App\Models\Maintenance_item;
use App\Models\Mro_item;
use App\Models\Purchase_order;
use App\Models\Purchase_requisition;
use App\Models\Request_quotation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
// use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
                ->addColumn('requisition_no', function ($item) {
                    $purchase_requisition = Purchase_requisition::find($item->purchase_requisition_id);
                    return $purchase_requisition?->requisition_no ?? '';
                })->addColumn('vendor', function ($item) {
                    $vendor = Client_vendor::find($item->client_vendor_id);
                    return $vendor?->name ?? '';
                })
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $job = config('job');
        $breadcrum = [
            'module' => 'Procurement',
            'route-module' => null,
            'sub-module' => 'Purchase Order',
            'route-sub-module' => 'purchaseorder.index',
        ];
        return view('purchase_order.index', compact('breadcrum', 'uom', 'system_setting', 'job'));
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
                'date' => 'required',
                'client_vendor_id' => 'required',
                'vendor_offer_path' => 'file|mimes:pdf,doc,docx|max:2048',
            ]);
            $purchase_requisition = Purchase_requisition::find($request->purchase_requisition_id);
            $type = $purchase_requisition?->type ?? 'General';
            $department = $purchase_requisition?->department ?? 'Equipment';
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'purchase_requisition_id',
                    'client_vendor_id',
                    'date',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'urgency',
                    'discount'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'type' => $type,
                    'department' => $department
                ]
            );
            $purchase_order = Purchase_order::firstOrCreate($data);
            if ($type == 'Equipment') {
                if ($request->has('maintenance_item_id')) {
                    $mro_item = Mro_item::find($request->mro_item_id);
                    $type = $mro_item?->type ?? 'Good';
                    foreach ($request->maintenance_item_id as $i => $item) {
                        $purchase_order->purchase_order_detail()->create(
                            [
                                'request_token' => $purchase_order->request_token,
                                'maintenance_item_id' => $item,
                                'mro_item_id' => $request->mro_item_id[$i],
                                'type' => $type,
                                'desc_vendor' => $request->desc_vendor[$i],
                                'uom' => $request->uom[$i],
                                'qty' => $request->qty[$i],
                                'price' => $request->price[$i],
                                'discount_item' => $request->discount_item[$i],
                                'tax' => $system_setting['tax'],
                                'amount' => $request->amount[$i],
                            ]
                        );
                    }
                }
            } else {
                if ($request->has('description')) {
                    foreach ($request->description as $i => $item) {
                        $purchase_order->purchase_order_detail()->create(
                            [
                                'request_token' => $purchase_order->request_token,
                                'type' => $request->type[$i],
                                'description' => $item,
                                'desc_vendor' => $request->desc_vendor[$i],
                                'uom' => $request->uom[$i],
                                'qty' => $request->qty[$i],
                                'price' => $request->price[$i],
                                'discount_item' => $request->discount_item[$i],
                                'tax' => $system_setting['tax'],
                                'amount' => $request->amount[$i]
                            ]
                        );
                    }
                }
            }

            if ($request->has('vendor_over_path')) {
                $file = $request->file('vendor_offer_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "quotation_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                Request_quotation::firstOrCreate([
                    'purchase_requisition_id' => $request->purchase_requisition_id ?? null,
                    'client_vendor_id' => $request->client_vendor_id,
                    'request_token' => $request->request_token,
                    'user_id' => Auth::user()->id,
                    'real_name' => $realname,
                    'quotation_path' => $directory . '/' . $filename,
                    'notes' => $request->notes
                ]);
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_order';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_order->status = 'Approval';
                    $purchase_order->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_order->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_order->status = 'Approved';
                    $purchase_order->save();
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
    public function show(Purchase_order $purchase_order)
    {
        $purchase_order_detail = $purchase_order->purchase_order_detail;
        $vendor = Client_vendor::find($purchase_order->client_vendor_id);
        $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token);
        $html = '<table style="width: 100%">';
        if ($request_quotation->count() > 0) {
            foreach ($request_quotation->get() as $key => $value) {
                $html .= '<tr>';
                $html .= '<td style="width:5%">';
                $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_file(\'' . $value->id . '\')"><i class="bx bx-trash me-0"></i></a>';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<a href="' . route('purchaseorder.export_file', $value->id) . '" target="_blank">' . $value->real_name . '</a>';
                $html .= '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td class="text-center">No quotation file</td></tr>';
        }
        $html .= '</table>';
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_order,
            'purchase_order_detail' => $purchase_order_detail,
            'vendor' => $vendor,
            'html' => $html
        ], 200);
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
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required',
                'client_vendor_id' => 'required',
            ]);
            $purchase_requisition = Purchase_requisition::find($request->purchase_requisition_id);
            $type = $purchase_requisition?->type ?? 'General';
            $department = $purchase_requisition?->department ?? 'Equipment';
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'purchase_requisition_id',
                    'date',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'urgency',
                    'client_vendor_id',
                    'discount'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'type' => $type,
                    'department' => $department
                ]
            );
            $lockPurchase_order = Purchase_order::where('id', $purchase_order->id)->lockForUpdate()->first();
            $lockPurchase_order->update($data);
            $purchase_order->purchase_order_detail()->delete();
            if ($type == 'Equipment') {
                if ($request->has('maintenance_item_id')) {
                    foreach ($request->maintenance_item_id as $i => $item) {
                        $purchase_order->purchase_order_detail()->create(
                            [
                                'request_token' => $purchase_order->request_token,
                                'maintenance_item_id' => $item,
                                'mro_item_id' => $request->mro_item_id[$i],
                                'desc_vendor' => $request->desc_vendor[$i],
                                'uom' => $request->uom[$i],
                                'qty' => $request->qty[$i],
                                'price' => $request->price[$i],
                                'discount_item' => $request->discount_item[$i],
                                'tax' => $system_setting['tax'],
                                'amount' => $request->amount[$i]
                            ]
                        );
                    }
                }
            } else {
                if ($request->has('description')) {
                    foreach ($request->description as $i => $item) {
                        $purchase_order->purchase_order_detail()->create(
                            [
                                'request_token' => $purchase_order->request_token,
                                'type' => $request->type[$i],
                                'description' => $item,
                                'desc_vendor' => $request->desc_vendor[$i],
                                'uom' => $request->uom[$i],
                                'qty' => $request->qty[$i],
                                'price' => $request->price[$i],
                                'discount_item' => $request->discount_item[$i],
                                'tax' => $system_setting['tax'],
                                'amount' => $request->amount[$i]
                            ]
                        );
                    }
                }
            }

            if ($request->has('vendor_over_path')) {
                $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token)->first();
                $filePath = $request_quotation->quotation_path;
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $request_quotation->delete();

                $file = $request->file('vendor_offer_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "quotation_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                Request_quotation::firstOrCreate([
                    'purchase_requisition_id' => $request->purchase_requisition_id ?? null,
                    'client_vendor_id' => $request->client_vendor_id,
                    'request_token' => $purchase_order->request_token,
                    'user_id' => Auth::user()->id,
                    'real_name' => $realname,
                    'quotation_path' => $directory . '/' . $filename,
                    'notes' => $request->notes
                ]);
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_order';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_order->status = 'Approval';
                    $purchase_order->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_order->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_order->status = 'Approved';
                    $purchase_order->save();
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
    public function destroy(Purchase_order $purchase_order)
    {
        DB::beginTransaction();
        try {
            $purchase_order->purchase_order_detail()->delete();
            $purchase_order->delete();
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
     * Ngambil data requisition
     */
    public function get_purchase_requisition(Request $request)
    {
        try {
            $purchase_requisition = Purchase_requisition::whereIn('status', ['Approved', 'Received'])->get();
            return response()->json([
                'success' => true,
                'data' => $purchase_requisition
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
    public function get_table_add(Request $request, Purchase_order $purchase_order)
    {
        try {
            $type = 'General';
            $client_vendor = Client_vendor::find($request->client_vendor_id);
            $taxable = $client_vendor?->taxable ?? 'PKP';
            $purchase_requisition_id = $request?->purchase_requisition_id ?? 'Direct PO';
            $purchase_requisition = Purchase_requisition::find($request->purchase_requisition_id);
            if ($purchase_requisition) {
                $type = $purchase_requisition->type;
            }
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $view = 'purchase_order.table-add';
            if ($type == 'General') {
                $view = 'purchase_order.table-gen-add';
            }
            $uom = config('uom');
            $system_setting = config('system_setting');
            $kodeDokumen = 'TMS-SGT';
            $order_prev_no = running_number()
                ->type('po')
                ->formatter(new class($kodeDokumen) implements Presenter {
                    public function __construct(
                        private string $kodeDokumen
                    ) {}
                    public function format(string $type, int $number): string
                    {
                        $bulanRomawi = [
                            1  => 'I',
                            2  => 'II',
                            3  => 'III',
                            4  => 'IV',
                            5  => 'V',
                            6  => 'VI',
                            7  => 'VII',
                            8  => 'VIII',
                            9  => 'IX',
                            10 => 'X',
                            11 => 'XI',
                            12 => 'XII',
                        ];
                        $bulan = $bulanRomawi[(int) date('n')];
                        $tahun = date('Y');
                        return sprintf(
                            '%03d/%s/%s/%s',
                            $number,
                            $this->kodeDokumen,
                            $bulan,
                            $tahun
                        );
                    }
                })
                ->preview();
            $html = view($view, compact('uom', 'system_setting', 'purchase_requisition', 'purchase_requisition_id', 'taxable'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'order_prev_no' => $order_prev_no,
                'data' => $purchase_requisition
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
    public function get_table_edit(Request $request, Purchase_order $purchase_order)
    {
        try {
            $type = 'General';
            $purchase_requisition_id = $request?->purchase_requisition_id ?? 'Direct PO';
            $purchase_requisition = Purchase_requisition::find($request->purchase_requisition_id);
            if ($purchase_requisition) {
                $type = $purchase_requisition->type;
            }
            $view = 'purchase_order.table-edit';
            if ($type == 'General') {
                $view = 'purchase_order.table-gen-edit';
            }
            $uom = config('uom');
            $system_setting = config('system_setting');
            $purchase_order_detail = $purchase_order->purchase_order_detail;
            $html = view($view, compact('purchase_order', 'purchase_order_detail', 'uom', 'system_setting', 'purchase_requisition_id'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'order_no' => $purchase_order->order_no,
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
     * Ngambil data maintenance item
     */
    public function get_client_vendor(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $client_vendor = Client_vendor::selectRaw("id, name as text")
                ->where('type', 'Vendor')
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($client_vendor);
            $morePages = true;
            $pagination_obj = json_encode($client_vendor);
            if (empty($client_vendor->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $client_vendor->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $po_id)
    {
        try {
            $purchase_order = Purchase_order::find($po_id);
            $purchase_order_detail = $purchase_order->purchase_order_detail;
            $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token)->get();
            $view = 'purchase_order.detail';
            return response()->view($view, compact('purchase_order', 'purchase_order_detail', 'request_quotation'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngeprint
     */
    public function print(Request $request, Purchase_order $purchase_order)
    {
        // $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->first();
        // $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        // $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        // $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        // $system_setting = config('system_setting');
        // $pdf = Pdf::loadView('purchase_order.print', [
        //     'purchase_order' => $purchase_order,
        //     'purchase_order_detail' => $purchase_order->purchase_order_detail,
        //     'approval_flow' => $approval_flow,
        //     'approval_step' => $approval_step,
        //     'approval_process' => $approval_process,
        //     'approval_status' => $approval_status,
        //     'system_setting' => $system_setting
        // ])->setPaper('a4', 'portrait');

        // // WAJIB: render dulu
        // $dompdf = $pdf->getDomPDF();
        // $dompdf->render();

        // // Ambil canvas + font
        // $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        // $fontMetrics = $dompdf->getFontMetrics();
        // $font = $fontMetrics->getFont('Helvetica', 'normal');

        // $width  = $canvas->get_width();
        // $height = $canvas->get_height();

        // // Tulis nomor halaman ke semua halaman
        // $canvas->page_text(
        //     $width - 120,          // X: posisi kanan footer
        //     $height - 35,          // Y: posisi footer bawah
        //     "Page {PAGE_NUM} of {PAGE_COUNT}",
        //     $font,
        //     10,
        //     [0, 0, 0]
        // );

        // /**
        //  * Buat check statusnya, kalo draft, open, approval, cancel
        //  * nanti ada watermarknya
        //  */
        // $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received', 'Done'];
        // if (in_array($purchase_order->status, $status, true)) {
        //     $w = $canvas->get_width();
        //     $h = $canvas->get_height();
        //     $font = $fontMetrics->getFont('Helvetica', 'bold');
        //     $size = 48;
        //     $text = "Status : " . $purchase_order->status;
        //     $x = ($w / 2) - 100;
        //     $y = $h / 2 - 350;
        //     $text = $purchase_order->status;
        //     $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        // }

        // $safeFilename = Str::of($purchase_order->order_no)
        //     ->replace(['/', '\\'], '-')   // ganti 
        //     ->toString();
        // return $pdf->stream("report-{$safeFilename}.pdf");

        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;

        $system_setting = config('system_setting');

        $pdf = Pdf::loadView('purchase_order.print', [
            'purchase_order' => $purchase_order,
            'purchase_order_detail' => $purchase_order->purchase_order_detail,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status,
            'system_setting' => $system_setting
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();

        $fontNormal = $fontMetrics->getFont('Helvetica', 'normal');
        $fontBold = $fontMetrics->getFont('Helvetica', 'bold');

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        // ===============================
        // QR CODE LANGSUNG TANPA SIMPAN FILE
        // ===============================

        $qrText = 'PO ini telah disetujui secara digital';

        $qrImage = QrCode::format('png')
            ->size(150)
            ->margin(1)
            ->generate($qrText);

        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);

        // Posisi QR Code di atas page number
        $qrSize = 55;
        $qrX = $width - 120;
        $qrY = $height - 100;

        $canvas->image(
            $qrBase64,
            $qrX,
            $qrY,
            $qrSize,
            $qrSize
        );

        // ===============================
        // PAGE NUMBER
        // ===============================
        $canvas->page_text(
            $width - 120,
            $height - 35,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $fontNormal,
            10,
            [0, 0, 0]
        );

        // ===============================
        // WATERMARK STATUS
        // ===============================
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received', 'Done'];

        if (in_array($purchase_order->status, $status, true)) {
            $size = 48;
            $text = $purchase_order->status;

            $x = ($width / 2) - 100;
            $y = $height / 2 - 350;

            $canvas->text(
                $x,
                $y,
                $text,
                $fontBold,
                $size,
                [0.6, 0.6, 0.6]
            );
        }

        // ===============================
        // SAFE FILENAME
        // ===============================
        $safeFilename = Str::of($purchase_order->order_no)
            ->replace(['/', '\\'], '-')
            ->toString();

        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Purchase_order $purchase_order)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $pdf = Pdf::loadView('purchase_order.print', [
            'purchase_order' => $purchase_order,
            'purchase_order_detail' => $purchase_order->purchase_order_detail,
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

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            $width - 120,          // X: posisi kanan footer
            $height - 35,          // Y: posisi footer bawah
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
        if (in_array($purchase_order->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_order->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_order->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_order->order_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_client_vendor_by_id(Request $request, Client_vendor $client_vendor)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $client_vendor,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * export file
     */

    public function export_file(Request $request, Request_quotation $request_quotation)
    {
        try {
            $path = public_path('storage/' . $request_quotation->quotation_path);
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            $mimeType = mime_content_type($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileName = basename($path);
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $request_quotation->quotation_path . '"',
                ]);
            }
            return response()->download($path, $request_quotation->real_name, [
                'Content-Type' => $mimeType ?: 'application/octet-stream',
            ]);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('purchaseorder.index')
                ->with('error', 'Failed to open file.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_file(Request_quotation $request_quotation)
    {
        DB::beginTransaction();
        try {
            $filePath = $request_quotation->quotation_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $request_quotation->delete();
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
}
