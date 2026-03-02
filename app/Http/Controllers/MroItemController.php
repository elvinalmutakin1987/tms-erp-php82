<?php

namespace App\Http\Controllers;

use App\Models\Mro_item;
use App\Models\Mro_unit;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;


class MroItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $mro_item = Mro_item::query();
            return DataTables::of($mro_item)
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
                ->addColumn('unit', function ($item) {
                    $return = '<ul>';
                    $mro_unit  = Mro_unit::where('mro_item_id', $item->id)->get();
                    foreach ($mro_unit as $d) {
                        $return .= '<li><i class="icon-copy bi bi-caret-right-fill"></i>' . $d->unit->vehicle_no . '</li>';
                    }
                    $return .= '</ul>';
                    return new HtmlString($return);
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'MRO Item',
            'route-sub-module' => 'mroitem.index',
        ];
        return view('mroitem.index', compact('breadcrum'));
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
                'part_number' => 'required|unique:mro_items,part_number',
                'name' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            $mro_item = Mro_item::create($data);
            if ($request->has('unit_id')) {
                $mro_item->mro_unit()->attach($request->unit_id);
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
    public function show(Mro_item $mro_item)
    {
        $mro_unit = Mro_unit::where('mro_item_id', $mro_item->id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $mro_item,
            'mro_unit' => $mro_unit
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mro_item $mro_item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mro_item $mro_item)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'part_number' => 'required|unique:mro_items,part_number,' . $mro_item->id . ',id',
                'name' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            $mro_item->update($data);
            if ($request->has('unit_id')) {
                $mro_item->mro_unit()->sync($request->unit_id);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Updated!',
                'message' => 'Data updated!'
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
    public function destroy(Mro_item $mro_item)
    {
        DB::beginTransaction();
        try {
            $mro_item->mro_unit()->detach();
            $mro_item->delete();
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
}
