<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Service_item;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $service = Service::query();
            if (request()->type != 'All') {
                $service = $service->where('type', request()->type)->get();
            }
            return DataTables::of($service)
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
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Service',
            'route-sub-module' => 'service.index',
        ];
        $servicetype = config('servicetype');
        return view('service.index', compact('breadcrum', 'servicetype'));
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
                'name' => 'required|unique:services,name',
                'type' => 'required'
            ]);
            $data = array_merge($request->except(
                '_token',
                '_method',
                'txt_item_no',
                'txt_item_des',
                'item_no',
                'item_des'
            ));
            $service = Service::create($data);
            if ($request->item_no) {
                foreach ($request->item_no as $key => $item_no) {
                    $detail[] = [
                        'service_id' => $service->id,
                        'item_no' => $item_no,
                        'item_des' => $request->item_des[$key],
                    ];
                }
                $service->service_item()->createMany($detail);
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
    public function show(Service $service)
    {
        $service_item = Service_item::where('service_id', $service->id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $service,
            'service_item' => $service_item
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|unique:services,name,' . $service->id . ',id',
                'type' => 'required'
            ]);
            $data = array_merge($request->except(
                '_token',
                '_method',
                'txt_item_no',
                'txt_item_des',
                'item_no',
                'item_des'
            ));
            $service = Service::update($data);
            if ($request->item_no) {
                foreach ($request->item_no as $key => $item_no) {
                    $detail[] = [
                        'service_id' => $service->id,
                        'item_no' => $request->item_no[$key],
                        'item_des' => $request->item_des[$key],
                    ];
                }
                $service->service_item()->sync($detail);
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
    public function destroy(Service $service)
    {
        DB::beginTransaction();
        try {
            $service->service_item()->delete();
            $service->delete();
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
     * Ngambil data service item
     */
    public function get_service_item_list(Request $request)
    {
        try {
            $service = Service::find($request->service_id);
            $service_item = Service_item::where('service_id', $request->service_id)->get();
            $view = 'service.service-item-list';
            return response()->view($view, compact('service', 'service_item'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
