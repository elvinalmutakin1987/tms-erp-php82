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
use App\Models\Proforma_invoice;
use App\Models\Purchase_order;
use App\Models\Purchase_order_payment;
use App\Models\Purchase_requisition;
use App\Models\Request_quotation;
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
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '" data-model="' . str_replace('App\\Models\\', '', $item->approval_flow->approvable_model) . '">Detail</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="approve_(\'' . $item->approval_flow->approvable_model . '\', \'' . $item->id . '\')">Approved</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="reject_(\'' . $item->approval_flow->approvable_model . '\', \'' . $item->id . '\')">Reject</a>
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
                $data = Purchase_requisition::find($id);
                $detail = $data->purchase_requisition_detail;
                if ($data->type == 'General') {
                    $view = 'approval.detail-pr-general';
                } else {
                    $view = 'approval.detail-pr';
                }
                $compact = compact('data', 'detail');
            } else if ($approvable_model == 'Purchase_order') {
                $purchase_order = Purchase_order::find($id);
                $purchase_order_detail = $purchase_order->purchase_order_detail;
                $view = 'approval.detail-po';
                $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token)->get();
                $compact = compact('purchase_order', 'purchase_order_detail', 'request_quotation');
            }
            return response()->view($view, $compact, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
