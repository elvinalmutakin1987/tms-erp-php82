<?php

namespace App\Http\Controllers;

use App\Models\Unit_model;
use App\Models\Unit_brand;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $unit_model = Unit_model::query();
            return DataTables::of($unit_model)
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
                    return $item->unit_brand->name;
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Unit Model',
            'route-sub-module' => 'unitmodel.index',
        ];
        return view('unitmodel.index', compact('breadcrum'));
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
                'desc' => 'required|unique:unit_models,desc',
                'unit_brand_id' => 'required'
            ]);
            $data = array_merge($request->except('_token', '_method'));
            Unit_model::firstOrCreate($data);
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
    public function show(Unit_model $unit_model)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $unit_model,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit_model $unit_model)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit_model $unit_model)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'desc' => 'required|unique:unit_models,desc,' . $unit_model->id . ',id',
                'unit_brand_id' => 'required'
            ]);
            $data = array_merge($request->except('_token', '_method', 'request_token'));
            $unit_model->update($data);
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
    public function destroy(Unit_model $unit_model)
    {
        DB::beginTransaction();
        try {
            $unit_model->delete();
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
}
