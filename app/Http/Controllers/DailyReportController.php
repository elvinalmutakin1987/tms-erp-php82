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

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $daily_report = Daily_report::query();
            if (request()->unit_id != 'All') {
                $daily_report = $daily_report->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $daily_report = $daily_report->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $daily_report = $daily_report->where('date', '<=', request()->date_end);
            }
            $daily_report = $daily_report->orderBy('date', 'desc')->get();
            return DataTables::of($daily_report)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('dailyreport.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('dailyreport.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                               <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('inspection.delete')):
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
                ->addColumn('total_km_duration', function ($item) {
                    if ($item->type != 'LCT') {
                        return $item->km_total;
                    }
                    return $item->duration_trip;
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Daily Report',
            'route-sub-module' => 'dailyreport.index',
        ];
        return view('daily_report.index', compact('breadcrum'));
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
            if ($request->type == 'LCT') {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                    'duration_trip' => 'required'
                ]);
            } else {
                $request->validate([
                    'unit_id' => ['required', 'not_in:All'],
                    'date' => 'required',
                    'km_start' => 'required',
                    'km_finish' => 'required',
                    'km_total' => 'required',
                ]);
            }
            $unit = Unit::find($request->unit_id);
            $type = 'Vehicle';
            if ($unit->type == 'LCT') {
                $type = 'LCT';
            }
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'daily_report_id',
                    'location_id',
                    'loading_at',
                    'complete_loading_at',
                    'depart_at',
                    'duration_trip',
                    'lct_unit_id',
                    'item',
                    'uom',
                    'value',
                ),
                [
                    'input_method' => 'Web',
                    'type' => $type
                ]
            );
            $daily_report = Daily_report::create($data);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Daily_report $daily_report)
    {
        //
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
     * Ngambil form tambah
     */
    public function get_form_add(Request $request, Daily_report $daily_report)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $unit = Unit::find($request->unit_id);
            $view = 'daily_report.form';
            if ($unit && $unit->type == 'LCT') {
                $view = 'daily_report.form-lct';
            }
            $report_prev_no = Generator::make()
                ->type('rep')
                ->formatter($presenter)
                ->preview();
            $html = view($view)->render();
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
            if ($unit && $unit->type == 'LCT') {
                $view = 'daily_report.form-edit-lct';
            }
            $html = view($view, compact('daily_report', 'daily_report_detail'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
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
}
