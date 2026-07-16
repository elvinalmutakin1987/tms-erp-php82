<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Proforma_invoice;
use App\Models\Purchase_order;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_requisition;
use App\Models\Request_quotation;
use App\Models\Unit_target;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $approval_process = Approval_process::where('user_id', Auth::user()->id)
                ->where('action', 'Open')
                ->orderBy('id', 'desc')
                ->get();
            if ($approval_process->isEmpty()) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->addColumn('action', function () {
                        return '';
                    })
                    ->make(true);
            }
            return DataTables::of($approval_process)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a
                                        class="dropdown-item detailButton"
                                        href="javascript:void(0)"
                                        data-bs-toggle="modal"
                                        data-bs-target="#formDetail"
                                        data-id="' . $item->approvable_id . '"
                                        data-model="' . str_replace(
                        'App\\Models\\',
                        '',
                        $item->approval_flow->approvable_model
                    ) . '"
                                        data-procid="' . $item->id . '"
                                    >
                                        Detail
                                    </a>
                                </li>

                                <li>
                                    <a
                                        class="dropdown-item approveButton"
                                        href="javascript:void(0)"
                                        data-id="' . $item->id . '"
                                    >
                                        Approve
                                    </a>
                                </li>

                                <li>
                                    <a
                                        class="dropdown-item rejectButton"
                                        href="javascript:void(0)"
                                        data-id="' . $item->id . '"
                                    >
                                        Reject
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('number', function ($item) {
                    if ($item->approval_flow->approvable_model == 'App\Models\Purchase_requisition') {
                        $purchase_requisition = Purchase_requisition::find($item->approvable_id);
                        return $purchase_requisition->requisition_no ?? '';
                    } else if ($item->approval_flow->approvable_model == 'App\Models\Purchase_order') {
                        $purchase_order = Purchase_order::find($item->approvable_id);
                        return $purchase_order->order_no ?? '';
                    } else if ($item->approval_flow->approvable_model == 'App\Models\Purchase_order_payment') {
                        $purchase_order_payment = Purchase_order_payment::find($item->approvable_id);
                        return $purchase_order_payment->payment_no ?? '';
                    } else if ($item->approval_flow->approvable_model == 'App\Models\Proforma_invoice') {
                        $proforma_invoice = Proforma_invoice::find($item->approvable_id);
                        return $proforma_invoice->proforma_no ?? '';
                    } else if ($item->approval_flow->approvable_model == 'App\Models\Invoice') {
                        // $invoice = Invoice::find($item->approvable_id);
                        // return $invoice->invoice_no ?? '';
                        return true;
                    } else {
                        // $invoice_payment = Invoice_payment::find($item->approvable_id);
                        // return $invoice_payment->payment_no ?? '';
                        return true;
                    }
                })
                ->addColumn('type', function ($item) {
                    $models = [
                        'App\Models\Purchase_requisition' => 'Purchase Requisition',
                        'App\Models\Purchase_order' => 'Purchase Order',
                        'App\Models\Purchase_order_payment' => 'Purchase Order Payment',
                        'App\Models\Proforma_invoice' => 'Proforma Invoice',
                        'App\Models\Invoice' => 'Invoice',
                        'App\Models\Invoice_payment' => 'Invoice Payment',
                    ];
                    return $models[$item->approval_flow->approvable_model] ?? 'Not Found';
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Approval',
            'route-module' => null,
            'sub-module' => '',
            'route-sub-module' => 'approval.index',
        ];
        return view('approval.index', compact('breadcrum'));
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
    public function show(string $approvable_model, string $id)
    {
        //
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
        //
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
    public function get_detail(string $approvable_model, string $id)
    {
        try {
            $view = '';
            $data = null;
            $detail = null;
            $compact = [];
            if ($approvable_model == 'Purchase_requisition') {
                $purchase_requisition = Purchase_requisition::find($id);
                $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
                $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_requisition')
                    ->where('department', $purchase_requisition->department)
                    ->first();
                $approval_process = Approval_process::where('approval_flow_id', $approval_flow->id)
                    ->where('approvable_id', $purchase_requisition->id)
                    ->get();
                if ($purchase_requisition->type == 'General') {
                    $view = 'approval.detail-pr-general';
                } else {
                    $view = 'approval.detail-pr';
                }
                $compact = compact(
                    'purchase_requisition',
                    'purchase_requisition_detail',
                    'approval_process',
                    'approvable_model'
                );
            } else if ($approvable_model == 'Purchase_order') {
                $purchase_order = Purchase_order::find($id);
                $purchase_order_detail = $purchase_order->purchase_order_detail;
                $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')
                    ->where('department', $purchase_order->department)
                    ->first();
                $approval_process = Approval_process::where('approval_flow_id', $approval_flow->id)
                    ->where('approvable_id', $purchase_order->id)
                    ->get();
                $view = 'approval.detail-po';
                $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token)->get();
                $compact = compact('purchase_order', 'purchase_order_detail', 'request_quotation', 'approvable_model', 'approval_process');
            } else if ($approvable_model == 'Proforma_invoice') {
                $proforma_invoice = Proforma_invoice::find($id);
                $contract = Contract::find($proforma_invoice->contract_id);
                $contract_rate = Contract_rate::where('contract_id', $contract->id)->first();
                $contract_fmf = Contract_fmf::where('contract_id', $contract->id)->first();
                $unit_target = Unit_target::where('contract_id', $contract->id)->where('unit_id', $proforma_invoice->unit_id)->first();
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
                $view = 'approval.detail-pi';
                $compact = compact(
                    'proforma_invoice',
                    'contract',
                    'contract_rate',
                    'contract_fmf',
                    'unit_target',
                    'approval_process',
                    'year',
                    'month'
                );
            }
            return response()->view($view, $compact, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function approve(Approval_process $approval_process)
    {
        DB::beginTransaction();
        try {
            approve($approval_process);
            nextStep($approval_process);
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data approved!'
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

    public function reject(Approval_process $approval_process)
    {
        DB::beginTransaction();
        try {
            rejected($approval_process);
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
                'message' => 'Data rejected!'
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
