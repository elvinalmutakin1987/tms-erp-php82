<?php

namespace App\Http\Controllers;

use App\Models\Maintenance_item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class MaintenanceItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $maintenance_item = Maintenance_item::query();
            if (request()->action != 'All') {
                $maintenance_item = $maintenance_item->where('action', request()->action);
            }
            $maintenance_item = $maintenance_item->get();
            return DataTables::of($maintenance_item)
                ->addIndexColumn()
                ->addColumn('act', function ($item) {
                    return $item->action;
                })
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
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Maintenance Item',
            'route-sub-module' => 'maintenanceitem.index',
        ];
        return view('maintenance_item.index', compact('breadcrum'));
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
                'name' => 'required',
                'action' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            Maintenance_item::create($data);
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
    public function show(Maintenance_item $maintenance_item)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $maintenance_item,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance_item $maintenance_item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance_item $maintenance_item)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required',
                'action' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            $maintenance_item->update($data);
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
    public function destroy(Maintenance_item $maintenance_item)
    {
        DB::beginTransaction();
        try {
            $maintenance_item->delete();
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
}
