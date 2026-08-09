<?php

namespace App\Http\Controllers;

use App\Models\InvoicePayment;
use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Client_vendor;
use App\Models\Invoice;
use App\Models\Invoice_payment;
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
use Spatie\Permission\Models\Permission;
use App\Services\ApprovalService;

class InvoicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $invoice_payment = Invoice_payment::query()
                ->with('invoice');
            if (request()->status != 'All' && request()->status != '') {
                $invoice_payment->where('status', request()->status);
            }
            if (request()->vendor != 'All' && request()->vendor != '') {
                $invoice_payment->whereHas('purchase_order', function ($query) {
                    $query->where('client_vendor_id', request()->vendor);
                });
            }
            if (request()->date_start != '') {
                $invoice_payment->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $invoice_payment->where('date', '<=', request()->date_end);
            }
            $invoice_payment = $invoice_payment
                ->orderBy('date', 'desc')
                ->get();
            $user = Auth::user();
            $permissionNames = [
                'invoice_payment.edit',
                'invoice_payment.delete',
            ];
            $guardName = config('auth.defaults.guard', 'web');
            $existingPermissions = Permission::query()
                ->whereIn('name', $permissionNames)
                ->where('guard_name', $guardName)
                ->pluck('name')
                ->flip();
            $canAccess = function (string $permission) use ($user, $existingPermissions) {
                if ($user->hasRole('superadmin')) {
                    return true;
                }
                if (! $existingPermissions->has($permission)) {
                    return false;
                }
                return $user->hasPermissionTo($permission);
            };
            return DataTables::of($invoice_payment)
                ->addIndexColumn()
                ->addColumn('action', function ($item) use ($canAccess) {
                    $button = '
                        <div class="col">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item exportPdfButton" href="' . route('invoicepayment.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('invoicepayment.print', $item->id) . '" target="_blank">
                                            Print
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail" data-id="' . $item->id . '">
                                            Detail
                                        </a>
                                    </li>
                    ';
                    /**
                     * Tombol Edit:
                     * - hanya muncul jika status Draft
                     * - hanya untuk superadmin atau user dengan permission invoice_payment.edit
                     */
                    if (($item->status === 'Draft' && $canAccess('invoice_payment.edit')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal" data-id="' . $item->id . '">
                                    Edit
                                </a>
                            </li>
                        ';
                    }
                    /**
                     * Tombol Delete:
                     * - hanya muncul jika status bukan Done, Approval, atau Approved
                     * - hanya untuk superadmin atau user dengan permission invoice_payment.delete
                     */
                    $cannotDeleteStatuses = [
                        'Done',
                        'Approval',
                        'Approved',
                    ];
                    if ((!in_array($item->status, $cannotDeleteStatuses, true) && $canAccess('invoice_payment.delete')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                        <li>
                            <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">
                                Delete
                            </a>
                        </li>
                    ';
                    }
                    $button .= '
                            </ul>
                        </div>
                    </div>
                ';
                    return $button;
                })
                ->addColumn('client', function ($item) {
                    return $item->invoice?->client_vendor?->name ?? '';
                })
                ->addColumn('invoice_no', function ($item) {
                    return $item->invoice?->invoice_no ?? '';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $bank = config('bank');
        $bank_tms = config('bank_tms');
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'Invoice Payment',
            'route-sub-module' => 'invoicepayment.index',
        ];
        return view('invoice_payment.index', compact('breadcrum', 'bank', 'bank_tms'));
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
    public function store(Request $request, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required',
                'total' => 'required',
                'payment_path' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png|max:2048',
            ]);
            $bankReceipt = explode('-', $request->bank_receipt);
            $data = array_merge(
                $request->only([
                    'invoice_id',
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
                    'bank_receipt' => trim($bankReceipt[0] ?? null),
                    'bank_account_receipt' => trim($bankReceipt[1] ?? null),
                ]
            );
            $invoice_payment = Invoice_payment::firstOrCreate($data);
            if ($request->has('payment_path')) {
                $file = $request->file('payment_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "payment_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $invoice_payment->real_name = $realname;
                $invoice_payment->payment_path = $directory . '/' . $filename;
                $invoice_payment->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Purchase_order_payment';
            $department = 'Finanace';
            if ($approval_service->checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $invoice_payment->status = 'Approval';
                    $invoice_payment->save();
                    $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                    $approval_service->createApprovalProcess($approval_flow_id, $invoice_payment->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $invoice_payment->status = 'Done';
                    $invoice_payment->save();

                    /**
                     * Buat check status bayarnya
                     */
                    $invoice = Invoice::find($request->invoice_id);
                    $this->check_payment($invoice);
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
    public function show(Invoice_payment $invoice_payment)
    {
        $invoice = $invoice_payment->invoice()
            ->select('invoices.*')
            ->selectRaw("
                (
                    SELECT COALESCE(SUM(COALESCE(total, 0)), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                    and invoice_payments.status = 'Done'
                ) AS payment_total,
                (
                    COALESCE(invoices.grand_total, 0) -
                    (
                        SELECT COALESCE(SUM(COALESCE(total, 0)), 0)
                        FROM invoice_payments
                        WHERE invoice_payments.invoice_id = invoices.id
                        and invoice_payments.status = 'Done'
                    )
                ) AS balance
            ")
            ->first();
        $client_vendor = $invoice->client_vendor;
        $html = '<table style="width: 100%">';
        if ($invoice->payment_path) {
            $html .= '<tr>';
            $html .= '<td style="width:5%">';
            $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_file(\'' . $invoice_payment->id . '\')"><i class="bx bx-trash me-0"></i></a>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<a href="' . route('invoicepayment.export_file', $invoice_payment->id) . '" target="_blank">' . $invoicepayment->real_name . '</a>';
            $html .= '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center">No attachment file</td></tr>';
        }
        $html .= '</table>';
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $invoice_payment,
            'invoice' => $invoice,
            'client_vendor' => $client_vendor,
            'html' => $html
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice_payment $invoice_payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice_payment $invoice_payment, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required',
                'total' => 'required',
                'payment_path' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png|max:2048',
            ]);
            $bankReceipt = explode('-', $request->bank_receipt);
            $data = array_merge(
                $request->only([
                    'invoice_id',
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
                    'bank_receipt' => trim($bankReceipt[0] ?? null),
                    'bank_account_receipt' => trim($bankReceipt[1] ?? null),
                ]
            );
            $invoice_payment->update($data);
            if ($request->has('payment_path')) {
                $filePath = $invoice_payment->payment_path;
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $invoice_payment->payment_path = null;
                $invoice_payment->real_name = null;
                $file = $request->file('payment_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "payment_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $invoice_payment->real_name = $realname;
                $invoice_payment->payment_path = $directory . '/' . $filename;
                $invoice_payment->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Invoice_payment';
            $department = 'Finanace';
            if ($approval_service->checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $invoice_payment->status = 'Approval';
                    $invoice_payment->save();
                    $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                    $approval_service->createApprovalProcess($approval_flow_id, $invoice_payment->id);
                }
            } else {
                if ($request->status == 'Open') {
                    $invoice_payment->status = 'Done';
                    $invoice_payment->save();

                    /**
                     * Buat check status bayarnya
                     */
                    $invoice = Invoice::find($request->invoice_id);
                    $this->check_payment($invoice);
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
    public function destroy(Invoice_payment $invoice_payment)
    {
        DB::beginTransaction();
        try {
            $filePath = $invoice_payment->payment_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $invoice = Invoice::find($invoice_payment->invoice_id);
            $invoice_payment->delete();
            $this->check_payment($invoice);
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
     * Ngambil data vendor
     */
    public function get_client_vendor(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $client_vendor = Client_vendor::selectRaw("id, name as text")
                ->where('type', 'Client')
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
    public function get_invoice(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $invoice = Invoice::selectRaw("id, invoice_no as text, 
            (select name from client_vendors where client_vendors.id = invoices.client_vendor_id) as client,
            (select bank from client_vendors where client_vendors.id = invoices.client_vendor_id) as bank,
            (select bank_account from client_vendors where client_vendors.id = invoices.client_vendor_id) as bank_account,
            date,
            (select top from client_vendors where client_vendors.id = invoices.client_vendor_id) as top,
            grand_total,
            (
                select 
                    COALESCE(SUM(COALESCE(total, 0)), 0) 
                from invoice_payments 
                where invoice_payments.invoice_id = invoices.id
                and invoice_payments.status = 'Done'
            ) as payment_total,
            (
                COALESCE(grand_total, 0) -
                (
                    SELECT 
                        COALESCE(SUM(COALESCE(total, 0)), 0)
                    FROM invoice_payments
                    WHERE invoice_payments.invoice_id = invoices.id
                    and invoice_payments.status = 'Done'
                )
            ) AS balance,
            client_vendor_id")
                ->where('status', 'Done')
                ->whereIn('payment_status', ['Unpaid', 'Partially Paid'])
                ->where('invoice_no', 'like', '%' . $term . '%')
                ->orderBy('invoice_no')->simplePaginate(10);
            $total_count = count($invoice);
            $morePages = true;
            $pagination_obj = json_encode($invoice);
            if (empty($invoice->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $invoice->items(),
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
                ->type('inv-payment')
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
    public function print(Request $request, Invoice_payment $invoice_payment)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Invoice_payment')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('invoice_payment.print', [
            'invoice_payment' => $invoice_payment,
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
        if (in_array($invoice_payment->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $invoice_payment->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $invoice_payment->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($invoice_payment->payment_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Invoice_payment $invoice_payment)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Invoice_payment')->first();
        $approval_step = $approval_flow  ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow  ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow  ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('invoice_payment.print', [
            'invoice_payment' => $invoice_payment,
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
        if (in_array($invoice_payment->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $invoice_payment->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $invoice_payment->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($invoice_payment->payment_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("{$safeFilename}.pdf");
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $payment_id)
    {
        try {
            $invoice_payment = Invoice_payment::find($payment_id);
            $view = 'invoice_payment.detail';
            return response()->view($view, compact('invoice_payment'), 200);
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

    public function export_file(Request $request, Invoice_payment $invoice_payment)
    {
        try {
            $path = public_path('storage/' . $invoice_payment->payment_path);
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            $mimeType = mime_content_type($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileName = basename($path);
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $invoice_payment->payment_path . '"',
                ]);
            }
            return response()->download($path, $invoice_payment->real_name, [
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
    public function destroy_file(Invoice_payment $invoice_payment)
    {
        DB::beginTransaction();
        try {
            $filePath = $invoice_payment->payment_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $invoice_payment->payment_path = null;
            $invoice_payment->real_name = null;
            $invoice_payment->save();
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

    public function check_payment(Invoice $invoice)
    {
        $total_payment = Invoice_payment::where('invoice_id', $invoice->id)->sum('total');
        $grand_total = $invoice->grand_total;
        if ($total_payment >= $grand_total) {
            $invoice->payment_status = 'Paid';
        } else {
            $invoice->payment_status = 'Partially Paid';
        }
        $invoice->save();
    }
}
