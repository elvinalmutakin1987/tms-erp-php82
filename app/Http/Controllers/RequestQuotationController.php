<?php

namespace App\Http\Controllers;

use App\Models\Client_vendor;
use App\Models\Purchase_order;
use App\Models\Purchase_requisition;
use App\Models\Request_quotation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_requisition = Purchase_requisition::query();
            if (request()->department != 'All') {
                $purchase_requisition = $purchase_requisition->where('department', request()->department);
            }
            if (request()->status != 'All') {
                if (request()->status == 'Open') {
                    $purchase_requisition = $purchase_requisition
                        ->whereIn('status', ['Approved', 'Received'])
                        ->whereDoesntHave('purchase_order');
                } else {
                    $purchase_requisition = $purchase_requisition
                        ->whereIn('status', ['Approved', 'Received'])
                        ->whereHas('purchase_order', function ($query) {
                            $query->whereNotNull('purchase_requisition_id');
                        });
                }
            }
            $purchase_requisition = $purchase_requisition->orderBy('created_at', 'desc')->get();
            return DataTables::of($purchase_requisition)
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
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    $purchase_order = Purchase_order::where('purchase_requisition_id', $item->id)->get();
                    if ($purchase_order->count() == 0) {
                        $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Upload Quotation</a>
                                </li>';
                    }
                    $button .= '</ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('quotation_file', function ($item) {
                    $request_quotation = Request_quotation::where('purchase_requisition_id', $item->id);
                    $html = '<table style="width: 100%">';
                    if ($request_quotation->count() > 0) {
                        $purchase_order = Purchase_order::where('purchase_requisition_id', $item->id)->get();
                        foreach ($request_quotation->get() as $key => $value) {
                            $html .= '<tr>';
                            $html .= '<td style="width: 45%">';
                            $check_po = Purchase_order::where('purchase_requisition_id', $item->id)
                                ->where('client_vendor_id', $value->client_vendor_id)
                                ->first();

                            $html .= '<div class="d-flex align-items-center gap-2">';
                            $html .= '<span>' . $value->client_vendor->name . '</span>';
                            if ($check_po) {
                                $html .= '
                                    <div class="d-flex align-items-center text-success">
                                        <i class="bx bx-radio-circle-marked bx-burst bx-rotate-90 align-middle font-18 me-1"></i>
                                        <span>Selected</span>
                                    </div>
                                ';
                            }
                            $html .= '</div>';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<a href="' . route('requestquotation.export_pdf', $value->id) . '" target="_blank">' . $value->real_name . '</a>';
                            $html .= '</td>';
                            $html .= '<td style="width: 15%">';
                            if ($purchase_order->count() == 0) {
                                $html .= '<div class="d-flex gap-1 text-end">';
                                $html .= '<a class="btn btn-sm btn-success" href="#" onclick="create_(\'' . $value->id . '\')"><i class="bx bx-plus me-0"></i> Create PO</a>';
                                $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_(\'' . $value->id . '\')"><i class="bx bx-trash me-0"></i></a>';
                                $html .= '</div>';
                            }
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                    } else {
                        $html .= '<tr><td class="text-center">No quotation file</td></tr>';
                    }
                    $html .= '</table>';
                    return $html;
                })
                ->rawColumns(['action', 'quotation_file'])
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $department = config('department');
        $breadcrum = [
            'module' => 'Purchase Order',
            'route-module' => null,
            'sub-module' => 'Request Quotation',
            'route-sub-module' => 'requestquotation.index',
        ];
        return view('request_quotation.index', compact('breadcrum', 'uom', 'system_setting', 'department'));
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
    public function show(Request_quotation $request_quotation)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $request_quotation,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_quotation $request_quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Request_quotation $request_quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_quotation $request_quotation)
    {
        DB::beginTransaction();
        try {
            $filePath = $request_quotation->quotation_path;
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $request_quotation->delete();
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
     * Display the specified resource.
     */
    public function get_purchase_requisition(Purchase_requisition $purchase_requisition)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $purchase_requisition,
        ], 200);
    }

    /**
     * ngambil detail purchase requisition
     */
    public function get_detail(Request $request, $pr_id)
    {
        try {
            $purchase_requisition = Purchase_requisition::find($pr_id);
            $purchase_requisition_detail = $purchase_requisition->purchase_requisition_detail;
            $view = 'request_quotation.detail';
            if ($purchase_requisition->type == 'General') {
                $view = 'request_quotation.detail-gen';
            }
            return response()->view($view, compact('purchase_requisition', 'purchase_requisition_detail'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data maintenance item
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
     * Store a newly created resource in storage.
     */
    public function quotation(Request $request, Purchase_requisition $purchase_requisition)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'client_vendor_id' => 'required',
                'quotation_path' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);
            if ($request->quotation_path) {
                $file = $request->file('quotation_path');
                $realname = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $directory = "quotation_path";
                $filename = Str::random(24) . "." . $extension;
                $file->storeAs($directory, $filename);
                Request_quotation::firstOrCreate([
                    'purchase_requisition_id' => $purchase_requisition->id,
                    'client_vendor_id' => $request->client_vendor_id,
                    'request_token' => $request->request_token,
                    'user_id' => Auth::user()->id,
                    'real_name' => $realname,
                    'quotation_path' => $directory . '/' . $filename
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
     * export pdf
     */

    public function export_pdf(Request $request, Request_quotation $request_quotation)
    {
        try {
            $path = public_path('storage/' . $request_quotation->quotation_path);
            if (!file_exists($path)) {
                abort(404, 'File not found.');
            }
            $mimeType = mime_content_type($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileName = basename($path);
            if ($mimeType === 'application/pdf' || $extension === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $request_quotation->quotation_path . '"',
                ]);
            }
            return response()->download($path, $request_quotation->real_name, [
                'Content-Type' => $mimeType ?: 'application/octet-stream',
            ]);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('requestquotation.index')
                ->with('error', 'Failed to open file.');
        }
    }

    /**
     * Buat PO dari Request Quotation
     */

    public function create_purchase_order(Request $request, Request_quotation $request_quotation)
    {
        DB::beginTransaction();
        try {
            $purchase_requisition_id = $request_quotation->purchase_requisition_id;
            $client_vendor_id = $request_quotation->client_vendor_id;
            $request_token = (string) Str::uuid();

            $purchase_requisition = Purchase_requisition::with('purchase_requisition_detail')
                ->findOrFail($purchase_requisition_id);

            $purchase_order = Purchase_order::create([
                'purchase_requisition_id' => $purchase_requisition_id,
                'client_vendor_id' => $client_vendor_id,
                'request_token' => $request_token,
                'user_id' => Auth::id(),
                'date'  => Carbon::now(),
                'status' => 'Approved',
                'total' => $purchase_requisition->total,
                'discount' => $purchase_requisition->discount,
                'tax' => $purchase_requisition->tax,
                'grand_total' => $purchase_requisition->grand_total,
                'department' => $purchase_requisition->department,
                'urgency' => $purchase_requisition->urgency,
                'notes' => $purchase_requisition->notes,
            ]);

            $purchase_order_details = $purchase_requisition->purchase_requisition_detail
                ->map(function ($purchase_requisition_detail) use ($request_token) {
                    return [
                        'request_token' => $request_token,
                        'maintenance_item_id' => $purchase_requisition_detail->maintenance_item_id,
                        'mro_item_id' => $purchase_requisition_detail->mro_item_id,
                        'description' => $purchase_requisition_detail->description,
                        'type' => $purchase_requisition_detail->type,
                        'uom' => $purchase_requisition_detail->uom,
                        'qty' => $purchase_requisition_detail->qty,
                        'price' => $purchase_requisition_detail->price,
                        'discount_item' => $purchase_requisition_detail->discount_item,
                        'tax' => $purchase_requisition_detail->tax,
                        'amount' => $purchase_requisition_detail->amount,
                        'part_number' => $purchase_requisition_detail->part_number,
                        'desc_vendor' => $purchase_requisition_detail->desc_vendor,
                    ];
                })
                ->toArray();
            $purchase_order->purchase_order_detail()->createMany($purchase_order_details);

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
