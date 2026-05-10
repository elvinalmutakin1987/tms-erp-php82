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
            $purchase_order_payment = Purchase_order_payment::query();
            if (request()->status != 'All') {
                $purchase_order_payment = $purchase_order_payment->where('status', request()->status);
            }
            if (request()->date_start != '') {
                $purchase_order_payment = $purchase_order_payment->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $purchase_order_payment = $purchase_order_payment->where('date', '<=', request()->date_end);
            }
            $purchase_order_payment = $purchase_order_payment->orderBy('date', 'desc')->get();
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
                                    <a class="dropdown-item printButton" href="' . route('purchaseorder.print', $item->id) . '" target="_blank">Print</a>
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
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaseorder.delete')):
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
                    return $item->client_vendor ? $item->client_vendor->name : '-';
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'PO Payment',
            'route-sub-module' => 'purchaseorderpayment.index',
        ];
        return view('purchase_order_payment.index', compact('breadcrum'));
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
                'client_vendor_id' => 'required',
                'vendor_offer_path' => 'file|mimes:pdf,doc,docx|max:2048',
            ]);
            $data = array_merge(
                $request->only([
                    'purchase_order_id',
                    'date',
                    'notes',
                    'total',
                    'bank',
                    'bank_account',
                    'type'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
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
                    $purchase_order_payment->status = 'Approved';
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
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_order_payment,
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
        //
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
        try {
            $purchase_order = Purchase_order::whereIn('status', ['Done'])->get();
            return response()->json([
                'success' => true,
                'data' => $purchase_order
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
