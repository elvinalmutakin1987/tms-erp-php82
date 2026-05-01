<?php

namespace App\Http\Controllers;

use App\Models\Client_vendor;
use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ClientVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $client_vendor = Client_vendor::query();
            if (request()->type != 'All') {
                $client_vendor = $client_vendor->where('type', request()->type)->get();
            }
            return DataTables::of($client_vendor)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                data-id="' . $item->id . '" data-type="' . $item->type . '">Edit</a>
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
        $bank = config('bank');
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Client Vendor',
            'route-sub-module' => 'clientvendor.index',
        ];
        return view('clientvendor.index', compact('breadcrum', 'bank'));
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
            ]);
            $data = array_merge($request->except('_token', '_method'));
            Client_vendor::firstOrCreate($data);
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
    public function show(Client_vendor $client_vendor)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $client_vendor,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client_vendor $client_vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client_vendor $client_vendor)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method', 'request_token'));
            $client_vendor->update($data);
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
    public function destroy(Client_vendor $client_vendor)
    {
        DB::beginTransaction();
        try {
            $client_vendor->delete();
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
