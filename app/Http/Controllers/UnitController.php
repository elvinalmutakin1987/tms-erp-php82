<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Unit_brand;
use App\Models\Unit_model;
use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $unit = Unit::query();
            if (request()->typeUnit != 'All') {
                $unit = $unit->where('type', request()->typeUnit);
            }
            $unit = $unit->leftJoin('locations', 'units.location_id', '=', 'locations.id')
                ->select('units.*', 'locations.name as location');
            $unit = $unit->get();
            return DataTables::of($unit)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                data-id="' . $item->id . '">Edit</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('brand', function ($item) {
                    return $item->unit_model->unit_brand->name;
                })
                ->addColumn('model', function ($item) {
                    return $item->unit_model->desc;
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Unit',
            'route-sub-module' => 'unit.index',
        ];
        $typeunit = config('typeunit');
        return view('unit.index', compact('breadcrum', 'typeunit'));
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
                'vehicle_no' => 'required|unique:units,vehicle_no',
                'location_id' => 'required',
                'type' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            Unit::create($data);
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
    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $unit,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'vehicle_no' => 'required|unique:units,vehicle_no,' . $unit->id . ',id',
                'location_id' => 'required',
                'type' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            $unit->update($data);
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
    public function destroy(Unit $unit)
    {
        DB::beginTransaction();
        try {
            $unit->delete();
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
     * Ngambil data location
     */
    public function get_location_all(Request $request)
    {
        try {
            $location = Location::where('loc_type', 'Unit Location')->get();
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
     * Ngambil data brand
     */
    public function get_brand_all(Request $request)
    {
        try {
            $unit_brand = Unit_brand::all();
            return response()->json([
                'success' => true,
                'data' => $unit_brand
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }


    /**
     * Ngambil data model
     */
    public function get_model_all(Request $request)
    {
        try {
            $unit_model = Unit_model::where('unit_brand_id', $request->unit_brand_id)->get();
            return response()->json([
                'success' => true,
                'data' => $unit_model
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
