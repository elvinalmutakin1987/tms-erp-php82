<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client_vendor;
use App\Models\Contract_rate;
use App\Models\Service;
use App\Models\Service_item;
use App\Models\Unit;
use App\Models\Unit_target;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $contract = Contract::query();
            if (request()->service_id != 'All') {
                $contract = $contract->where('service_id', request()->service_id)->get();
            }
            return DataTables::of($contract)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button_change_status = '<li>
                                    <a class="dropdown-item changeStatusButton" href="#"
                                    data-id="' . $item->id . '" data-status="Deactive" >Activate</a>
                                </li>';
                    if ($item->status == 'Active') {
                        $button_change_status = '<li>
                            <a class="dropdown-item changeStatusButton" href="#"
                            data-id="' . $item->id . '" data-status="Active">Deactivate</a>
                        </li>';
                    }
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                                <ul class="dropdown-menu">
                                ' . $button_change_status . '
                                <li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
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
                ->addColumn('client', function ($item) {
                    return $item->client_vendor->name;
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Contract',
            'route-sub-module' => 'contract.index',
        ];
        return view('contract.index', compact('breadcrum'));
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
                'contract_no' =>  'required',
                'client_vendor_id' => 'required',
                'service_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $data = array_merge(
                $request->only(
                    'contract_no',
                    'client_vendor_id',
                    'service_id',
                    'start_date',
                    'end_date',
                    'value',
                    'notes'
                ),
                [
                    'status' => 'Active'
                ]
            );
            $contract = Contract::create($data);
            if ($request->service_item_id) {
                foreach ($request->service_item_id as $i => $item) {
                    $rate = isset($request->rate[$i]) ? $request->rate[$i] : 0;
                    $contract->contract_rate()->updateOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'service_item_id' => $item,
                        ],
                        [
                            'rate' => $rate
                        ]
                    );
                }
            }
            if ($request->unit_id) {
                foreach ($request->unit_id as $i => $item) {
                    $target = isset($request->target[$i]) ? $request->target[$i] : 0;
                    $price = isset($request->price[$i]) ? $request->price[$i] : 0;
                    $contract->unit_target()->updateOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'unit_id' => $item,
                        ],
                        [
                            'target' => $target,
                            'price' => $price
                        ]
                    );
                }
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
    public function show(Contract $contract)
    {
        $service_item = Service_item::where('service_id', $contract->service_id)->get();
        $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
        $unit_target = Unit_target::where('contract_id', $contract->id)->get();
        $view = 'contract.rate-edit';
        $html = view($view, compact('service_item', 'contract_rate', 'contract'))->render();
        $view_target = 'contract.target-edit';
        $html_target = view($view_target, compact('unit_target'))->render();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $contract,
            'html' => $html,
            'html_target' => $html_target
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'contract_no' =>  'required',
                'client_vendor_id' => 'required',
                'service_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $data = array_merge(
                $request->only(
                    'contract_no',
                    'client_vendor_id',
                    'service_id',
                    'start_date',
                    'end_date',
                    'value',
                    'notes'
                )
            );
            $contract->update($data);
            if ($request->service_item_id) {
                foreach ($request->service_item_id as $i => $item) {
                    $rate = isset($request->rate[$i]) ? $request->rate[$i] : 0;
                    $contract->contract_rate()->updateOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'service_item_id' => $item,
                        ],
                        [
                            'rate' => $rate
                        ]
                    );
                }
            }
            if ($request->unit_id) {
                foreach ($request->unit_id as $i => $item) {
                    $target = isset($request->target[$i]) ? $request->target[$i] : 0;
                    $price = isset($request->price[$i]) ? $request->price[$i] : 0;
                    $contract->unit_target()->updateOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'unit_id' => $item,
                        ],
                        [
                            'target' => $target,
                            'price' => $price
                        ]
                    );
                }
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
    public function destroy(Contract $contract)
    {
        DB::beginTransaction();
        try {
            $contract->delete();
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
            $client_vendor = Client_vendor::where('type', 'Client')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $client_vendor
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data semua data service
     */
    public function get_service_all(Request $request)
    {
        try {
            $service = Service::all();
            return response()->json([
                'success' => true,
                'data' => $service
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_status(Request $request, Contract $contract)
    {
        DB::beginTransaction();
        try {
            $contract->status = 'Active';
            if ($request->status == 'Active') {
                $contract->status = 'Deactive';
            }
            $contract->save();
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
     * Ngambil service item
     */
    public function get_service_item(Request $request)
    {
        try {
            $service_item = Service_item::where('service_id', $request->service_id)->get();
            $view = 'contract.rate';
            $html = view($view, compact('service_item'))->render();
            return response()->json([
                'success' => true,
                'html' => $html
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
