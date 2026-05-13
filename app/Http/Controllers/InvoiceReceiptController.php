<?php

namespace App\Http\Controllers;

use App\Models\Purchase_order;
use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Client_vendor;
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

class InvoiceReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_order = Purchase_order::where('payment_status', 'Waiting Invoice')
                ->orderBy('id', 'desc')
                ->get();
            return DataTables::of($purchase_order)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                         <a type="button" href="" class="btn btn-sm btn-primary editButton" 
                             href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Receive Invoice</a>
                    </div>
                    ';
                    return $button;
                })->addColumn('vendor', function ($item) {
                    return $item->client_vendor?->name ?? '';
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'Invoice Receipt',
            'route-sub-module' => 'invoicereceipt.index',
        ];
        return view('invoice_receipt.index', compact('breadcrum'));
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
    public function show(Purchase_order $purchase_order)
    {
        $vendor = Client_vendor::find($purchase_order->client_vendor_id);
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_order,
            'vendor' => $vendor,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase_order $purchase_order)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'inovice_no' => 'required',
                'invoice_date' => 'required',
            ]);
            $type = $purchase_requisition?->type ?? 'General';
            $department = $purchase_requisition?->department ?? 'Equipment';
            $system_setting = config('system_setting');
            $data = array_merge(
                $request->only([
                    'purchase_requisition_id',
                    'date',
                    'notes',
                    'total',
                    'tax',
                    'grand_total',
                    'status',
                    'urgency',
                    'client_vendor_id',
                    'discount'
                ]),
                [
                    'request_token' => $request->request_token,
                    'input_method' => 'Web',
                    'user_id' => Auth::user()->id,
                    'type' => $type,
                    'department' => $department
                ]
            );
            $lockPurchase_order = Purchase_order::where('id', $purchase_order->id)->lockForUpdate()->first();

            if ($request->has('vendor_offer_path')) {
                $request_quotation = Request_quotation::where('request_token', $purchase_order->request_token)->first();
                if ($request_quotation) {
                    $filePath = $request_quotation->quotation_path;
                    if ($filePath && Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                    $request_quotation->delete();
                }

                $file = $request->file('vendor_offer_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "quotation_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                Request_quotation::firstOrCreate([
                    'purchase_requisition_id' => $request->purchase_requisition_id ?? null,
                    'client_vendor_id' => $request->client_vendor_id,
                    'request_token' => $purchase_order->request_token,
                    'user_id' => Auth::user()->id,
                    'real_name' => $realname,
                    'quotation_path' => $directory . '/' . $filename,
                    'notes' => $request->notes
                ]);
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
    public function destroy(Purchase_order $purchase_order)
    {
        //
    }
}
