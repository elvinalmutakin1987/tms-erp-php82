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
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
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
            if (request()->unit_id != '') {
                $proforma_invoice = $proforma_invoice->where('unit_id', request()->unit_id);
            }
            if (request()->year != 'All') {
                if (request()->month != 'All') {
                    $proforma_invoice = $proforma_invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $proforma_invoice = $proforma_invoice->where('periode', 'like', request()->year . '-%');
                }
            }
            $proforma_invoice = $proforma_invoice->orderBy('id', 'desc')->get();
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
                                        <a class="dropdown-item exportPdfButton" href="' . route('proformainvoice.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('proformainvoice.print', $item->id) . '" target="_blank">
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
                                <a class="dropdown-item updateButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal" data-id="' . $item->id . '">
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
                    return Number::format($item->price, precision: 0) ?? '';
                })
                ->addColumn('penalty_', function ($item) {
                    return Number::format($item->penalty, precision: 0) ?? '';
                })
                ->addColumn('total_', function ($item) {
                    return Number::format($item->total, precision: 0) ?? '';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $contract = Contract::where('status', 'Active')->get();
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Proforma Invoice',
            'route-sub-module' => 'proformainvoice.index'
        ];
        return view('proforma_invoice.index', compact('breadcrum', 'contract'));
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
                'contract_id' => 'required',
                'year' => 'required',
                'month' => 'required'
            ]);
            $contract = Contract::find($request->contract_id);
            $year = $request->year;
            $month = $request->month;
            if (checkProformaInvoice($contract, $year, $month)) {
                return response()->json([
                    'success' => false,
                    'title' => 'Oops...',
                    'message' => 'Proforma Invoice on this periode already created.!'
                ], 200);
            }
            $gen_proforma = genProformaInvoice($contract, $year, $month);
            $start_date = Carbon::create($year, $month, 1)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            if ($contract->service->type == 'Unit Rental') {
                foreach ($gen_proforma['unit_target'] as $unittarget) {
                    $excelRound = function ($value, int $precision = 2) {
                        return round((float) $value, $precision, PHP_ROUND_HALF_UP);
                    };
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate = $startDate->copy()->endOfMonth();
                    $hariKerja = $startDate->daysInMonth;
                    $totalJamKerja = $hariKerja * 24;
                    $totalBreakdownSeconds = Maintenance::whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->where('unit_id', $unittarget->unit_id)
                        ->where('status', '!=', 'Draft')
                        ->selectRaw('COALESCE(SUM(TIME_TO_SEC(work_duration)), 0) as total_seconds')
                        ->value('total_seconds');
                    $dailyReport = Daily_report::whereBetween('date', [$start_date, $end_date])
                        ->where('unit_id', $unittarget->unit_id);
                    $km_awal = (clone $dailyReport)
                        ->orderBy('date')
                        ->orderBy('id')
                        ->value('km_start');
                    $km_akhir = (clone $dailyReport)
                        ->orderByDesc('date')
                        ->orderByDesc('id')
                        ->value('km_finish');
                    $total_breakdown = $excelRound($totalBreakdownSeconds / 3600, 2);
                    $price = $excelRound($unittarget->price ?? 0, 2);
                    $target = $excelRound($unittarget->target ?? 0, 2);
                    if ($totalJamKerja > 0) {
                        $pa = 100 - ($total_breakdown / $totalJamKerja) * 100;
                    } else {
                        $pa = 0;
                    }
                    $pa = max(0, min(100, $pa));
                    $pa = $excelRound($pa, 2);
                    $penalty = $pa >= $target ? 0 : $excelRound(((100 - $pa) / 100) * $price, 2);
                    $total_payment = $price - $penalty;
                    $total_payment = max(0, min($price, $total_payment));
                    $total_payment = $excelRound($total_payment, 2);
                    $proforma_invoice = Proforma_invoice::firstOrCreate([
                        'contract_id' => $request->contract_id,
                        'unit_target_id' => $unittarget->id,
                        'client_vendor_id' => $contract->client_vendor_id,
                        'request_token' => $request->request_token,
                        'user_id' => Auth::id(),
                        'unit_id' => $unittarget->unit_id,
                        'periode_start' => $start_date,
                        'periode_finish' => $end_date,
                        'periode' => Carbon::parse("$year-$month")->format('Y-m'),
                        'target' => $target,
                        'price' => $price,
                        'pa' => $pa,
                        'penalty' => $penalty,
                        'total' => $total_payment,
                        'km_awal' => $km_awal,
                        'km_akhir' => $km_akhir,
                        'type' => $contract->service->type,
                        'status' => $request->status
                    ]);
                    $this->check_approval($proforma_invoice, $request->status);
                }
            } else if ($contract->service->type == 'LCT') {
                $data = [
                    'contract_id' => $request->contract_id,
                    'client_vendor_id' => $contract->client_vendor_id,
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                ];
            } else if ($contract->service->type == 'Fuel Truck Rental') {
                $data = [
                    'contract_id' => $request->contract_id,
                    'client_vendor_id' => $contract->client_vendor_id,
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                ];
            } else if ($contract->service->type == 'Explo') {
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
        $proforma_no = $proforma_invoice->proforma_no;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_id = $contract->id;
        $contract_no = $contract->contract_no;
        $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
        $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
        $unit_target = Unit_target::find($proforma_invoice->unit_target_id);
        $unit_id = $unit_target->unit_id;
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
        $month_name = $this->convertMonthName($exp_periode[1]);
        $html = view('proforma_invoice.table-rental-edit', compact(
            'proforma_invoice',
            'contract',
            'contract_rate',
            'contract_fmf',
            'unit_target',
            'approval_process',
            'year',
            'month',
            'month_name',
            'unit_id'
        ))->render();
        if ($contract->type == 'LCT') {
            $html = 'proforma_invoice.table-lct-edit';
        } else if ($contract->type == 'Fuel Truck Rental') {
            $html = 'proforma_invoice.table-fuel-edit';
        }
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'year' => $year,
            'month' => $month,
            'month_name' => $month_name,
            'contract_id' => $contract_id,
            'contract_no' => $contract_no,
            'proforma_no' => $proforma_no,
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
    public function update(Request $request, Proforma_invoice $proforma_invoice)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'edit_contract_id' => 'required',
                'edit_year' => 'required',
                'edit_month' => 'required'
            ]);
            $contract = Contract::find($request->edit_contract_id);
            $unit_target_id = $proforma_invoice->unit_target_id;
            $unit_target = Unit_target::find($unit_target_id);
            $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
            $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
            $unit_id = $proforma_invoice->unit_id;
            $year = $request->edit_year;
            $month = $request->edit_month;
            $start_date = Carbon::create($year, $month, 1)->startOfMonth();
            $end_date = $start_date->copy()->endOfMonth();
            if ($contract->service->type == 'Unit Rental') {
                $excelRound = function ($value, int $precision = 2) {
                    return round((float) $value, $precision, PHP_ROUND_HALF_UP);
                };
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $hariKerja = $startDate->daysInMonth;
                $totalJamKerja = $hariKerja * 24;
                $totalBreakdownSeconds = Maintenance::whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->where('unit_id', $unit_id)
                    ->where('status', '!=', 'Draft')
                    ->selectRaw('COALESCE(SUM(TIME_TO_SEC(work_duration)), 0) as total_seconds')
                    ->value('total_seconds');
                $dailyReport = Daily_report::whereBetween('date', [$start_date, $end_date])
                    ->where('unit_id', $unit_id);
                $km_awal = (clone $dailyReport)
                    ->orderBy('date')
                    ->orderBy('id')
                    ->value('km_start');
                $km_akhir = (clone $dailyReport)
                    ->orderByDesc('date')
                    ->orderByDesc('id')
                    ->value('km_finish');
                $total_breakdown = $excelRound($totalBreakdownSeconds / 3600, 2);
                $price = $excelRound($unit_target->price ?? 0, 2);
                $target = $excelRound($unit_target->target ?? 0, 2);
                if ($totalJamKerja > 0) {
                    $pa = 100 - ($total_breakdown / $totalJamKerja) * 100;
                } else {
                    $pa = 0;
                }
                $pa = max(0, min(100, $pa));
                $pa = $excelRound($pa, 2);
                $penalty = $pa >= $target ? 0 : $excelRound(((100 - $pa) / 100) * $price, 2);
                $total_payment = $price - $penalty;
                $total_payment = max(0, min($price, $total_payment));
                $total_payment = $excelRound($total_payment, 2);
                $data = [
                    'contract_id' => $contract->id,
                    'unit_target_id' => $unit_target_id,
                    'client_vendor_id' => $contract->client_vendor_id,
                    'request_token' => $request->request_token,
                    'user_id' => Auth::id(),
                    'unit_id' => $unit_id,
                    'periode_start' => $start_date,
                    'periode_finish' => $end_date,
                    'periode' => Carbon::parse("$year-$month")->format('Y-m'),
                    'target' => $target,
                    'price' => $price,
                    'pa' => $pa,
                    'penalty' => $penalty,
                    'total' => $total_payment,
                    'km_awal' => $km_awal,
                    'km_akhir' => $km_akhir,
                    'type' => $contract->service->type,
                    'status' => $request->status
                ];
                $lockProforma_invoice = Proforma_invoice::where('id', $proforma_invoice->id)->lockForUpdate()->first();
                $lockProforma_invoice->update($data);
                $this->check_approval($proforma_invoice, $request->status);
            } else if ($contract->service->type == 'LCT') {
                $data = [
                    'contract_id' => $request->contract_id,
                    'client_vendor_id' => $contract->client_vendor_id,
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                ];
            } else if ($contract->service->type == 'Fuel Truck Rental') {
                $data = [
                    'contract_id' => $request->contract_id,
                    'client_vendor_id' => $contract->client_vendor_id,
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                ];
            } else if ($contract->service->type == 'Explo') {
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
    public function destroy(Proforma_invoice $proforma_invoice)
    {
        DB::beginTransaction();
        try {
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
    public function get_table_add(Request $request)
    {
        try {
            $contract = Contract::find($request->contract_id);
            $month = $request->month;
            $year = $request->year;
            $data = genProformaInvoice($contract, $year, $month);
            $kodeDokumen = 'P-INV';
            $year = date('y');
            $month = date('m');
            $proforma_prev_no = running_number()
                ->type('pro-inv')
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
            $view = 'proforma_invoice.table-rental-add';
            if ($contract->type == 'LCT') {
                $view = 'proforma_invoice.table-lct-add';
            } else if ($contract->type == 'Fuel Truck Rental') {
                $view = 'proforma_invoice.table-fuel-add';
            }
            /**
             * Check contract id sudah dibuatkan proforma invoice di bulan ini atau belum
             */
            $doc_status = 0;
            if (checkProformaInvoice($contract, $year, $month)) {
                $doc_status = 1;
            }
            $html = view($view, compact('data', 'contract', 'year', 'month'))->render();
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

    /**
     * Ngambil data unit
     */
    public function get_unit_all(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $unit = Unit::selectRaw("id, vehicle_no as text")
                ->where('vehicle_no', 'like', '%' . $term . '%')
                ->orderBy('vehicle_no')
                ->simplePaginate(10);
            $total_count = count($unit);
            $morePages = true;
            $pagination_obj = json_encode($unit);
            if (empty($unit->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $unit->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    public function check_approval(Proforma_invoice $proforma_invoice, string $status)
    {
        $model = 'App\Models\Proforma_invoice';
        $department = 'Equipment';
        if (checkHasApproval($model, $department)) {
            if ($status == 'Open') {
                $proforma_invoice->status = 'Approval';
                $proforma_invoice->save();
                $approval_flow_id = getApprovalFlowId($model, $department);
                createApprovalProcess($approval_flow_id, $proforma_invoice->id);
            }
        } else {
            if ($status == 'Open') {
                $proforma_invoice->status = 'Approved';
                $proforma_invoice->save();
            }
        }
    }

    public function check_proforma_invoice(Request $request)
    {
        $contract = Contract::find($request->contract_id);
        $year = $request->year;
        $month = $request->month;
        return response()->json([
            'status' => checkProformaInvoice($contract, $year, $month)
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
            $view = 'proforma_invoice.detail';
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
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
        $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
        $unit_target = Unit_target::find($proforma_invoice->unit_target_id);
        $periode = $proforma_invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('proforma_invoice.print', [
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
        $safeFilename = Str::of($proforma_invoice->order_no)
            ->replace(['/', '\\'], '-')
            ->toString();

        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Proforma_invoice $proforma_invoice)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Proforma_invoice')->first();
        $approval_step = $approval_flow ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_rate = Contract_rate::find($proforma_invoice->contract_rate_id);
        $contract_fmf = Contract_fmf::find($proforma_invoice->contract_fmf_id);
        $unit_target = Unit_target::find($proforma_invoice->unit_target_id);
        $periode = $proforma_invoice->periode;
        $exp_periode = explode("-", $periode);
        $year = $exp_periode[0];
        $month = $exp_periode[1];
        $system_setting = config('system_setting');
        $pdf = Pdf::loadView('proforma_invoice.print', [
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
}
