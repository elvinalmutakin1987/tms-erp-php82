<?php

namespace App\Http\Controllers;


use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Daily_report;
use App\Models\Daily_report_detail;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
use App\Models\Proforma_invoice_detail;
use App\Models\Purchase_requisition;
use App\Models\Unit;
use App\Models\Unit_target;
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
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Spatie\Permission\Models\Permission;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\ProformaInvoiceService;
use App\Services\ApprovalService;


class ProgressClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $proforma_invoice = Proforma_invoice::query();
            if (request()->status != 'All' && request()->status != '') {
                $proforma_invoice = $proforma_invoice->where('status', request()->status);
            }
            if (request()->year != 'All') {
                if (request()->month != 'All') {
                    $proforma_invoice = $proforma_invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $proforma_invoice = $proforma_invoice->where('periode', 'like', request()->year . '-%');
                }
            }
            if (request()->month != 'All') {
                if (request()->year != 'All') {
                    $proforma_invoice = $proforma_invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $proforma_invoice = $proforma_invoice->where('periode', 'like', '%-' . request()->month);
                }
            }
            $proforma_invoice = $proforma_invoice
                ->whereHas('contract.service', function ($query) {
                    $query->where('type', '=', 'Survey');
                })
                ->orderBy('id', 'desc')
                ->get();
            // $proforma_invoice = $proforma_invoice->orderBy('id', 'desc')->get();
            $user = Auth::user();
            $permissionNames = [
                'proforma_invoice.edit',
                'proforma_invoice.update_progress',
                'proforma_invoice.delete',
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
            return DataTables::of($proforma_invoice)
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
                                        <a class="dropdown-item exportPdfButton" href="' . route('progressclaim.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('progressclaim.print', $item->id) . '" target="_blank">
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
                    if (($item->status === 'Draft' && $canAccess('proforma_invoice.edit')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formEdit" data-id="' . $item->id . '">
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
                    if (($item->status === 'Approved' && $canAccess('proforma_invoice.update_progress')) || Auth::user()->hasRole('superadmin')) {
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
                    if (($item->status !== 'Done' && $canAccess('proforma_invoice.delete')) || Auth::user()->hasRole('superadmin')) {
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
                ->addColumn('unit', function ($item) {
                    return $item->unit?->vehicle_no ?? '';
                })
                ->addColumn('contract_no', function ($item) {
                    return $item->contract->contract_no ?? '';
                })
                ->addColumn('type', function ($item) {
                    return $item->contract->service->type ?? '';
                })
                ->addColumn('periode_', function ($item) {
                    return Carbon::parse($item->periode)->format('F Y') ?? '';
                })
                ->addColumn('price_', function ($item) {
                    return Number::format($item->price ?? 0, precision: 0) ?? '';
                })
                ->addColumn('penalty_', function ($item) {
                    return Number::format($item->penalty ?? 0, precision: 0) ?? '';
                })
                ->addColumn('total_', function ($item) {
                    return Number::format($item->total ?? 0, precision: 0) ?? '';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $contract = Contract::where('status', 'Active')
            ->whereHas('service', function ($query) {
                $query->where('type', '=', 'Survey');
            })
            ->get();
        $breadcrum = [
            'module' => 'Survey',
            'route-module' => null,
            'sub-module' => 'Progress Claim',
            'route-sub-module' => 'progressclaim.index'
        ];
        return view('progress_claim.index', compact('breadcrum', 'contract'));
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
    public function store(Request $request,  ProformaInvoiceService $proforma_invoice_service, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $excelRound = function ($value, int $precision = 2) {
                return round((float) $value, $precision, PHP_ROUND_HALF_UP);
            };
            $request->validate([
                'contract_id' => 'required',
                'year' => 'required',
                'month' => 'required'
            ]);
            $contract = Contract::find($request->contract_id);
            $year = $request->year;
            $month = $request->month;
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            if ($proforma_invoice_service->checkProformaInvoice($contract, $year, $month)) {
                return response()->json([
                    'success' => false,
                    'title' => 'Oops...',
                    'message' => 'Proforma Invoice on this periode already created.!'
                ], 200);
            }
            $gen_proforma = $proforma_invoice_service->genProformaInvoice($contract, $year, $month);
            $start_date = Carbon::create($year, $month, 1)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
            $fix_monthly_fee = $gen_proforma['fix_monthly_fee'];
            $fmf_qty = 1;
            $total = 0;
            $total_ptd = 0;
            $amount = 0;
            $qty_ptd = 0;
            $amount_ptd = 0;
            $fmf_qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                ->where('contract_fmf_id', $gen_proforma['contract_fmf']->id)
                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                ->count();
            $fmf_amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                ->where('contract_fmf_id', $gen_proforma['contract_fmf']->id)
                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                ->sum('value');
            $amount = $fix_monthly_fee * $fmf_qty;
            $qty_ptd = $fmf_qty_ptd + $fmf_qty;
            $amount_ptd = $fmf_amount_ptd + $amount;
            $total += $amount;
            $total_ptd += $amount_ptd;
            $proforma_invoice = Proforma_invoice::firstOrCreate([
                'contract_id' => $contract->id,
                'client_vendor_id' => $contract->client_vendor_id,
                'request_token' => $request->request_token,
                'user_id' => Auth::id(),
                'periode_start' => $start_date,
                'periode_finish' => $end_date,
                'periode' => Carbon::parse("$year-$month")->format('Y-m'),
                'total' => $total,
                'type' => $contract->service->type,
                'status' => $request->status
            ]);
            $proforma_invoice->proforma_invoice_detail()->create([
                'request_token' => $proforma_invoice->request_token,
                'proforma_invoice_id' => $proforma_invoice->id,
                'contract_fmf_id' => $gen_proforma['contract_fmf']->id,
                'contract_id' => $contract->id,
                'item_no' => '',
                'service_item' => 'Fix Monthly Fee',
                'rate' => $gen_proforma['contract_fmf']->value,
                'value' => $gen_proforma['contract_fmf']->value,
                'qty' => 1,
                'amount' => $amount,
                'ptd_qty' => $qty_ptd,
                'ptd_amount' => $amount_ptd
            ]);
            $this->check_approval($proforma_invoice, $request->status, $approval_service);
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
        $proforma_no = $proforma_invoice->proforma_no;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_id = $contract->id;
        $contract_no = $contract->contract_no;
        $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
        $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
        $unit_target = Unit_target::find($proforma_invoice->unit_target_id);
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')
            ->where('department', 'Survey')
            ->first();
        $approval_process = $approval_flow
            ? Approval_process::where('approval_flow_id', $approval_flow->id)
            ->where('approvable_id', $proforma_invoice->id)
            ->get()
            : null;
        $periode = $proforma_invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $month_name = $this->convertMonthName($exp_periode[1]);
        $html = view('progress_claim.table-edit', compact(
            'proforma_invoice',
            'contract',
            'contract_rate',
            'contract_fmf',
            'unit_target',
            'approval_process',
            'year',
            'month',
            'month_name'
        ))->render();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'year' => $year,
            'month' => $month,
            'month_name' => $month_name,
            'contract_id' => $contract_id,
            'contract_no' => $contract_no,
            'proforma_no' => $proforma_no,
            'proforma_invoice' => $proforma_invoice,
            'unit' => $proforma_invoice->unit->vehicle_no,
            'html' => $html
        ], 200);
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
    public function update(Request $request, Proforma_invoice $proforma_invoice, ProformaInvoiceService $proforma_invoice_service, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'edit_contract_id' => 'required',
                'edit_year' => 'required',
                'edit_month' => 'required'
            ]);
            $contract = Contract::find($proforma_invoice->contract_id);
            $unit_target_id = $proforma_invoice->unit_target_id;
            $unit_target = Unit_target::find($unit_target_id);
            $unit_id = $proforma_invoice->unit_id;
            $year = $request->edit_year;
            $month = $request->edit_month;
            $start_date = Carbon::create($year, $month, 1)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $gen_proforma = $proforma_invoice_service->genProformaInvoice($contract, $year, $month);
            $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
            $fix_monthly_fee = $gen_proforma['fix_monthly_fee'];
            $fmf_qty = 1;
            $total = 0;
            $total_ptd = 0;
            $amount = 0;
            $qty_ptd = 0;
            $amount_ptd = 0;
            $fmf_qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                ->where('contract_fmf_id', $gen_proforma['contract_fmf']->id)
                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                ->count();
            $fmf_amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                ->where('contract_fmf_id', $gen_proforma['contract_fmf']->id)
                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                ->sum('value');
            $amount = $fix_monthly_fee * $fmf_qty;
            $qty_ptd = $fmf_qty_ptd + $fmf_qty;
            $amount_ptd = $fmf_amount_ptd + $amount;
            $total += $amount;
            $total_ptd += $amount_ptd;
            $data = [
                'request_token' => $contract->request_token,
                'user_id' => Auth::id(),
                'periode_start' => $start_date,
                'periode_finish' => $end_date,
                'periode' => Carbon::parse("$year-$month")->format('Y-m'),
                'total' => $total,
                'type' => $contract->service->type,
                'status' => $request->status
            ];
            $lockProforma_invoice = Proforma_invoice::where('id', $proforma_invoice->id)->lockForUpdate()->first();
            $lockProforma_invoice->update($data);
            $lockProforma_invoice->proforma_invoice_detail()->delete();
            $lockProforma_invoice->proforma_invoice_detail()->create([
                'request_token' => $lockProforma_invoice->request_token,
                'proforma_invoice_id' => $lockProforma_invoice->id,
                'contract_fmf_id' => $gen_proforma['contract_fmf']->id,
                'contract_id' => $contract->id,
                'rate' => $gen_proforma['contract_fmf']->value,
                'value' => $gen_proforma['contract_fmf']->value,
                'service_item' => 'Fix Monthly Fee',
                'qty' => 1,
                'amount' => $amount,
                'ptd_qty' => $qty_ptd,
                'ptd_amount' => $amount_ptd
            ]);
            $this->check_approval($lockProforma_invoice, $request->status, $approval_service);
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
    public function destroy(Proforma_invoice $proforma_invoice)
    {
        DB::beginTransaction();
        try {
            $proforma_invoice->proforma_invoice_detail()->delete();
            $proforma_invoice->delete();
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
     * Ngambil tabel add
     */
    public function get_table_add(Request $request, ProformaInvoiceService $proforma_invoice_service)
    {
        try {
            $contract = Contract::find($request->contract_id);
            $month = $request->month;
            $year = $request->year;
            $data = $proforma_invoice_service->genProformaInvoice($contract, $year, $month);
            $kodeDokumen = 'P-INV';
            $year_no = date('y');
            $month_no = date('m');
            $proforma_prev_no = running_number()
                ->type('pro-inv')
                ->formatter(new class($kodeDokumen, $year_no, $month_no) implements Presenter {
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
            $html = view('progress_claim.table-add', compact(
                'data',
                'contract',
                'year',
                'month'
            ))->render();

            /**
             * Check contract id sudah dibuatkan proforma invoice di bulan ini atau belum
             */
            $doc_status = 0;
            if ($proforma_invoice_service->checkProformaInvoice($contract, $year, $month)) {
                $doc_status = 1;
            }
            return response()->json([
                'success' => true,
                'html' => $html,
                'proforma_prev_no' => $proforma_prev_no,
                'data' => $data,
                'doc_status' => $doc_status,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }


    public function check_approval(Proforma_invoice $proforma_invoice, string $status, ApprovalService $approval_service)
    {
        $model = 'App\Models\Proforma_invoice';
        $department = 'Survey';
        if ($approval_service->checkHasApproval($model, $department)) {
            if ($status == 'Open') {
                $proforma_invoice->status = 'Approval';
                $proforma_invoice->save();
                $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                $approval_service->createApprovalProcess($approval_flow_id, $proforma_invoice->id);
            }
        } else {
            if ($status == 'Open') {
                $proforma_invoice->status = 'Approved';
                $proforma_invoice->save();
            }
        }
    }

    public function check_proforma_invoice(Request $request, ProformaInvoiceService $proforma_invoice_service)
    {
        $contract = Contract::find($request->contract_id);
        $year = $request->year;
        $month = $request->month;
        return response()->json([
            'status' => $proforma_invoice_service->checkProformaInvoice($contract, $year, $month)
        ], 200);
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $proforma_invoice_id)
    {
        try {
            $proforma_invoice = Proforma_invoice::find($proforma_invoice_id);
            $contract = Contract::find($proforma_invoice->contract_id);
            $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
            $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
            $unit_target = Unit_target::find($proforma_invoice->unit_target_id);
            $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')
                ->where('department', 'Equipment')
                ->first();
            $approval_process = $approval_flow
                ? Approval_process::where('approval_flow_id', $approval_flow->id)
                ->where('approvable_id', $proforma_invoice->id)
                ->get()
                : null;
            $periode = $proforma_invoice->periode;
            $exp_periode = explode("-", $periode);
            $year = $exp_periode[0];
            $month = $exp_periode[1];
            $view = 'progress_claim.detail';
            $json_data = [
                'proforma_no' => $proforma_invoice->proforma_no,
            ];
            return response()->view($view, compact(
                'proforma_invoice',
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

    public function convertMonthName(string $month)
    {
        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        return $months[$month] ?? null;
    }

    /**
     * ngeprint
     */
    public function print(Request $request, Proforma_invoice $proforma_invoice)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')
            ->where('department', 'Survey')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_rate = Contract_rate::where('contract_id', $proforma_invoice->contract_id)->get();
        $contract_fmf = Contract_fmf::where('contract_id', $proforma_invoice->contract_id)->get();
        $unit_target = Unit_target::where('contract_id', $proforma_invoice->contract_id)->get();
        $periode = $proforma_invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('progress_claim.print', [
            'proforma_invoice' => $proforma_invoice,
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

        if (in_array($proforma_invoice->status, ['Approved', 'CIC Approval', 'Invoicing', 'Done'], true)) {
            $qrText = 'PT. Tunas Mitra Sejati' . "\n" . "\n" .
                'Nomor Proforma Invoice : ' . $proforma_invoice->proforma_no . "\n" .
                'Tanggal : ' . Carbon::parse($proforma_invoice->date)->format('d-m-Y') . "\n" .
                'Client : ' . optional($proforma_invoice->client_vendor)->name . "\n" .
                'Total : ' . Number::format($proforma_invoice->total, 0) . "\n" .
                'Telah disetujui secara digital.';

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
        }
        $canvas->page_text(
            $width - 120,
            $height - 35,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $fontNormal,
            10,
            [0, 0, 0]
        );
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($proforma_invoice->status, $status, true)) {
            $size = 48;
            $text = $proforma_invoice->status;

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
        $safeFilename = Str::of($proforma_invoice->proforma_no)
            ->replace(['/', '\\'], '-')
            ->toString();

        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Proforma_invoice $proforma_invoice)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')
            ->where('department', 'Survey')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_rate = Contract_rate::where('contract_id', $proforma_invoice->contract_id)->get();
        $contract_fmf = Contract_fmf::where('contract_id', $proforma_invoice->contract_id)->get();
        $unit_target = Unit_target::where('contract_id', $proforma_invoice->contract_id)->get();
        $periode = $proforma_invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('progress_claim.print', [
            'proforma_invoice' => $proforma_invoice,
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

        if (in_array($proforma_invoice->status, ['Approved', 'Approval', 'Received', 'Done'], true)) {
            $qrText = 'PT. Tunas Mitra Sejati' . "\n" . "\n" .
                'Nomor Proforma Invoice : ' . $proforma_invoice->proforma_no . "\n" .
                'Tanggal : ' . Carbon::parse($proforma_invoice->date)->format('d-m-Y') . "\n" .
                'Client : ' . optional($proforma_invoice->client_vendor)->name . "\n" .
                'Total : ' . Number::format($proforma_invoice->total, 0) . "\n" .
                'Telah disetujui secara digital.';

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
        }
        $canvas->page_text(
            $width - 120,
            $height - 35,
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $fontNormal,
            10,
            [0, 0, 0]
        );
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received'];
        if (in_array($proforma_invoice->status, $status, true)) {
            $size = 48;
            $text = $proforma_invoice->status;

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
        $safeFilename = Str::of($proforma_invoice->proforma_no)
            ->replace(['/', '\\'], '-')
            ->toString();
        return $pdf->download("{$safeFilename}.pdf");
    }

    /*
    * Update progress proforma invoice
    */
    public function update_progress(Request $request, Proforma_invoice $proforma_invoice, ProformaInvoiceService $proforma_invoice_service)
    {
        DB::beginTransaction();
        try {
            $lockProforma_invoice = Proforma_invoice::where('id', $proforma_invoice->id)->lockForUpdate()->first();
            if ($request->cut_off_date) $lockProforma_invoice->cut_off_date = $request->cut_off_date;
            if ($request->consolidation_date) $lockProforma_invoice->consolidation_date = $request->consolidation_date;
            if ($request->progress_claim_date) $lockProforma_invoice->progress_claim_date = $request->progress_claim_date;
            if ($request->ops_received_date) $lockProforma_invoice->ops_received_date = $request->ops_received_date;
            if ($request->prof_inv_app_date) $lockProforma_invoice->prof_inv_app_date = $request->prof_inv_app_date;
            if ($request->cic_request_date) $lockProforma_invoice->cic_request_date = $request->cic_request_date;
            $lockProforma_invoice->status = $request->status;
            $lockProforma_invoice->save();
            if ($request->status == 'Done') {
                $proforma_invoice_service->genInvoiceFromProforma($lockProforma_invoice);
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
