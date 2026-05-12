<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Client_vendor;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PurchaseOrderPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_order_payment = Purchase_order_payment::query()
                ->with('purchase_order');
            if (request()->status != 'All') {
                $purchase_order_payment->where('status', request()->status);
            }
            if (request()->vendor != 'All') {
                $purchase_order_payment->whereHas('purchase_order', function ($query) {
                    $query->where('client_vendor_id', request()->vendor);
                });
            }
            if (request()->date_start != '') {
                $purchase_order_payment->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $purchase_order_payment->where('date', '<=', request()->date_end);
            }
            $purchase_order_payment = $purchase_order_payment
                ->orderBy('date', 'desc')
                ->get();
            return DataTables::of($purchase_order_payment)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('purchaseorderpayment.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('purchaseorderpayment.print', $item->id) . '" target="_blank">Print</a>
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
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaseorderpayment.edit')):
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
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaseorderpayment.delete')):
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
                ->addColumn('vendor', function ($item) {
                    return $item->purchase_order->client_vendor ? $item->purchase_order->client_vendor->name : '';
                })->addColumn('order_no', function ($item) {
                    return $item->purchase_order ? $item->purchase_order->order_no : '';
                })
                ->make();
        }
        $bank = config('bank');
        $bank_tms = config('bank_tms');
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'PO Payment',
            'route-sub-module' => 'purchaseorderpayment.index',
        ];
        return view('purchase_order_payment.index', compact('breadcrum', 'bank', 'bank_tms'));
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
                'total' => 'required',
                'payment_path' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png|max:2048',
            ]);
            $bankSender = explode('-', $request->bank_sender);
            $data = array_merge(
                $request->only([
                    'purchase_order_id',
                    'date',
                    'notes',
                    'total',
                    'bank',
                    'bank_account',
                    'type',
                    'status',
                    'bank',
                    'bank_account',
                    'ref_no'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'bank_sender' => trim($bankSender[0] ?? null),
                    'bank_account_sender' => trim($bankSender[1] ?? null),
                ]
            );
            $purchase_order_payment = Purchase_order_payment::firstOrCreate($data);
            if ($request->has('payment_path')) {
                $file = $request->file('payment_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "payment_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $purchase_order_payment->real_name = $realname;
                $purchase_order_payment->payment_path = $directory . '/' . $filename;
                $purchase_order_payment->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_order_payment';
            $department = 'Finanace';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_order_payment->status = 'Approval';
                    $purchase_order_payment->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_order_payment->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_order_payment->status = 'Done';
                    $purchase_order_payment->save();
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
    public function show(Purchase_order_payment $purchase_order_payment)
    {
        $purchase_order = $purchase_order_payment->purchase_order()
            ->select('purchase_orders.*')
            ->selectRaw("
                (
                    SELECT COALESCE(SUM(COALESCE(total, 0)), 0)
                    FROM purchase_order_payments
                    WHERE purchase_order_payments.purchase_order_id = purchase_orders.id
                    and purchase_order_payments.status = 'Done'
                ) AS payment_total,
                (
                    COALESCE(purchase_orders.grand_total, 0) -
                    (
                        SELECT COALESCE(SUM(COALESCE(total, 0)), 0)
                        FROM purchase_order_payments
                        WHERE purchase_order_payments.purchase_order_id = purchase_orders.id
                        and purchase_order_payments.status = 'Done'
                    )
                ) AS balance
            ")
            ->first();
        $client_vendor = $purchase_order->client_vendor;
        $html = '<table style="width: 100%">';
        if ($purchase_order_payment->payment_path) {
            $html .= '<tr>';
            $html .= '<td style="width:5%">';
            $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_file(\'' . $purchase_order_payment->id . '\')"><i class="bx bx-trash me-0"></i></a>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<a href="' . route('purchaseorderpayment.export_file', $purchase_order_payment->id) . '" target="_blank">' . $purchase_order_payment->real_name . '</a>';
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center">No attachment file</td></tr>';
        }
        $html .= '</table>';
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_order_payment,
            'purchase_order' => $purchase_order,
            'client_vendor' => $client_vendor,
            'html' => $html
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase_order_payment $purchase_order_payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase_order_payment $purchase_order_payment)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required',
                'total' => 'required',
                'payment_path' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png|max:2048',
            ]);
            $bankSender = explode('-', $request->bank_sender);
            $data = array_merge(
                $request->only([
                    'purchase_order_id',
                    'date',
                    'notes',
                    'total',
                    'bank',
                    'bank_account',
                    'type',
                    'status',
                    'bank',
                    'bank_account',
                    'ref_no'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'bank_sender' => trim($bankSender[0] ?? null),
                    'bank_account_sender' => trim($bankSender[1] ?? null),
                ]
            );
            $purchase_order_payment->update($data);
            if ($request->has('payment_path')) {
                $filePath = $purchase_order_payment->payment_path;
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $purchase_order_payment->payment_path = null;
                $purchase_order_payment->real_name = null;
                $file = $request->file('payment_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "payment_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $purchase_order_payment->real_name = $realname;
                $purchase_order_payment->payment_path = $directory . '/' . $filename;
                $purchase_order_payment->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_order_payment';
            $department = 'Finanace';
            if (checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $purchase_order_payment->status = 'Approval';
                    $purchase_order_payment->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $purchase_order_payment->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $purchase_order_payment->status = 'Done';
                    $purchase_order_payment->save();
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
    public function destroy(Purchase_order_payment $purchase_order_payment)
    {
        //
    }

    /**
     * Ngambil data vendor
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
     * Ngambil data purchase order
     */
    public function get_purchase_order(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $purchase_order = Purchase_order::selectRaw("id, order_no as text, 
            (select name from client_vendors where client_vendors.id = purchase_orders.client_vendor_id) as vendor,
            (select bank from client_vendors where client_vendors.id = purchase_orders.client_vendor_id) as bank,
            (select bank_account from client_vendors where client_vendors.id = purchase_orders.client_vendor_id) as bank_account,
            invoice_date,
            (select top from client_vendors where client_vendors.id = purchase_orders.client_vendor_id) as top,
            grand_total,
            (
                select 
                    COALESCE(SUM(COALESCE(total, 0)), 0) 
                from purchase_order_payments 
                where purchase_order_payments.purchase_order_id = purchase_orders.id
                and purchase_order_payments.status = 'Done'
            ) as payment_total,
            (
                COALESCE(grand_total, 0) -
                (
                    SELECT 
                        COALESCE(SUM(COALESCE(total, 0)), 0)
                    FROM purchase_order_payments
                    WHERE purchase_order_payments.purchase_order_id = purchase_orders.id
                    and purchase_order_payments.status = 'Done'
                )
            ) AS balance,
            client_vendor_id")
                ->where('status', 'Done')
                ->where('order_no', 'like', '%' . $term . '%')
                ->orderBy('order_no')->simplePaginate(10);
            $total_count = count($purchase_order);
            $morePages = true;
            $pagination_obj = json_encode($purchase_order);
            if (empty($purchase_order->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $purchase_order->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * Ngambil prev nomornya
     */
    public function get_prev_no(Request $request)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $payment_prev_no = Generator::make()
                ->type('po-payment')
                ->formatter($presenter)
                ->preview();
            return response()->json([
                'success' => true,
                'payment_prev_no' => $payment_prev_no
            ], 200);
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
    public function print(Request $request, Purchase_order_payment $purchase_order_payment)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order_payment')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('purchase_order_payment.print', [
            'purchase_order_payment' => $purchase_order_payment,
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
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($purchase_order_payment->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_order_payment->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_order_payment->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_order_payment->payment_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Purchase_order_payment $purchase_order_payment)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order_payment')->first();
        $approval_step = $approval_flow  ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow  ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow  ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('purchase_order_payment.print', [
            'purchase_order_payment' => $purchase_order_payment,
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
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($purchase_order_payment->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_order_payment->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_order_payment->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_order_payment->payment_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $payment_id)
    {
        try {
            $purchase_order_payment = Purchase_order_payment::find($payment_id);
            $view = 'purchase_order_payment.detail';
            return response()->view($view, compact('purchase_order_payment'), 200);
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

    public function export_file(Request $request, Purchase_order_payment $purchase_order_payment)
    {
        try {
            $path = public_path('storage/' . $purchase_order_payment->payment_path);
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            $mimeType = mime_content_type($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileName = basename($path);
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $purchase_order_payment->payment_path . '"',
                ]);
            }
            return response()->download($path, $purchase_order_payment->real_name, [
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
    public function destroy_file(Purchase_order_payment $purchase_order_payment)
    {
        DB::beginTransaction();
        try {
            $filePath = $purchase_order_payment->payment_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $purchase_order_payment->payment_path = null;
            $purchase_order_payment->real_name = null;
            $purchase_order_payment->save();
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
