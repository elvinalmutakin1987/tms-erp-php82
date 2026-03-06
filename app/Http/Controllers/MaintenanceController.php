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
            if (request()->vendor != 'All') {
                $maintenance = $maintenance->where('client_vendor_id', request()->status);
            }
            if (request()->date_start != '') {
                $maintenance = $maintenance->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $maintenance = $maintenance->where('date', '<=', request()->date_end);
            }
            $maintenance = $maintenance->orderBy('date', 'desc')->get();
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
                                    <a class="dropdown-item exportPdfButton" href="' . route('mechanicalinspection.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('mechanicalinspection.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                               <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>
                                <li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>
                                <li>
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
                ->make();
        }
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Maintenance',
            'route-sub-module' => 'maintenance.index',
        ];
        return view('maintenance.index', compact('breadcrum'));
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
                'cost_total' => 'required',
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
                    'cost',
                ),
                ['input_method' => 'Web']
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
                        'mro_item_id' => $request->mro_item_id[$key],
                        'notes' => $request->notes[$key],
                        'cost' => $request->cost[$key],
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
                'cost_total' => 'required',
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
                    'cost',
                ),
                ['input_method' => 'Web']
            );
            $maintenance->update($data);
            foreach ($request->maintenance_item_id as $key => $item) {
                $maintenance->maintenance_detail()->updateOrCreate(
                    [
                        'maintenance_item_id' => $item,
                    ],
                    [
                        'maintenance_item_id' => $request->maintenance_item_id[$key],
                        'action' => $request->action[$key],
                        'mro_item_id' => $request->mro_item_id[$key],
                        'notes' => $request->notes[$key],
                        'cost' => $request->cost[$key],
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
            $html = view($view, compact('maintenance_detail'))->render();
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
}
