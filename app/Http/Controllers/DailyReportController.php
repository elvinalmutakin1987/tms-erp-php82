<?php

namespace App\Http\Controllers;

use App\Models\Daily_report;
use App\Models\Location;
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
use Spatie\Permission\Models\Permission;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $daily_report = Daily_report::query();
            if (request()->unit_id != 'All' && request()->unit_id != '') {
                $daily_report = $daily_report->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $daily_report = $daily_report->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $daily_report = $daily_report->where('date', '<=', request()->date_end);
            }
            $daily_report = $daily_report->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            $user = Auth::user();
            $permissionNames = [
                'dailyreport.edit',
                'dailyreport.delete',
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

            return DataTables::of($daily_report)
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
                                        <a class="dropdown-item exportPdfButton" href="' . route('dailyreport.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('dailyreport.print', $item->id) . '" target="_blank">
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
                     * - hanya muncul untuk superadmin
                     * - atau user dengan permission dailyreport.edit
                     */
                    if ($canAccess('dailyreport.edit') || Auth::user()->hasRole('superadmin')) {
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
                     * - hanya muncul untuk superadmin
                     * - atau user dengan permission dailyreport.delete
                     */
                    if ($canAccess('dailyreport.delete') || Auth::user()->hasRole('superadmin')) {
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
                ->addColumn('total_km_duration', function ($item) {
                    if ($item->type != 'LCT') {
                        return $item->km_total;
                    }
                    if (is_null($item->duration_trip_1) || is_null($item->duration_trip_2)) {
                        return null;
                    }
                    $totalDuration = addTime($item->duration_trip_1, $item->duration_trip_2);
                    return $totalDuration;
                })
                ->rawColumns(['action'])
                ->make();
        }
        $service_type = config('servicetype');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Daily Report',
            'route-sub-module' => 'dailyreport.index',
        ];
        return view('daily_report.index', compact('breadcrum', 'service_type'));
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
            $unit = Unit::find($request->unit_id);
            $type = 'Non LCT';
            $duration_trip_1 = '00:00';
            $duration_trip_2 = '00:00';
            if ($unit && $unit->type == 'LCT') {
                $type = 'LCT';
            }
            if ($type == 'LCT') {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                ]);
                $berthing_trip_1 = $request->trip_1_berthing_at;
                $depart_trip_1 = $request->trip_1_departed_at;

                $duration_trip_1 = $request->filled('trip_1_berthing_at') && $request->filled('trip_1_departed_at')
                    ? countTime($depart_trip_1, $berthing_trip_1)
                    : null;

                $berthing_trip_2 = $request->trip_2_berthing_at;
                $depart_trip_2 = $request->trip_2_departed_at;

                $duration_trip_2 = $request->filled('trip_2_berthing_at') && $request->filled('trip_2_departed_at')
                    ? countTime($depart_trip_2, $berthing_trip_2)
                    : null;
            } else {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                    'km_start' => 'required',
                    'km_finish' => 'required',
                    'km_total' => 'required',
                ]);
            }
            $data = [
                'unit_id' => $request->unit_id,
                'date' => $request->date,
                'shift' => $request->shift,
                'km_start' => $request->km_start,
                'km_finish' => $request->km_finish,
                'km_total' => $request->km_total,
                'trip_1_location_id' => $request->trip_1_location_id,
                'trip_1_arr_location_id' => $request->trip_1_arr_location_id,
                'trip_1_loading_at' => $request->trip_1_loading_at,
                'trip_1_complete_loading_at' => $request->trip_1_complete_loading_at,
                'trip_1_departed_at' => $request->trip_1_departed_at,
                'trip_1_arrived_at' => $request->trip_1_arrived_at,
                'trip_1_berthing_at' => $request->trip_1_berthing_at,
                'trip_2_location_id' => $request->trip_2_location_id,
                'trip_2_arr_location_id' => $request->trip_2_arr_location_id,
                'trip_2_loading_at' => $request->trip_2_loading_at,
                'trip_2_complete_loading_at' => $request->trip_2_complete_loading_at,
                'trip_2_departed_at' => $request->trip_2_departed_at,
                'trip_2_arrived_at' => $request->trip_2_arrived_at,
                'trip_2_berthing_at' => $request->trip_2_berthing_at,
                'operator' => $request->operator,
                'helper' => $request->helper,
                'load' => $request->load,
                'refule_km' => $request->refule_km,
                'refule_liter' => $request->refule_liter,
                'refule_mechine' => $request->refule_mechine,
                'refule_genset' => $request->refule_genset,
                'refule_type' => $request->refule_type,
                'request_token' => $request->request_token,
                'input_method' => 'Web',
                'type' => $type,
                'service_type' => $request->service_type,
                'duration_trip_1' => $duration_trip_1,
                'duration_trip_2' => $duration_trip_2,
                'remarks' => $request->remarks,
            ];
            $daily_report = Daily_report::firstOrCreate($data);
            if ($request->filled('detail_unit_id')) {
                $details = [];
                foreach ($request->input('detail_unit_id', []) as $key => $unitId) {
                    if (blank($unitId)) {
                        continue;
                    }
                    $details[] = [
                        'request_token' => $daily_report->request_token,
                        'unit_id'       => $unitId,
                        'item'          => $request->input("item.$key"),
                        'uom_1'         => $request->input("uom_1.$key"),
                        'value_1'       => (float) $request->input("value_1.$key", 0),
                        'uom_2'         => $request->input("uom_2.$key"),
                        'value_2'       => (float) $request->input("value_2.$key", 0),
                    ];
                }
                if ($details !== []) {
                    $daily_report
                        ->daily_report_detail()
                        ->createMany($details);
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
    public function show(Daily_report $daily_report)
    {
        $daily_report_detail = $daily_report->daily_report_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $daily_report,
            'daily_report_detail' => $daily_report_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Daily_report $daily_report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Daily_report $daily_report)
    {
        DB::beginTransaction();
        try {
            $unit = Unit::find($request->unit_id);
            $type = 'Non LCT';
            $duration_trip_1 = '00:00';
            $duration_trip_2 = '00:00';
            if ($unit && $unit->type == 'LCT') {
                $type = 'LCT';
            }
            if ($type == 'LCT') {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                ]);
                $berthing_trip_1 = $request->trip_1_berthing_at;
                $depart_trip_1 = $request->trip_1_departed_at;

                $duration_trip_1 = $request->filled('trip_1_berthing_at') && $request->filled('trip_1_departed_at')
                    ? countTime($depart_trip_1, $berthing_trip_1)
                    : null;

                $berthing_trip_2 = $request->trip_2_berthing_at;
                $depart_trip_2 = $request->trip_2_departed_at;

                $duration_trip_2 = $request->filled('trip_2_berthing_at') && $request->filled('trip_2_departed_at')
                    ? countTime($depart_trip_2, $berthing_trip_2)
                    : null;
            } else {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                    'km_start' => 'required',
                    'km_finish' => 'required',
                    'km_total' => 'required',
                ]);
            }
            $data = [
                'unit_id' => $request->unit_id,
                'date' => $request->date,
                'shift' => $request->shift,
                'km_start' => $request->km_start,
                'km_finish' => $request->km_finish,
                'km_total' => $request->km_total,
                'trip_1_location_id' => $request->trip_1_location_id,
                'trip_1_arr_location_id' => $request->trip_1_arr_location_id,
                'trip_1_loading_at' => $request->trip_1_loading_at,
                'trip_1_complete_loading_at' => $request->trip_1_complete_loading_at,
                'trip_1_departed_at' => $request->trip_1_departed_at,
                'trip_1_arrived_at' => $request->trip_1_arrived_at,
                'trip_1_berthing_at' => $request->trip_1_berthing_at,
                'trip_2_location_id' => $request->trip_2_location_id,
                'trip_2_arr_location_id' => $request->trip_2_arr_location_id,
                'trip_2_loading_at' => $request->trip_2_loading_at,
                'trip_2_complete_loading_at' => $request->trip_2_complete_loading_at,
                'trip_2_departed_at' => $request->trip_2_departed_at,
                'trip_2_arrived_at' => $request->trip_2_arrived_at,
                'trip_2_berthing_at' => $request->trip_2_berthing_at,
                'operator' => $request->operator,
                'helper' => $request->helper,
                'load' => $request->load,
                'refule_km' => $request->refule_km,
                'refule_liter' => $request->refule_liter,
                'refule_mechine' => $request->refule_mechine,
                'refule_genset' => $request->refule_genset,
                'refule_type' => $request->refule_type,
                'input_method' => 'Web',
                'type' => $type,
                'service_type' => $request->service_type,
                'duration_trip_1' => $duration_trip_1,
                'duration_trip_2' => $duration_trip_2,
                'remarks' => $request->remarks,
            ];
            $lockDaily_report = Daily_report::where('id', $daily_report->id)->lockForUpdate()->first();
            $lockDaily_report->update($data);
            $daily_report->daily_report_detail()->delete();
            if ($request->filled('detail_unit_id')) {
                $details = [];
                foreach ($request->input('detail_unit_id', []) as $key => $unitId) {
                    if (blank($unitId)) {
                        continue;
                    }
                    $details[] = [
                        'request_token' => $daily_report->request_token,
                        'unit_id'       => $unitId,
                        'item'          => $request->input("item.$key"),
                        'uom_1'         => $request->input("uom_1.$key"),
                        'value_1'       => $request->input("value_1.$key", 0),
                        'uom_2'         => $request->input("uom_2.$key"),
                        'value_2'       => $request->input("value_2.$key", 0),
                    ];
                }
                if (!empty($details)) {
                    $daily_report
                        ->daily_report_detail()
                        ->createMany($details);
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
     * Remove the specified resource from storage.
     */
    public function destroy(Daily_report $daily_report)
    {
        DB::beginTransaction();
        try {
            $daily_report->daily_report_detail()->delete();
            $daily_report->delete();
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

    /**
     * Ngambil form tambah
     */
    public function get_form_add(Request $request)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $unit = Unit::find($request->unit_id);
            $view = 'daily_report.form';
            $html = view($view)->render();
            if ($unit && $unit->type == 'LCT') {
                $location = Location::where('loc_type', 'Project Location')->get();
                $view = 'daily_report.form-lct';
                $html = view($view, compact('location'))->render();
            }
            $report_prev_no = Generator::make()
                ->type('rep')
                ->formatter($presenter)
                ->preview();
            return response()->json([
                'success' => true,
                'html' => $html,
                'report_prev_no' => $report_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil form edit
     */
    public function get_form_edit(Request $request, Daily_report $daily_report)
    {
        try {
            $daily_report_detail = $daily_report->daily_report_detail;
            $view = 'daily_report.form-edit';
            $unit = Unit::find($daily_report->unit_id);
            $html = view($view, compact('daily_report', 'daily_report_detail'))->render();
            if ($unit && $unit->type == 'LCT') {
                $location = Location::where('loc_type', 'Project Location')->get();
                $view = 'daily_report.form-lct-edit';
                $html = view($view, compact('daily_report', 'daily_report_detail', 'location'))->render();
            }
            return response()->json([
                'success' => true,
                'html' => $html,
                'unit' => $unit,
                'report_no' => $daily_report->report_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data project
     */
    public function get_project_location(Request $request)
    {
        try {
            $location = Location::where('loc_type', 'Project Location')->get();
            return response()->json([
                'success' => true,
                'data' => $location
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngambil detail daily report
     */
    public function get_detail(Request $request, Daily_report $daily_report)
    {
        try {
            $daily_report_detail = $daily_report->daily_report_detail;
            $view = 'daily_report.detail';
            return response()->view($view, compact('daily_report', 'daily_report_detail'), 200);
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
    public function print(Request $request, Daily_report $daily_report)
    {
        $pdf = Pdf::loadView('daily_report.print', [
            'daily_report' => $daily_report,
            'daily_report_detail' => $daily_report->daily_report_detail
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
        $safeFilename = Str::of($daily_report->report_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Daily_report $daily_report)
    {
        $pdf = Pdf::loadView('daily_report.print', [
            'daily_report' => $daily_report,
            'daily_report_detail' => $daily_report->daily_report_detail
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
        $safeFilename = Str::of($daily_report->report_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("{$safeFilename}.pdf");
    }
}
