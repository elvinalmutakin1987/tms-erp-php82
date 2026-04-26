<?php

namespace App\Http\Controllers;

use App\Models\Client_vendor;
use App\Models\Contract;
use App\Models\Unit;
use App\Models\Unit_rate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class UnitRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $unit_rate = Unit_rate::query()
                ->leftJoin('contracts', 'contracts.id', '=', 'unit_rates.contract_id')
                ->leftJoin('client_vendors', 'client_vendors.id', '=', 'contracts.client_vendor_id')
                ->leftJoin('units', 'units.id', '=', 'unit_rates.unit_id')
                ->select([
                    'unit_rates.*',
                    'contracts.contract_no as contract_no',
                    'units.vehicle_no as vehicle_no',
                ]);
            if (request()->filled('client_vendor_id') && request('client_vendor_id') != 'All') {
                $unit_rate = $unit_rate->where('contracts.client_vendor_id', request('client_vendor_id'));
            }
            if (request()->filled('contract_id') && request('contract_id') != 'All') {
                $unit_rate = $unit_rate->where('unit_rates.contract_id', request('contract_id'));
            }
            return DataTables::of($unit_rate)
                ->filterColumn('contract_no', function ($query, $keyword) {
                    $query->where('contracts.contract_no', 'like', "%{$keyword}%");
                })
                ->filterColumn('vehicle_no', function ($query, $keyword) {
                    $query->where('units.vehicle_no', 'like', "%{$keyword}%");
                })
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
                ->addColumn('contract', function ($item) {
                    return $item->contract->contract_no;
                })
                ->addColumn('unit', function ($item) {
                    return $item->unit->vehicle_no;
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Unit Rate',
            'route-sub-module' => 'unitrate.index',
        ];
        return view('unitrate.index', compact('breadcrum'));
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
                'contract_id' =>  'required|',
                'unit_id' => 'required',
                'rate' => 'required',
                'target' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method'));
            Unit_rate::firstOrCreate($data);
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
    public function show(Unit_rate $unit_rate)
    {
        $client_vendor = $unit_rate->contract->client_vendor;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $unit_rate,
            'client_vendor' => $client_vendor
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit_rate $unit_rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit_rate $unit_rate)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'contract_id' =>  'required',
                'unit_id' => 'required',
                'rate' => 'required',
                'target' => 'required',
            ]);
            $data = array_merge($request->except('_token', '_method', 'request_token'));
            $unit_rate->update($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'title' => 'Saved!',
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
    public function destroy(Unit_rate $unit_rate)
    {
        DB::beginTransaction();
        try {
            $unit_rate->delete();
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
     * Ngambil data semua data client
     */
    public function get_client_all(Request $request)
    {
        try {
            $client = Client_vendor::all();
            return response()->json([
                'success' => true,
                'data' => $client
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data semua data contract
     */
    public function get_contract(Request $request)
    {
        try {
            $contract = Contract::all();
            if ($request->client_vendor_id && $request->client_vendor_id != 'All') {
                $contract = Contract::where('client_vendor_id', $request->client_vendor_id)
                    ->where('status', 'Active')
                    ->get();
            }
            return response()->json([
                'success' => true,
                'data' => $contract
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data semua data unit
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
