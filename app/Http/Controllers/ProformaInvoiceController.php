<?php

namespace App\Http\Controllers;

use App\Models\Contract;
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
                        $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                    endif;

                    /**
                     * status bukan done, bisa di hapus.
                     * user superadmin dan yang punya akses delete aja yang bisa muncul
                     */
                    if ($item->status != 'Done'):
                        $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>';
                    endif;

                    $button .= '</ul>
                        </div>
                    </div>
                    ';

                    return $button;
                })
                ->addColumn('unit', function ($item) {
                    return $item->unit?->vehicle_no ?? '';
                })
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
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
            ]);
            $data = [
                'client_vendor_id' => $request->client_vendor_id,
                'contract_id' => $request->contract_id,
                'unit_id' => $request->unit_id,
                'generate_no' => $request->generate_no,
                'proforma_no' => $request->proforma_no,
                'date' => $request->date,
                'periode' => $request->periode,
                'periode_start' => $request->periode_start,
                'periode_finish' => $request->periode_finish,
                'km_awal' => $request->km_awal,
                'km_akhir' => $request->km_akhir,
                'target' => $request->target,
                'pa' => $request->pa,
                'penalty' => $request->penalty,
                'breakdown' => $request->breakdown,
                'request_token' => $request->request_token,
                'input_method' => 'Web',
                'user_id' => Auth::user()->id
            ];
            $proforma_invoice = Proforma_invoice::firstOrCreate($data);



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
