<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Client_vendor;
use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Daily_report;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
use App\Models\Purchase_requisition;
use App\Models\Unit;
use App\Models\Unit_target;
use App\Models\Invoice;
use App\Models\Invoice_proforma_invoice;
use App\Models\Service;
use App\Models\Service_item;
use App\Models\Sys_setting;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Spatie\Permission\Models\Permission;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\ApprovalService;
use setasign\Fpdi\Fpdi;
use Symfony\Component\Process\Process;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $invoice = Invoice::query();
            if (request()->status != 'All') {
                $invoice = $invoice->where('status', request()->status);
            }
            if (request()->month != 'All') {
                if (request()->year != 'All') {
                    $invoice = $invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $invoice = $invoice->where('periode', 'like', '%-' . request()->month);
                }
            }
            if (request()->year != 'All') {
                if (request()->month != 'All') {
                    $invoice = $invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $invoice = $invoice->where('periode', 'like', request()->year . '-%');
                }
            }
            $invoice = $invoice->orderBy('id', 'desc')->get();
            $user = Auth::user();
            $permissionNames = [
                'invoice.edit',
                'invoice.update_progress',
                'invoice.delete',
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
            return DataTables::of($invoice)
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
                                        <a class="dropdown-item exportPdfButton" href="' . route('invoice.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('invoice.print', $item->id) . '" target="_blank">
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
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.edit
                     */
                    if (($item->contract_id === null && $item->status === 'Draft' && $canAccess('invoice.edit')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal" data-id="' . $item->id . '">
                                    Edit
                                </a>
                            </li>
                        ';
                    }
                    /**
                     * Tombol Update Progress:
                     * - hanya muncul jika status Approved
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.update_progress
                     */
                    if (($item->contract_id !== null && $item->status === 'Approved' && $canAccess('invoice.update_progress')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item updateButton" href="#" data-bs-toggle="modal" data-bs-target="#formUpdate" data-id="' . $item->id . '">
                                    Update Progress
                                </a>
                            </li>
                        ';
                    }
                    /**
                     * Tombol Delete:
                     * - hanya muncul jika status bukan Done
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.delete
                     */
                    $cannotDeleteStatuses = [
                        'Done',
                        'Approval',
                        'Approved',
                    ];
                    if ((!in_array($item->status, $cannotDeleteStatuses, true) && $canAccess('invoice.delete')) || Auth::user()->hasRole('superadmin')) {
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
                ->addColumn('contract_no', function ($item) {
                    return $item->contract->contract_no ?? 'Direct Invoice';
                })
                ->addColumn('client', function ($item) {
                    return $item->client_vendor->name ?? '';
                })
                ->addColumn('type', function ($item) {
                    return $item->contract->service->type ?? '';
                })
                ->addColumn('periode_', function ($item) {
                    return Carbon::parse($item->periode)->format('F Y') ?? '';
                })
                ->addColumn('penalty_', function ($item) {
                    return Number::format($item?->penalty ?? 0, precision: 0) ?? '';
                })
                ->addColumn('total_', function ($item) {
                    return Number::format($item?->total ?? 0, precision: 0) ?? '';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $system_setting = config('system_setting');
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'Sales',
            'route-sub-module' => null,
            'sub-sub-module' => 'Invoice',
            'route-sub-sub-module' => 'invoice.index'
        ];
        return view('invoice.index', compact('breadcrum', 'system_setting'));
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
                'client_vendor_id' => 'required',
                'invoice_path' => 'file|mimes:pdf,doc,docx|max:2048',
            ]);
            $department = 'Finance';
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'client_vendor_id',
                    'date',
                    'due_date',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'discount'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'payment_status' => 'Unpaid'
                ]
            );
            $invoice = Invoice::firstOrCreate($data);
            $invoice->periode = Carbon::parse($invoice->date)->format('Y-m');
            $invoice->save();
            if ($request->filled('service')) {
                $details = [];
                foreach ($request->input('service', []) as $i => $service_item) {
                    if (blank($service_item)) {
                        continue;
                    }
                    $details[] = [
                        'request_token' => $invoice->request_token,
                        'service_item' => $service_item,
                        'qty' => (float) $request->input("qty.$i", 0),
                        'price' => (float) $request->input("price.$i", 0),
                        'amount' => (float) $request->input("amount.$i", 0),
                    ];
                }
                if ($details !== []) {
                    $invoice
                        ->invoice_detail()
                        ->createMany($details);
                }
            }

            if ($request->has('invoice_path')) {
                $file = $request->file('invoice_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "invoice_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $invoice->invoice_path = $directory . '/' . $filename;
                $invoice->real_name = $realname;
                $invoice->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Invoice';
            if ($approval_service->checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $invoice->status = 'Approval';
                    $invoice->save();
                    $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                    $approval_service->createApprovalProcess($approval_flow_id, $invoice->id);
                }
            } else {
                if ($request->status == 'Open') {
                    if ($invoice->contract_id === null) {
                        $invoice->status = 'Done';
                    } else {
                        $invoice->status = 'Approved';
                    }
                    $invoice->save();
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
    public function show(Invoice $invoice)
    {
        $invoice_detail = $invoice->invoice_detail;
        $client = Client_vendor::find($invoice->client_vendor_id);
        $contract_no = $invoice?->contract_id !== null ? $invoice->contract->contract_no : null;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $invoice,
            'invoice' => $invoice,
            'contract_no' => $contract_no,
            'invoice_detail' => $invoice_detail,
            'client' => $client
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'date' => 'required',
                'client_vendor_id' => 'required',
                'invoice_path' => 'file|mimes:pdf,doc,docx|max:2048',
            ]);
            $department = 'Finance';
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'discount'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'payment_status' => 'Unpaid'
                ]
            );
            $lockInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->first();
            $lockInvoice->update($data);
            $lockInvoice->invoice_detail()->delete();
            // $lockInvoice->periode = Carbon::parse($lockInvoice->date)->format('Y-m');
            if ($request->filled('service')) {
                $details = [];
                foreach ($request->input('service', []) as $i => $service_item) {
                    if (blank($service_item)) {
                        continue;
                    }
                    $details[] = [
                        'service_item' => $service_item,
                        'qty' => (float) $request->input("qty.$i", 0),
                        'price' => (float) $request->input("price.$i", 0),
                        'amount' => (float) $request->input("amount.$i", 0),
                    ];
                }
                if ($details !== []) {
                    $lockInvoice
                        ->invoice_detail()
                        ->createMany($details);
                }
            }

            if ($request->has('invoice_path')) {
                $filePath = $lockInvoice->invoice_path;
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }

                $file = $request->file('invoice_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "invoice_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                $lockInvoice->invoice_path = $directory . '/' . $filename;
                $lockInvoice->real_name = $realname;
                $lockInvoice->save();
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Invoice';
            if ($approval_service->checkHasApproval($model, $department)) {
                if ($request->status == 'Open') {
                    $lockInvoice->status = 'Approval';
                    $lockInvoice->save();
                    $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                    $approval_service->createApprovalProcess($approval_flow_id, $invoice->id);
                }
            } else {
                if ($request->status == 'Open') {
                    if ($lockInvoice->contract_id === null) {
                        $lockInvoice->status = 'Done';
                    } else {
                        $lockInvoice->status = 'Approved';
                    }
                    $lockInvoice->save();
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
    public function destroy(Invoice $invoice)
    {
        DB::beginTransaction();
        try {
            Proforma_invoice::where('invoice_id', $invoice->id)
                ->update([
                    'invoice_id' => null
                ]);
            Invoice_proforma_invoice::where('invoice_id', $invoice->id)
                ->delete();
            $invoice->delete();
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
     * ngeprint
     */
    public function print(Request $request, Invoice $invoice)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $proforma_invoice_id = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->pluck('proforma_invoice_id');
        $proforma_invoice = Proforma_invoice::whereIn('id', $proforma_invoice_id)->get();
        $contract = Contract::find($invoice->contract_id);
        $contract_rate = Contract_rate::where('contract_id', $invoice->contract_id)->get();
        $contract_fmf = Contract_fmf::where('contract_id', $invoice->contract_id)->get();
        $unit_target = Unit_target::where('contract_id', $invoice->contract_id)->get();
        $periode = $invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('invoice.print', [
            'invoice' => $invoice,
            'proforma_invoice' => $proforma_invoice,
            'proforma_invoice_id' => $proforma_invoice_id,
            'contract' => $contract,
            'contract_rate' => $contract_rate,
            'contract_fmf' => $contract_fmf,
            'unit_target' => $unit_target,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status,
            'system_setting' => $system_setting,
            'year' => $year,
            'month' => $month
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();

        $fontNormal = $fontMetrics->getFont('Helvetica', 'normal');
        $fontBold = $fontMetrics->getFont('Helvetica', 'bold');

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($invoice->status, $status, true)) {
            $size = 48;
            $text = $invoice->status;

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
        $safeFilename = Str::of($invoice->invoice_no)
            ->replace(['/', '\\'], '-')
            ->toString();

        return $pdf->stream("{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Invoice $invoice)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $proforma_invoice_id = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->pluck('proforma_invoice_id');
        $proforma_invoice = Proforma_invoice::whereIn('id', $proforma_invoice_id)->get();
        $contract = Contract::find($invoice->contract_id);
        $contract_rate = Contract_rate::where('contract_id', $invoice->contract_id)->get();
        $contract_fmf = Contract_fmf::where('contract_id', $invoice->contract_id)->get();
        $unit_target = Unit_target::where('contract_id', $invoice->contract_id)->get();
        $periode = $invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('invoice.print', [
            'invoice' => $invoice,
            'proforma_invoice' => $proforma_invoice,
            'proforma_invoice_id' => $proforma_invoice_id,
            'contract' => $contract,
            'contract_rate' => $contract_rate,
            'contract_fmf' => $contract_fmf,
            'unit_target' => $unit_target,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status,
            'system_setting' => $system_setting,
            'year' => $year,
            'month' => $month
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();

        $fontNormal = $fontMetrics->getFont('Helvetica', 'normal');
        $fontBold = $fontMetrics->getFont('Helvetica', 'bold');

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($invoice->status, $status, true)) {
            $size = 48;
            $text = $invoice->status;

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
        $safeFilename = Str::of($invoice->invoice_no)
            ->replace(['/', '\\'], '-')
            ->toString();
        return $pdf->download("{$safeFilename}.pdf");
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $invoice_id)
    {
        try {
            $invoice = Invoice::find($invoice_id);
            $invoice_proforma_invoice = Invoice_proforma_invoice::where('invoice_id', $invoice_id)->get();
            $proforma_invoice_id = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->pluck('proforma_invoice_id');
            $proforma_invoice = Proforma_invoice::whereIn('id', $proforma_invoice_id)->get() ?? collect([]);
            $contract = Contract::find($invoice->contract_id) ?? collect([]);
            $contract_rate = Contract_rate::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $contract_fmf = Contract_fmf::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $unit_target = Unit_target::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Invoice')
                ->where('department', 'Finance')
                ->first();
            $approval_process = $approval_flow
                ? Approval_process::where('approval_flow_id', $approval_flow->id)
                ->where('approvable_id', $invoice->id)
                ->get()
                : null;
            $periode = $invoice->periode;
            $exp_periode = explode("-", $periode);
            $year = $exp_periode[0];
            $month = $exp_periode[1];
            $view = 'invoice.detail';
            $json_data = [
                'invoice_no' => $invoice->invoice_no,
            ];
            return response()->view($view, compact(
                'invoice',
                'proforma_invoice',
                'proforma_invoice_id',
                'invoice_proforma_invoice',
                'contract',
                'contract_rate',
                'contract_fmf',
                'unit_target',
                'approval_process',
                'year',
                'month'
            ), 200)->header('X-Json-Data', base64_encode(json_encode($json_data)));
        } catch (\Throwable $th) {
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
     * Ngambil data service
     */
    public function get_service(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $service_item = Service::selectRaw("id, name as text")
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($service_item);
            $morePages = true;
            $pagination_obj = json_encode($service_item);
            if (empty($service_item->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $service_item->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * Ngambil tabel list invoice
     */
    public function get_table_add(Request $request, Invoice $invoice)
    {
        try {
            $client_vendor = Client_vendor::find($request->client_vendor_id);
            $taxable = $client_vendor?->taxable ?? 'PKP';
            $view = 'invoice.table-add';
            $system_setting = config('system_setting');
            $year = Carbon::parse(now())->format('Y');
            $month = Carbon::parse(now())->format('m');
            $kodeDokumen = 'INV';
            $invoice_prev_no =  running_number()
                ->type('inv')
                ->formatter(new class($kodeDokumen, $year, $month) implements Presenter {
                    public function __construct(
                        private string $kodeDokumen,
                        private string $year,
                        private string $month
                    ) {}

                    public function format(string $type, int $number): string
                    {
                        return sprintf(
                            '%s/%s/%s-%03d',
                            $this->kodeDokumen,
                            $this->year,
                            $this->month,
                            $number
                        );
                    }
                })
                ->preview();
            $html = view($view, compact('system_setting', 'taxable'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'invoice_prev_no' => $invoice_prev_no,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel edit invoice
     */
    public function get_table_edit(Request $request, Invoice $invoice)
    {
        try {
            $invoice = Invoice::find($invoice->id);
            $invoice_proforma_invoice = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->get();
            $proforma_invoice_id = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->pluck('proforma_invoice_id');
            $proforma_invoice = Proforma_invoice::whereIn('id', $proforma_invoice_id)->get() ?? collect([]);
            $contract = Contract::find($invoice->contract_id) ?? collect([]);
            $contract_rate = Contract_rate::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $contract_fmf = Contract_fmf::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $unit_target = Unit_target::where('contract_id', $invoice->contract_id)->get() ?? collect([]);
            $view = 'invoice.table-edit';
            $system_setting = config('system_setting');
            $invoice_detail = $invoice->invoice_detail;
            $html = view($view, compact(
                'invoice',
                'proforma_invoice',
                'proforma_invoice_id',
                'invoice_proforma_invoice',
                'contract',
                'contract_rate',
                'contract_fmf',
                'unit_target',
                'invoice_detail',
                'system_setting'
            ))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'invoice_no' => $invoice->invoice_no,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngambil detail client vendor
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
     * Untuk update progress invoice
     * Langsung update ke proforma invoice nya
     */

    public function update_progress(Request $request, Invoice $invoice)
    {
        DB::beginTransaction();
        try {
            if ($request->cic_created_date) $invoice->cic_created_date = $request->cic_created_date;
            if ($request->cic_received_date) $invoice->cic_received_date = $request->cic_received_date;
            if ($request->inv_date) $invoice->inv_date = $request->inv_date;
            if ($request->inv_create_date) $invoice->inv_create_date = $request->inv_create_date;
            if ($request->cic_send_date) $invoice->cic_send_date = $request->cic_send_date;
            if ($request->cic_ready_to_pick_date) $invoice->cic_ready_to_pick_date = $request->cic_ready_to_pick_date;
            if ($request->cic_pick_up_date) $invoice->cic_pick_up_date = $request->cic_pick_up_date;
            if ($request->inv_send_date) $invoice->inv_send_date = $request->inv_send_date;
            $invoice->status = $request->status;
            $invoice->save();
            $invoice_proforma_invoice = Invoice_proforma_invoice::where('invoice_id', $invoice->id)->get();
            foreach ($invoice_proforma_invoice as $ipi) {
                $proforma_invoice = Proforma_invoice::find($ipi->proforma_invoice_id);
                if ($request->cic_created_date) $proforma_invoice->cic_created_date = $request->cic_created_date;
                if ($request->cic_received_date) $proforma_invoice->cic_received_date = $request->cic_received_date;
                if ($request->inv_date) $proforma_invoice->inv_date = $request->inv_date;
                if ($request->inv_create_date) $proforma_invoice->inv_create_date = $request->inv_create_date;
                if ($request->cic_send_date) $proforma_invoice->cic_send_date = $request->cic_send_date;
                if ($request->cic_ready_to_pick_date) $proforma_invoice->cic_ready_to_pick_date = $request->cic_ready_to_pick_date;
                if ($request->cic_pick_up_date) $proforma_invoice->cic_pick_up_date = $request->cic_pick_up_date;
                if ($request->inv_send_date) $proforma_invoice->inv_send_date = $request->inv_send_date;
                $proforma_invoice->save();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data saved!'
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
    public function export_file(Request $request, Proforma_invoice $proforma_invoice)
    {
        try {
            $sys_setting = Sys_setting::where('description', 'loc_ttd_siboro')->first();
            $path = public_path(
                'storage/' . $proforma_invoice->cic_path
            );

            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }

            $mimeType = mime_content_type($path);
            $extension = strtolower(
                pathinfo($path, PATHINFO_EXTENSION)
            );

            $fileName = $proforma_invoice->proforma_no . "__CIC" . $proforma_invoice->cic_number . ".pdf";
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                $tempDirectory = storage_path('app/temp/pdf');
                if (!is_dir($tempDirectory)) {
                    mkdir(
                        $tempDirectory,
                        0755,
                        true
                    );
                }
                $normalizedPath =
                    $tempDirectory
                    . DIRECTORY_SEPARATOR
                    . 'normalized_'
                    . uniqid()
                    . '.pdf';

                $process = new Process([
                    'qpdf',
                    '--object-streams=disable',
                    $path,
                    $normalizedPath,
                ]);

                $process->setTimeout(60);

                $process->run();

                if (!$process->isSuccessful()) {

                    if (file_exists($normalizedPath)) {
                        unlink($normalizedPath);
                    }

                    throw new \RuntimeException(
                        'Failed to normalize PDF: '
                            . $process->getErrorOutput()
                    );
                }

                $signaturePath = public_path(
                    'assets/images/ttd_siboro.png'
                );

                if (!file_exists($signaturePath)) {

                    if (file_exists($normalizedPath)) {
                        unlink($normalizedPath);
                    }

                    abort(404, 'Signature not found.');
                }

                $pdf = new Fpdi();

                $pageCount = $pdf->setSourceFile(
                    $normalizedPath
                );

                $signatureWidth = (int) $sys_setting->val_1; //35; // mm
                $bottomMargin = (int) $sys_setting->val_2; //10;   // mm

                [$imageWidth, $imageHeight] =
                    getimagesize($signaturePath);

                if (!$imageWidth || !$imageHeight) {
                    throw new \RuntimeException(
                        'Invalid signature image.'
                    );
                }

                $signatureHeight =
                    $signatureWidth
                    * ($imageHeight / $imageWidth);

                for (
                    $pageNo = 1;
                    $pageNo <= $pageCount;
                    $pageNo++
                ) {


                    $templateId = $pdf->importPage(
                        $pageNo
                    );


                    $size = $pdf->getTemplateSize(
                        $templateId
                    );


                    $pdf->AddPage(
                        $size['orientation'],
                        [
                            $size['width'],
                            $size['height'],
                        ]
                    );


                    $pdf->useTemplate(
                        $templateId
                    );


                    $x =
                        ($size['width'] - $signatureWidth - (int) $sys_setting->val_3)
                        / 2;


                    //Val 3
                    $y =
                        $size['height']
                        - $signatureHeight
                        - $bottomMargin;

                    $pdf->Image(
                        $signaturePath,
                        $x,
                        $y,
                        $signatureWidth
                    );
                }


                $outputPath =
                    $tempDirectory
                    . DIRECTORY_SEPARATOR
                    . 'signed_'
                    . uniqid()
                    . '.pdf';

                $pdf->Output(
                    'F',
                    $outputPath
                );

                if (file_exists($normalizedPath)) {
                    unlink($normalizedPath);
                }

                return response()
                    ->file(
                        $outputPath,
                        [
                            'Content-Type' =>
                            'application/pdf',

                            'Content-Disposition' =>
                            'inline; filename="'
                                . $fileName
                                . '"',
                        ]
                    )
                    ->deleteFileAfterSend(true);
            }

            /*
    |--------------------------------------------------------------------------
    | Non-PDF
    |--------------------------------------------------------------------------
    */

            return response()->download(
                $path,
                $proforma_invoice->real_name,
                [
                    'Content-Type' =>
                    $mimeType
                        ?: 'application/octet-stream',
                ]
            );
        } catch (HttpException $e) {

            throw $e;
        } catch (\Throwable $th) {

            /*
     * Log error sebenarnya.
     * Jangan hanya menelan exception.
     */
            \Log::error(
                'Failed to open/sign CIC PDF',
                [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                ]
            );
            return redirect()
                ->route('invoice.index')
                ->with(
                    'error',
                    'Failed to open file.'
                );
        }
    }
}
