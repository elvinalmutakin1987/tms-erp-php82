<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Invoice;
use App\Models\Invoice_detail;
use App\Models\Invoice_proforma_invoice;
use App\Models\Proforma_invoice;
use App\Models\Proforma_invoice_detail;
use App\Models\Unit_target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\ProformaInvoiceService;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CICController extends Controller
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
            $proforma_invoice = $proforma_invoice->orderBy('id', 'desc')->get();
            $user = Auth::user();
            $permissionNames = [
                'cic.edit',
                'cic.update_progress',
                'cic.create_invoice'
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
                                        <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail" data-id="' . $item->id . '">
                                            Detail
                                        </a>
                                    </li>
                    ';

                    /**
                     * Tombol Update Progress:
                     * - hanya muncul jika status Approved
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.update_progress
                     */
                    if (($canAccess('cic.update_progress')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item updateButton" href="#" data-bs-toggle="modal" data-bs-target="#formUpdate" data-id="' . $item->id . '" 
                                    data-status="' . $item->status . '">
                                    Update Progress
                                </a>
                            </li>
                        ';
                    }

                    /**
                     * Tombol Create invoice:
                     * - Buat create data invoice nya
                     */
                    if (($item->invoice_id === null && $canAccess('cic.create_invoice')) || Auth::user()->hasRole('superadmin')) {
                        // $button .= '
                        //     <li>
                        //         <a class="dropdown-item createButton" href="#" data-bs-toggle="modal" data-bs-target="#formCreate" data-id="' . $item->id . '">
                        //             Create Invoice
                        //         </a>
                        //     </li>
                        // ';
                        $button .= '
                            <li>
                                <a class="dropdown-item" href="#" onclick="create_invoice(\'' . $item->id . '\')">
                                    Create Invoice
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
                    return $item->contract->service->name ?? '';
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
                ->addColumn('invoice_no', function ($item) {
                    $invoice = Invoice::find($item->invoice_id);
                    return $invoice?->invoice_no ?? '-';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $contract = Contract::where('status', 'Active')->get();
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'Sales',
            'route-sub-module' => null,
            'sub-sub-module' => 'CIC',
            'route-sub-sub-module' => 'cic.index'
        ];
        return view('cic.index', compact('breadcrum', 'contract'));
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
    public function show(string $id)
    {
        $proforma_invoice  = Proforma_invoice::find($id);
        $invoice  = Invoice::find($proforma_invoice->invoice_id);
        $invoice_no = $invoice?->invoice_no ?? '-';
        $proforma_no = $proforma_invoice->proforma_no;
        $contract = Contract::find($proforma_invoice->contract_id);
        $contract_id = $contract->id;
        $contract_no = $contract->contract_no;
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
        $month_name = $this->convertMonthName($exp_periode[1]);
        $html = "";
        if ($proforma_invoice->cic_path !== null) {
            $html = '<table style="width: 100%">';
            if ($proforma_invoice) {
                $html .= '<tr>';
                $html .= '<td style="width:5%">';
                $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_file(\'' . $proforma_invoice->id . '\')"><i class="bx bx-trash me-0"></i></a>';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<a href="' . route('cic.export_file', $proforma_invoice->id) . '" target="_blank">' . $proforma_invoice->real_name . '</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr><td class="text-center">No quotation file</td></tr>';
            }
            $html .= '</table>';
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
            'proforma_invoice' => $proforma_invoice,
            'invoice' => $invoice,
            'invoice_no' => $invoice_no,
            'unit' => $proforma_invoice->unit->vehicle_no,
            'html' => $html
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $request->validate(
                [
                    'cic_path' => [
                        'nullable',
                        'file',
                        'mimes:pdf',
                        'mimetypes:application/pdf',
                        'max:5120',
                    ],
                ],
                [
                    'cic_path.file' => 'File CIC tidak valid.',
                    'cic_path.mimes' => 'File CIC hanya boleh berupa PDF.',
                    'cic_path.mimetypes' => 'File CIC harus memiliki format PDF.',
                    'cic_path.max' => 'Ukuran file CIC maksimal 5 MB.',
                ]
            );
            $lockProforma_invoice = Proforma_invoice::where('id', $id)->lockForUpdate()->first();
            if ($request->cut_off_date) $lockProforma_invoice->cut_off_date = $request->cut_off_date;
            if ($request->consolidation_date) $lockProforma_invoice->consolidation_date = $request->consolidation_date;
            if ($request->progress_claim_date) $lockProforma_invoice->progress_claim_date = $request->progress_claim_date;
            if ($request->ops_received_date) $lockProforma_invoice->ops_received_date = $request->ops_received_date;
            if ($request->prof_inv_app_date) $lockProforma_invoice->prof_inv_app_date = $request->prof_inv_app_date;
            if ($request->cic_request_date) $lockProforma_invoice->cic_request_date = $request->cic_request_date;
            if ($request->cic_created_date) $lockProforma_invoice->cic_created_date = $request->cic_created_date;
            if ($request->cic_received_date) $lockProforma_invoice->cic_received_date = $request->cic_received_date;
            if ($request->inv_date) $lockProforma_invoice->inv_date = $request->inv_date;
            if ($request->inv_create_date) $lockProforma_invoice->inv_create_date = $request->inv_create_date;
            if ($request->cic_send_date) $lockProforma_invoice->cic_send_date = $request->cic_send_date;
            if ($request->cic_ready_to_pick_date) $lockProforma_invoice->cic_ready_to_pick_date = $request->cic_ready_to_pick_date;
            if ($request->cic_pick_up_date) $lockProforma_invoice->cic_pick_up_date = $request->cic_pick_up_date;
            if ($request->inv_send_date) $lockProforma_invoice->inv_send_date = $request->inv_send_date;
            if ($request->cic_number) $lockProforma_invoice->cic_number = $request->cic_number;
            if ($request->hasFile('cic_path')) {
                // Hapus file lama jika masih tersedia
                if (
                    !empty($lockProforma_invoice->cic_path) &&
                    Storage::exists($lockProforma_invoice->cic_path)
                ) {
                    Storage::delete($lockProforma_invoice->cic_path);
                }
                // Simpan file baru
                $file = $request->file('cic_path');
                $extension = $file->getClientOriginalExtension();
                $realname = $file->getClientOriginalName();
                $directory = 'cic_path';
                $filename = Str::random(24) . '.' . $extension;
                $file->storeAs($directory, $filename);
                $lockProforma_invoice->cic_path = $directory . '/' . $filename;
                $lockProforma_invoice->real_name = $realname;
            }
            if ($request->status == 'Done') {
                $lockProforma_invoice->status = $request->status;
            }
            $lockProforma_invoice->save();
            $invoice = Invoice::where('id', $lockProforma_invoice->invoice_id)->lockForUpdate()->first();
            if ($invoice) {
                if ($request->cut_off_date) $invoice->cut_off_date = $request->cut_off_date;
                if ($request->consolidation_date) $invoice->consolidation_date = $request->consolidation_date;
                if ($request->progress_claim_date) $invoice->progress_claim_date = $request->progress_claim_date;
                if ($request->ops_received_date) $invoice->ops_received_date = $request->ops_received_date;
                if ($request->prof_inv_app_date) $invoice->prof_inv_app_date = $request->prof_inv_app_date;
                if ($request->cic_request_date) $invoice->cic_request_date = $request->cic_request_date;
                if ($request->cic_created_date) $invoice->cic_created_date = $request->cic_created_date;
                if ($request->cic_received_date) $invoice->cic_received_date = $request->cic_received_date;
                if ($request->inv_date) $invoice->inv_date = $request->inv_date;
                if ($request->inv_create_date) $invoice->inv_create_date = $request->inv_create_date;
                if ($request->cic_send_date) $invoice->cic_send_date = $request->cic_send_date;
                if ($request->cic_ready_to_pick_date) $invoice->cic_ready_to_pick_date = $request->cic_ready_to_pick_date;
                if ($request->cic_pick_up_date) $invoice->cic_pick_up_date = $request->cic_pick_up_date;
                if ($request->inv_send_date) $invoice->inv_send_date = $request->inv_send_date;
                $invoice->save();
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
    public function destroy(string $id)
    {
        //
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
            $view = 'cic.detail';
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
     * generate invoice per proforma invoice
     * 1 proforma invoice : 1 invoice
     */
    public function create_invoice(Request $request, $proforma_invoice_id, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $excelRound = function ($value, int $precision = 2) {
                return round((float) $value, $precision, PHP_ROUND_HALF_UP);
            };
            $department = 'Finance';
            $proforma_invoice = Proforma_invoice::find($proforma_invoice_id);
            $proforma_invoice_detail = Proforma_invoice_detail::where('proforma_invoice_id', $proforma_invoice->id)->get();
            $total = $proforma_invoice->total;
            $dpp = $excelRound((11 / 12) * $total, 2);
            $ppn = $excelRound((12 / 100) * $dpp, 2);
            $grand_total = $total + $ppn;
            $invoice = Invoice::create([
                'request_token' => (string) Str::uuid(),
                'user_id' => Auth::user()->id,
                'client_vendor_id' => $proforma_invoice->client_vendor_id,
                'contract_id' => $proforma_invoice->contract_id,
                'contract_rate_id' => $proforma_invoice->contract_rate_id,
                'contract_fmf_id' => $proforma_invoice->contrcat_fmf_id,
                'unit_target_id' => $proforma_invoice->unit_target_id,
                'unit_id' => $proforma_invoice->unit_id,
                'proforma_invoice_id' => $proforma_invoice->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'periode' => $proforma_invoice->periode,
                'periode_start' => $proforma_invoice->periode_start,
                'periode_finish' => $proforma_invoice->periode_finish,
                'target' => $proforma_invoice->target,
                'price' => $proforma_invoice->price,
                'act_work_day' => $proforma_invoice->act_work_day,
                'act_work_hour' => $proforma_invoice->act_work_hour,
                'breakdown' => $proforma_invoice->breakdown,
                'pa' => $proforma_invoice->pa,
                'penalty' => $proforma_invoice->penalty,
                'tax' => $ppn,
                'dpp' => $dpp,
                'total' => $total,
                'grand_total' => $grand_total,
                'km_awal' => $proforma_invoice->km_awal,
                'km_akhir' => $proforma_invoice->km_akhir,
                'type' => $proforma_invoice->type,
                'input_method' => 'Web',
                'cut_off_date' => $proforma_invoice->cut_off_date,
                'consolidation_date' => $proforma_invoice->consolidation_date,
                'progress_claim_date' => $proforma_invoice->progress_claim_date,
                'ops_received_date' => $proforma_invoice->ops_received_date,
                'prof_inv_app_date' => $proforma_invoice->prof_inv_app_date,
                'cic_request_date' => $proforma_invoice->cic_request_date,
                'cic_created_date' => $proforma_invoice->cic_created_date,
                'cic_received_date' => $proforma_invoice->cic_received_date,
                'inv_date' => $proforma_invoice->inv_date,
                'cic_send_date' => $proforma_invoice->cic_send_date,
                'cic_ready_to_pick_date' => $proforma_invoice->cic_ready_to_pick_date,
                'cic_pick_up_date' => $proforma_invoice->cic_pick_up_date,
                'inv_send_date' => $proforma_invoice->inv_send_date,
                'status' => $request->status
            ]);
            $proforma_invoice->invoice_id = $invoice->id;
            $proforma_invoice->status = 'Invoicing';
            $proforma_invoice->save();
            foreach ($proforma_invoice_detail as $d) {
                Invoice_detail::create([
                    'request_token' => $invoice->request_token,
                    'invoice_id' => $invoice->id,
                    'contract_id' => $d->contract_id,
                    'contract_rate_id' => $d->contract_rate_id,
                    'contract_fmf_id' => $d->contract_fmf_id,
                    'unit_target_id' => $d->unit_target_id,
                    'service_item_id' => $d->service_item_id,
                    'unit_id' => $d->unit_id,
                    'item_no' => $d->item_no,
                    'service_item' => $d->service_item,
                    'unit' => $d->unit,
                    'rate' => $d->rate,
                    'type' => $d->type,
                    'year' => $d->year,
                    'value' => $d->value,
                    'target' => $d->target,
                    'price' => $d->price,
                    'qty' => $d->qty,
                    'amount' => $d->amount,
                    'ptd_qty' => $d->ptd_qty,
                    'ptd_amount' => $d->ptd_amount
                ]);
            }

            Invoice_proforma_invoice::create([
                'invoice_id' => $invoice->id,
                'proforma_invoice_id' => $proforma_invoice_id
            ]);

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
                    $invoice->status = 'Approved';
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

    public function generate_invoice(Request $request)
    {
        DB::beginTransaction();
        try {
            $year = $request->year;
            $month = $request->month;
            $contract = Contract::where('status', 'Active')->get();
            $html = view('cic.table-generate', compact(
                'contract',
                'year',
                'month'
            ))->render();
            return response()->json([
                'success' => true,
                'message' => 'Data showed',
                'html' => $html
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
     * export file
     */
    public function export_file(Request $request, Proforma_invoice $proforma_invoice)
    {
        try {
            $path = public_path('storage/' . $proforma_invoice->cic_path);
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            $mimeType = mime_content_type($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileName = basename($path);
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $proforma_invoice->cic_path . '"',
                ]);
            }
            return response()->download($path, $proforma_invoice->real_name, [
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
    public function destroy_file(Proforma_invoice $proforma_invoice)
    {
        DB::beginTransaction();
        try {
            $filePath = $proforma_invoice->cic_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $proforma_invoice->cic_path = null;
            $proforma_invoice->save();
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
     * Simpan generate invoice
     */
    public function store_generate_invoice(Request $request, ApprovalService $approval_service)
    {
        DB::beginTransaction();
        try {
            $department = "Finance";
            $excelRound = function ($value, int $precision = 2) {
                return round((float) $value, $precision, PHP_ROUND_HALF_UP);
            };
            $year = $request->year;
            $month = $request->month;
            $periode = Carbon::parse("$year-$month")->format('Y-m');
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $contract = Contract::where('status', 'Active')->get();
            foreach ($contract as $cont) {
                $proforma_invoice = Proforma_invoice::where('contract_id', $cont->id)
                    ->where('periode', $periode)
                    ->whereNull('invoice_id')
                    ->get();
                if ($proforma_invoice->count() > 0) {
                    $total = Proforma_invoice::where('contract_id', $cont->id)
                        ->where('periode', $periode)
                        ->whereNull('invoice_id')
                        ->sum('total');
                    $dpp = $excelRound((11 / 12) * $total, 2);
                    $ppn = $excelRound((12 / 100) * $dpp, 2);
                    $grand_total = $total + $ppn;
                    $invoice = Invoice::firstOrCreate([
                        'request_token' => (string) Str::uuid(),
                        'user_id' => Auth::user()->id,
                        'client_vendor_id' => $cont->client_vendor_id,
                        'contract_id' => $cont->id,
                        'date' => Carbon::now()->format('Y-m-d'),
                        'periode' => $periode,
                        'periode_start' => $startDate,
                        'periode_finish' => $endDate,
                        'dpp' => $dpp,
                        'ppn' => $ppn,
                        'tax' => $ppn,
                        'total' => $total,
                        'grand_total' => $grand_total,
                        'status' => $request->status
                    ]);
                    Proforma_invoice::where('contract_id', $cont->id)
                        ->where('periode', $periode)
                        ->whereNull('invoice_id')
                        ->update([
                            'invoice_id' => $invoice->id,
                            'status' => 'Invoicing'
                        ]);
                    foreach ($proforma_invoice as $pi) {
                        Invoice_proforma_invoice::create([
                            'request_token' => $invoice->request_token,
                            'invoice_id' => $invoice->id,
                            'proforma_invoice_id' => $pi->id
                        ]);
                    }
                    $this->check_approval($invoice, $request->status, $approval_service);
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

    public function check_approval(Invoice $invoice, string $status, ApprovalService $approval_service)
    {
        $model = 'App\Models\Invoice';
        $department = 'Finance';
        if ($approval_service->checkHasApproval($model, $department)) {
            if ($status == 'Open') {
                $invoice->status = 'Approval';
                $invoice->save();
                $approval_flow_id = $approval_service->getApprovalFlowId($model, $department);
                $approval_service->createApprovalProcess($approval_flow_id, $invoice->id);
            }
        } else {
            if ($status == 'Open') {
                $invoice->status = 'Approved';
                $invoice->save();
            }
        }
    }
}
