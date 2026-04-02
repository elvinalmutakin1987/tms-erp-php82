<?php

namespace App\Http\Controllers;

use App\Models\Client_vendor;
use App\Models\Maintenance;
use App\Models\Maintenance_detail;
use App\Models\Maintenance_item;
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

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $maintenance = Maintenance::query();
            if (request()->unit_id != 'All') {
                $maintenance = $maintenance->where('unit_id', request()->unit_id);
            }
            if (request()->status != 'All') {
                $maintenance = $maintenance->where('status', request()->status);
            }
            // if (request()->client_vendor_id != 'All') {
            //     $maintenance = $maintenance->where('client_vendor_id', request()->client_vendor_id);
            // }
            if (request()->type != 'All') {
                $maintenance = $maintenance->where('type', request()->type);
            }
            if (request()->date_start != '') {
                $maintenance = $maintenance->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $maintenance = $maintenance->where('date', '<=', request()->date_end);
            }
            $maintenance = $maintenance->orderBy('maintenance_no', 'desc')->get();
            return DataTables::of($maintenance)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('maintenance.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('maintenance.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                               <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    if ($item->status == 'Open'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('maintenance.cost')):
                            $button .= '<li>
                                    <a class="dropdown-item costButton" href="#" data-bs-toggle="modal" data-bs-target="#formCost"
                                    data-id="' . $item->id . '">Cost Setting</a>
                                </li>';
                        endif;
                    endif;
                    if ($item->status == 'Draft'):
                        $button .= '<li>
                        <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                        data-id="' . $item->id . '">Edit</a>
                    </li>';
                    endif;
                    $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('unit', function ($item) {
                    $unit = Unit::find($item->unit_id);
                    return $unit->vehicle_no;
                })
                ->addColumn('main_type', function ($item) {
                    $type = "";
                    if ($item->type == "BO") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "B1") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "B2") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "B3") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "B4") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "M") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "N") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "A") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "OP") {
                        $type = "Breakdown under repair";
                    } elseif ($item->type == "STD") {
                        $type = "Stand By";
                    } elseif ($item->type == "B0") {
                        $type = "Breakdown";
                    } elseif ($item->type == "A/OP") {
                        $type = "Abnormal Operation";
                    }
                    return $type;
                })
                ->make();
        }
        $maintenance_item = Maintenance_item::all();
        $maintenance_type = config('maintenance-type');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Maintenance',
            'route-sub-module' => 'maintenance.index',
        ];
        return view('maintenance.index', compact('breadcrum', 'maintenance_item', 'maintenance_type'));
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
                'client_vendor_id' => ['required', 'not_in:All'],
                'hour_meter' => 'required',
                'km_hm' => 'required',
                'start' => 'required',
                'finish' => 'required',
                'work_duration' => 'required'
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'maintenance_item_id',
                    'mro_item_id',
                    'notes',
                    'action',
                    'act',
                    'main_item',
                    'main_item_id'
                ),
                [
                    'input_method' => 'Web'
                ]
            );
            $maintenance = Maintenance::create($data);
            foreach ($request->maintenance_item_id as $key => $item) {
                $maintenance->maintenance_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                    ],
                    [
                        'maintenance_item_id' => $request->maintenance_item_id[$key],
                        'action' => $request->action[$key],
                    ]
                );
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
    public function show(Maintenance $maintenance)
    {
        $maintenance_detail = $maintenance->maintenance_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $maintenance,
            'maintenance_detail' => $maintenance_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
                'client_vendor_id' => ['required', 'not_in:All'],
                'hour_meter' => 'required',
                'km_hm' => 'required',
                'start' => 'required',
                'finish' => 'required',
                'work_duration' => 'required'
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'maintenance_item_id',
                    'mro_item_id',
                    'notes',
                    'action',
                    'act',
                    'main_item',
                    'main_item_id'
                ),
                ['input_method' => 'Web']
            );
            $maintenance->lockForUpdate($data);
            foreach ($request->maintenance_item_id as $key => $item) {
                $maintenance->maintenance_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                    ],
                    [
                        'maintenance_item_id' => $request->maintenance_item_id[$key],
                        'action' => $request->action[$key]
                    ]
                );
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
    public function destroy(Maintenance $maintenance)
    {
        DB::beginTransaction();
        try {
            $maintenance->maintenance_detail()->delete();
            $maintenance->delete();
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
     * Ngambil data unit
     */
    public function get_unit_all(Request $request)
    {
        try {
            $unit = Unit::all();
            return response()->json([
                'success' => true,
                'data' => $unit
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
    public function get_vendor_all(Request $request)
    {
        try {
            $vendor = Client_vendor::where('type', 'Vendor')->get();
            return response()->json([
                'success' => true,
                'data' => $vendor
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
    public function get_maintenance_item(Request $request)
    {
        try {
            if ($request->ajax()) {
                $term = trim($request->term);
                $item = Maintenance_item::selectRaw("id, action, name as text")
                    ->where('name', 'like', '%' . $term . '%')
                    ->orderBy('name')->simplePaginate(10);
                $total_count = count($item);
                $morePages = true;
                $pagination_obj = json_encode($item);
                if (empty($item->nextPageUrl())) {
                    $morePages = false;
                }
                $result = [
                    "results" => $item->items(),
                    "pagination" => [
                        "more" => $morePages
                    ],
                    "total_count" => $total_count
                ];
                return response()->json($result);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list maintenance nya
     */
    public function get_table_add(Request $request, Maintenance $maintenance)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $view = 'maintenance.table-add';
            $maintenance_item = Maintenance_item::where('action', 'Repair')->get();
            $maintenance_prev_no = Generator::make()
                ->type('main')
                ->formatter($presenter)
                ->preview();
            $html = view($view, compact('maintenance_item'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'maintenance_prev_no' => $maintenance_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list maintenance nya
     */
    public function get_table_edit(Request $request, Maintenance $maintenance)
    {
        try {
            $view = 'maintenance.table-edit';
            $maintenance_detail = $maintenance->maintenance_detail;
            $maintenance_item = Maintenance_item::where('action', 'Repair')->get();
            $html = view($view, compact('maintenance', 'maintenance_detail', 'maintenance_item'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'maintenance_no' => $maintenance->maintenance_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data maintenance item berdasarkan actionnya
     */
    public function get_maintenance_item_by_action(Request $request)
    {
        try {
            if ($request->ajax()) {
                $term = trim($request->term);
                $item = Maintenance_item::selectRaw("id, action, name as text")
                    ->where('action', $request->action)
                    ->orderBy('name')->simplePaginate(10);
                $total_count = count($item);
                $morePages = true;
                $pagination_obj = json_encode($item);
                if (empty($item->nextPageUrl())) {
                    $morePages = false;
                }
                $result = [
                    "results" => $item->items(),
                    "pagination" => [
                        "more" => $morePages
                    ],
                    "total_count" => $total_count
                ];
                return response()->json($result);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data service item
     */
    public function get_maintenance_item_list(Request $request)
    {
        try {
            $maintenance = Maintenance::find($request->maintenance_id);
            $maintenance_detail = Maintenance_detail::where('maintenance_id', $request->maintenance_id)->get();
            $view = 'maintenance.table-edit';
            return response()->view($view, compact('maintenance', 'maintenance_detail'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Nampilin form cost setting
     */
    public function cost(Request $request, Maintenance $maintenance)
    {
        try {
            $maintenance_detail = $maintenance->maintenance_detail;
            $view = 'maintenance.cost';
            return response()->view($view, compact('maintenance', 'maintenance_detail'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Nyimpen data cost nya
     */
    public function cost_store(Request $request)
    {
        try {
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Nyimpen data cost nya
     */
    public function cost_update(Request $request, Maintenance $maintenance)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'cost_total' => 'required'
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'maintenance_item_id',
                    'cost'
                )
            );
            $maintenance->update($data);
            foreach ($request->maintenance_item_id as $key => $item) {
                $maintenance->maintenance_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                    ],
                    [
                        'cost' => $request->cost[$key]
                    ]
                );
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
     * Check unit sedang maintenance atau tidak
     */
    public function check_maintenance_unit(String $unit_id)
    {
        $check = Maintenance::where('unit_id', $unit_id)->where('status', 'Open')->first()->count();
        if ($check == 1) {
            return true;
        }
        return false;
    }

    /**
     * ngambil detail maintenance
     */
    public function get_detail(Request $request, Maintenance $maintenance)
    {
        try {
            $maintenance_item = Maintenance_item::all();
            $maintenance_detail = $maintenance->maintenance_detail;
            $view = 'maintenance.detail';
            return response()->view($view, compact('maintenance', 'maintenance_detail', 'maintenance_item'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngeprint
     */
    public function print(Request $request, Maintenance $maintenance)
    {
        $maintenance_item = Maintenance_item::all();

        $pdf = Pdf::loadView('maintenance.print', [
            'maintenance' => $maintenance,
            'maintenance_item' => $maintenance_item,
            'maintenance_detail' => $maintenance->maintenance_detail
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
        $safeFilename = Str::of($maintenance->maintenance_item)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Maintenance $maintenance)
    {
        $maintenance_item = Maintenance_item::all();

        $pdf = Pdf::loadView('maintenance.print', [
            'maintenance' => $maintenance,
            'maintenance_item' => $maintenance_item,
            'maintenance_detail' => $maintenance->maintenance_detail
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
        $safeFilename = Str::of($maintenance->maintenance_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }
}
