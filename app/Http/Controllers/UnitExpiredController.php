<?php

namespace App\Http\Controllers;

use App\Exports\UnitsExport;
use App\Models\Unit;
use App\Models\Unit_brand;
use App\Models\Unit_model;
use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UnitExpiredController extends Controller
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
            if (request()->location != 'All') {
                $unit = $unit->where('location_id', request()->location);
            }
            if (request()->from != '') {
                $from = request()->from;
                $to = request()->to ?? date('Y-m-d');
                $unit = $unit->where(function ($query) use ($from, $to) {
                    $query->whereBetween('exp_crane', [$from, $to])
                        ->orWhereBetween('exp_fuel_issue', [$from, $to])
                        ->orWhereBetween('exp_tbst', [$from, $to])
                        ->orWhereBetween('exp_pass_road_1', [$from, $to])
                        ->orWhereBetween('exp_stnk', [$from, $to])
                        ->orWhereBetween('exp_tax', [$from, $to])
                        ->orWhereBetween('exp_comm', [$from, $to]);
                });
            }
            if (request()->to != '') {
                $from = request()->from ?? date('Y-m-d');
                $to = request()->to;
                $unit = $unit->where(function ($query) use ($from, $to) {
                    $query->whereBetween('exp_crane', [$from, $to])
                        ->orWhereBetween('exp_fuel_issue', [$from, $to])
                        ->orWhereBetween('exp_tbst', [$from, $to])
                        ->orWhereBetween('exp_pass_road_1', [$from, $to])
                        ->orWhereBetween('exp_stnk', [$from, $to])
                        ->orWhereBetween('exp_tax', [$from, $to])
                        ->orWhereBetween('exp_comm', [$from, $to]);
                });
            }
            $unit = $unit->leftJoin('locations', 'units.location_id', '=', 'locations.id')
                ->select('units.*', 'locations.name as location');
            $unit = $unit->get();
            return DataTables::of($unit)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <a type="button" class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                data-id="' . $item->id . '">Edit</a>
                    </div>
                    ';
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                data-id="' . $item->id . '">Edit</a>
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
        $location = Location::where('loc_type', 'Unit Location')->get();
        $typeunit = config('typeunit');
        $breadcrum = [
            'module' => 'Safety',
            'route-module' => null,
            'sub-module' => 'Unit Expired',
            'route-sub-module' => 'unitexpired.index',
        ];
        return view('unit_expired.index', compact('breadcrum', 'location', 'typeunit'));
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
        //
    }

    /**
     * Untuk export unit
     */
    public function export()
    {
        return Excel::download(new UnitsExport, 'unit_expired.xlsx');
    }
}
