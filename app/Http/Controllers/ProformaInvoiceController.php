<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Daily_report;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
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
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Spatie\Permission\Models\Permission;

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
            // return DataTables::of($proforma_invoice)
            //     ->addIndexColumn()
            //     ->addColumn('action', function ($item) {
            //         $button = '
            //         <div class="col">
            //             <div class="dropdown">
            //                 <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
            //                     aria-expanded="false">Action</button>
            //                 <ul class="dropdown-menu">
            //                     <li>
            //                         <a class="dropdown-item exportPdfButton" href="' . route('proformainvoice.export_pdf', $item->id) . '">Export PDF</a>
            //                     </li>
            //                     <li>
            //                         <a class="dropdown-item printButton" href="' . route('proformainvoice.print', $item->id) . '" target="_blank">Print</a>
            //                     </li>
            //                     <li>
            //                         <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
            //                         data-id="' . $item->id . '">Detail</a>
            //                     </li>';
            //         /**
            //          * status draft
            //          * user superadmin dan yang punya akses edit aja yang bisa muncul
            //          */
            //         if ($item->status == 'Draft'):
            //             $button .= '<li>
            //                         <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
            //                         data-id="' . $item->id . '">Edit</a>
            //                     </li>';
            //         endif;

            //         /**
            //          * status bukan done, bisa di hapus.
            //          * user superadmin dan yang punya akses delete aja yang bisa muncul
            //          */
            //         if ($item->status != 'Done'):
            //             $button .= '<li>
            //                         <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
            //                     </li>';
            //         endif;

            //         $button .= '</ul>
            //             </div>
            //         </div>
            //         ';

            //         return $button;
            //     })
            //     ->addColumn('unit', function ($item) {
            //         return $item->unit?->vehicle_no ?? '';
            //     })
            //     ->make();
            $user = Auth::user();
            $permissionNames = [
                'proforma_invoice.edit',
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
                                <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal" data-id="' . $item->id . '">
                                    Edit
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
                // foreach ($gen_proforma['unit_target'] as $unittarget) {
                //     $hariKerja = $start_date->daysInMonth;
                //     $total_breakdown = Maintenance::whereYear('date', $year)
                //         ->whereMonth('date', $month)
                //         ->where('unit_id', $unittarget->unit_id)
                //         ->where('status', '!=', 'Draft')
                //         ->selectRaw('COALESCE(ROUND(SUM(TIME_TO_SEC(work_duration)) / 3600, 2), 0) as total_duration_decimal')
                //         ->value('total_duration_decimal');
                //     $km_awal = Daily_report::whereBetween('date', [$start_date, $end_date])
                //         ->orderBy('date', 'asc')
                //         ->orderBy('id', 'asc')
                //         ->value('km_start');
                //     $km_akhir = Daily_report::whereBetween('date', [$start_date, $end_date])
                //         ->orderBy('date', 'desc')
                //         ->orderBy('id', 'desc')
                //         ->value('km_finish');
                //     $total_breakdown = (float) $total_breakdown;
                //     $price = (float) ($unittarget->price ?? 0);
                //     $target = (float) ($unittarget->target ?? 0);
                //     $totalJamKerja = $hariKerja * 24;
                //     $pa = $totalJamKerja > 0 ? 100 - ($total_breakdown / $totalJamKerja) * 100 : 0;
                //     $pa = max(0, min(100, $pa));
                //     if ($target > 0) {
                //         $total_payment = $pa >= $target ? $price : ($pa / $target) * $price;
                //     } else {
                //         $total_payment = 0;
                //     }
                //     $total_payment = min($price, $total_payment);
                //     $penalty = $price - $total_payment;
                //     $data = [
                //         'contract_id' => $request->contract_id,
                //         'client_vendor_id' => $contract->client_vendor_id,
                //         'request_token' => $request->request_token,
                //         'input_method' => 'Web',
                //         'user_id' => Auth::user()->id,
                //         'unit_id' => $unittarget->unit_id,
                //         'periode_start' => $start_date,
                //         'periode_finish' => $end_date,
                //         'periode' => Carbon::parse($year . '-' . $month)->format('Y-m'),
                //         'target' => $target,
                //         'price' => $price,
                //         'pa' => $pa,
                //         'penalty' => $penalty,
                //         'total' => $total_payment,
                //         'km_awal' => $km_awal,
                //         'km_akhir' => $km_akhir,
                //         'type' => $contract->service->type
                //     ];
                //     $proforma_invoice = Proforma_invoice::firstOrCreate($data);
                // }
                foreach ($gen_proforma['unit_target'] as $unittarget) {
                    $unitId = $unittarget->unit_id;
                    $price  = (float) ($unittarget->price ?? 0);
                    $target = (float) ($unittarget->target ?? 0);
                    $totalJamKerja = $start_date->daysInMonth * 24;
                    $total_breakdown = (float) Maintenance::whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->where('unit_id', $unitId)
                        ->where('status', '!=', 'Draft')
                        ->selectRaw('COALESCE(ROUND(SUM(TIME_TO_SEC(work_duration)) / 3600, 2), 0) as total')
                        ->value('total');
                    $dailyReport = Daily_report::whereBetween('date', [$start_date, $end_date])
                        ->where('unit_id', $unitId);
                    $km_awal = (clone $dailyReport)
                        ->orderBy('date')
                        ->orderBy('id')
                        ->value('km_start');
                    $km_akhir = (clone $dailyReport)
                        ->orderByDesc('date')
                        ->orderByDesc('id')
                        ->value('km_finish');
                    $pa = $totalJamKerja > 0
                        ? 100 - (round($total_breakdown / $totalJamKerja, 2) * 100)
                        : 0;
                    $pa = max(0, min(100, $pa));
                    $total_payment = $target > 0
                        ? min($price, $pa >= $target ? $price : round($pa / $target, 2) * $price)
                        : 0;
                    $penalty = $price - $total_payment;
                    $proforma_invoice = Proforma_invoice::firstOrCreate([
                        'contract_id'       => $request->contract_id,
                        'client_vendor_id'  => $contract->client_vendor_id,
                        'request_token'     => $request->request_token,
                        // 'input_method'      => 'Web',
                        'user_id'           => Auth::id(),
                        'unit_id'           => $unitId,
                        'periode_start'     => $start_date,
                        'periode_finish'    => $end_date,
                        'periode'           => Carbon::parse("$year-$month")->format('Y-m'),
                        'target'            => $target,
                        'price'             => $price,
                        'pa'                => $pa,
                        'penalty'           => $penalty,
                        'total'             => $total_payment,
                        'km_awal'           => $km_awal,
                        'km_akhir'          => $km_akhir,
                        'type'              => $contract->service->type,
                        'status'            => $request->status
                    ]);
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
            }

            /**
             * Buat check ada approvalnya gak
             * Kalo ada statusnya jadi Approval.
             * Nanti kalo approval beres baru jadi Open
             */
            $model = 'App\Models\Proforma_invoice';
            $department = 'Equipment';
            if (checkHasApproval($model, $department)) {
                if ($proforma_invoice->status == 'Open') {
                    $proforma_invoice->status = 'Approval';
                    $proforma_invoice->save();
                    $approval_flow_id = getApprovalFlowId($model, $department);
                    createApprovalProcess($approval_flow_id, $proforma_invoice->id);
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
    public function destroy(Proforma_invoice $proforma_invoice) {}

    /**
     * Hitung summary breakdown
     */
    public function summary_breakdown(Request $request) {}

    /**
     * Hitung summary transport
     */
    public function summary_transport() {}

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
}
