<?php

namespace App\Http\Controllers;

use App\Models\Mechanical_inspection;
use App\Models\Mechanical_inspection_detail;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;

class MechanicalInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $mechanical_inspection = Mechanical_inspection::query();
            if (request()->unit_id != 'All') {
                $mechanical_inspection = $mechanical_inspection->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $mechanical_inspection = $mechanical_inspection->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $mechanical_inspection = $mechanical_inspection->where('date', '<=', request()->date_end);
            }
            $mechanical_inspection = $mechanical_inspection->orderBy('date', 'desc')->get();
            return DataTables::of($mechanical_inspection)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('') . '">Export to PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Print</a>
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
                ->addColumn('result', function ($item) {
                    $total_item = $item->mechanical_inspection_detail()->count();
                    $total_broken = $item->mechanical_inspection_detail()->where('condition', "Not OK")->count();
                    $total_good = $item->mechanical_inspection_detail()->where('condition', "OK")->count();
                    $percent = $total_good / $total_item * 100;
                    return $percent;
                })
                ->addColumn('broken', function ($item) {
                    $total_broken = $item->mechanical_inspection_detail()->where('condition', "Not OK")->count();
                    return $total_broken;
                })
                ->addColumn('condition', function ($item) {
                    $total_item = $item->mechanical_inspection_detail()->count();
                    $total_broken = $item->mechanical_inspection_detail()->where('condition', "Not OK")->count();
                    $condition = $total_good / $total_item * 100;
                    return Number::format($condition, precision: 0);
                })
                ->make();
        }
        $inspection_item = config('mechanical-inspection');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Mechanical Inspection',
            'route-sub-module' => 'mechanicalinspection.index',
        ];
        return view('mechanical_inspection.index', compact('breadcrum', 'inspection_item'));
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
                'inspector' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'condition',
                    'remarks',
                    'inspected_by',
                ),
                ['input_method' => 'Web']
            );
            $mechanical_inspection = Mechanical_inspection::create($data);
            foreach ($request->inspection_item as $i => $item) {
                $p2h->p2h_detail()->updateOrCreate(
                    ['inspection_item' => $item],
                    [
                        'inspection_group' => $request->inspection_group[$i],
                        'condition' => (int) ($request->condition[$item] ?? 0),
                        'remarks' => $request->remarks[$i],
                        'inspected_by' => $request->inspected_by[$i]
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
    public function show(Mechanical_inspection $mechanical_inspection)
    {
        $mechanical_inspection = $mechanical_inspection->mechanical_inspection_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $mechanical_inspection,
            'mechanical_inspection' => $mechanical_inspection
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mechanical_inspection $mechanical_inspection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
                'inspector' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'condition',
                    'remarks',
                    'inspected_by',
                ),
                ['input_method' => 'Web']
            );
            $p2h = P2h::update($data);
            foreach ($request->inspection_item as $i => $item) {
                $p2h->p2h_detail()->updateOrCreate(
                    ['inspection_item' => $item],
                    [
                        'inspection_group' => $request->inspection_group[$i],
                        'condition' => (int) ($request->condition[$item] ?? 0),
                        'remarks' => $request->remarks[$i],
                        'inspected_by' => $request->inspected_by[$i]
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
    public function destroy(Mechanical_inspection $mechanical_inspection)
    {
        DB::beginTransaction();
        try {
            $mechanical_inspection->mechanical_inspection_detail()->delete();
            $mechanical_inspection->delete();
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
     * Ngambil tabel list p2h nya
     */
    public function get_table_add(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        try {
            $view = 'mechanical_inspection.table-add';
            $inspection_item = config('mechanical-inspection');
            $inspection_prev_no = Generator::make()
                ->type('inspection')
                ->preview();
            $html = view($view, compact('inspection_item'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'inspection_prev_no' => $inspection_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list mechanical inspection nya
     */
    public function get_table_edit(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        try {
            $view = 'mechanical_inspection.table-edit';
            $inspection_item = config('mechanical-inspection');
            $html = view($view, compact('inspection_item', 'p2h'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'inspection_no' => $mechanical_inspection->inspection_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
