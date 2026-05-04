<?php

namespace App\Http\Controllers;

use App\Models\Client_vendor;
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


class RequestQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_requisition = Purchase_requisition::query();
            $purchase_requisition = $purchase_requisition->whereIn('status', ['Approved', 'Received'])->get();;
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
                                </li>
                                <li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Upload Quotation</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('quotation_file', function ($item) {
                    $request_quotation = Request_quotation::where('purchase_requisition_id', $item->id);
                    $html = '<table style="width: 100%">';
                    // $html .= '
                    // <tr>
                    //     <th><b>Vendor</b></th>
                    //     <th><b>File</b></th>
                    //     <th><b>Action</b></th>
                    // </tr>
                    // ';
                    if ($request_quotation->count() > 0) {
                        foreach ($request_quotation->get() as $key => $value) {
                            $html .= '<tr>';
                            $html .= '<td>';
                            $html .= $value->client_vendor->name;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<a href="' . Storage::url($value->quotation_path) . '" target="_blank">' . $value->real_name . '</a>';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= '<div class="d-flex gap-1 text-end">';
                            $html .= '<a class="btn btn-sm btn-success" href="#" onclick="delete_(\'' . $item->id . '\')"><i class="bx bx-plus me-0"></i> Create PO</a>';
                            $html .= '<a class="btn btn-sm btn-danger" href="#" onclick="delete_(\'' . $item->id . '\')"><i class="bx bx-trash me-0"></i></a>';
                            $html .= '</div>';
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
        $breadcrum = [
            'module' => 'Purchase Order',
            'route-module' => null,
            'sub-module' => 'Request Quoation',
            'route-sub-module' => 'requestquotation.index',
        ];
        return view('request_quotation.index', compact('breadcrum', 'uom', 'system_setting'));
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

    public function export_pdf(Request $request, Purchase_requisition $purchase_requisition)
    {
        $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_requisition')->first();
        $approval_step = $approval_flow  ? Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order', 'asc')->get() : null;
        $approval_process = $approval_flow  ? Approval_process::where('approval_flow_id', $approval_flow->id)->get() : null;
        $approval_status = $approval_flow  ? Approval_status::where('approval_flow_id', $approval_flow->id)->get() : null;
        $pdf = Pdf::loadView('purchase_requisition.print', [
            'purchase_requisition' => $purchase_requisition,
            'purchase_requisition_detail' => $purchase_requisition->purchase_requisition_detail,
            'approval_flow' => $approval_flow,
            'approval_step' => $approval_step,
            'approval_process' => $approval_process,
            'approval_status' => $approval_status
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            255, // X (geser kiri/kanan kalau perlu)
            58,  // Y (geser atas/bawah kalau perlu)
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $font,
            10,
            [0, 0, 0]
        );
        /**
         * Buat check statusnya, kalo draft, open, approval, cancel
         * nanti ada watermarknya
         */
        $status = ['Draft', 'Open', 'Approval', 'Cancel', 'Received', 'Done'];
        if (in_array($purchase_requisition->status, $status, true)) {
            $w = $canvas->get_width();
            $h = $canvas->get_height();
            $font = $fontMetrics->getFont('Helvetica', 'bold');
            $size = 48;
            $text = "Status : " . $purchase_requisition->status;
            $x = ($w / 2) - 100;
            $y = $h / 2 - 350;
            $text = $purchase_requisition->status;
            $canvas->text($x, $y, $text, $font, $size, [0.6, 0.6, 0.6]);
        }

        $safeFilename = Str::of($purchase_requisition->requisition_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }
}
