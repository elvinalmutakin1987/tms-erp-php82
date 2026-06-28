<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client_vendor;
use App\Models\Contract_fmf;
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
            if (request()->service_id != '') {
                $contract = $contract->where('service_id', request()->service_id);
            }
            if (request()->client_vendor_id != '') {
                $contract = $contract->where('client_vendor_id', request()->client_vendor_id);
            }
            $contract = $contract->get();
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
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>
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
                ->addColumn('service', function ($item) {
                    return $item->service->name;
                })
                ->make();
        }
        $uom = config('uom');
        $breadcrum = [
            'module' => 'Master Data',
            'route-module' => null,
            'sub-module' => 'Contract',
            'route-sub-module' => 'contract.index',
        ];
        return view('contract.index', compact('breadcrum', 'uom'));
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
                    'request_token',
                    'contract_no',
                    'client_vendor_id',
                    'service_id',
                    'start_date',
                    'end_date',
                    'value',
                    'notes'
                ),
                [
                    'request_token' => $request->request_token,
                    'status' => 'Active'
                ]
            );

            $contract = Contract::firstOrCreate($data);
            //service rate
            if ($request->item_no) {
                foreach ($request->item_no as $i => $item) {
                    $rate = isset($request->rate[$i]) ? $request->rate[$i] : 0;
                    $contract->contract_rate()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'item_no' => $item,
                            'request_token' => $contract->request_token,
                            'service_item' => $request->service_item[$i],
                            'rate' => $rate,
                            'notes' => $request->note_rates[$i]
                        ],
                    );
                }
            }
            //unit rate
            if ($request->unit_id) {
                foreach ($request->unit_id as $i => $item) {
                    $target = isset($request->target[$i]) ? $request->target[$i] : 0;
                    $price = isset($request->price[$i]) ? $request->price[$i] : 0;
                    $contract->unit_target()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'unit_id' => $item,
                            'request_token' => $contract->request_token,
                            'target' => $target,
                            'price' => $price
                        ],
                    );
                }
            }
            //fmf
            if ($request->year) {
                foreach ($request->year as $i => $item) {
                    $value = isset($request->value[$i]) ? $request->value[$i] : 0;
                    $contract->contract_fmf()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'year' => $item,
                            'request_token' => $contract->request_token,
                            'value' => $value,
                        ],
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
        $service = Service::find($contract->service_id);
        $client_vendor = Client_vendor::find($contract->client_vendor_id);
        $service_item = Service_item::where('service_id', $contract->service_id)->get();
        $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
        $unit_target = Unit_target::where('contract_id', $contract->id)->get();
        $contract_fmf = Contract_fmf::where('contract_id', $contract->id)->get();
        $view_item = 'contract.item-edit';
        $view_target = 'contract.target-edit';
        $view_fmf = 'contract.fmf-edit';
        $html_item = view($view_item, compact('contract_rate'))->render();
        $html_target = view($view_target, compact('unit_target'))->render();
        $html_fmf = view($view_fmf, compact('contract_fmf'))->render();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $contract,
            'html_item' => $html_item,
            'html_target' => $html_target,
            'html_fmf' => $html_fmf,
            'service' => $service,
            'client_vendor' => $client_vendor
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
            //service rate
            $contract->contract_rate()->delete();
            if ($request->item_no) {
                foreach ($request->item_no as $i => $item) {
                    $rate = isset($request->rate[$i]) ? $request->rate[$i] : 0;
                    $contract->contract_rate()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'item_no' => $item,
                            'request_token' => $contract->request_token,
                            'service_item' => $request->service_item[$i],
                            'est_qty_per_month' => $request->est_qty_per_month[$i],
                            'unit' => $request->unit[$i],
                            'periode' => $request->periode[$i],
                            'est_subtotal' => $request->est_subtotal[$i],
                            'rate' => $rate
                        ]
                    );
                }
            }
            //unit rate
            $contract->unit_target()->delete();
            if ($request->unit_id) {
                foreach ($request->unit_id as $i => $item) {
                    $target = isset($request->target[$i]) ? $request->target[$i] : 0;
                    $price = isset($request->price[$i]) ? $request->price[$i] : 0;
                    $contract->unit_target()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'unit_id' => $item,
                            'request_token' => $contract->request_token,
                            'target' => $target,
                            'price' => $price
                        ]
                    );
                }
            }
            //fmf
            $contract->contract_fmf()->delete();
            if ($request->year) {
                foreach ($request->year as $i => $item) {
                    $value = isset($request->value[$i]) ? $request->value[$i] : 0;
                    $contract->contract_fmf()->firstOrCreate(
                        [
                            'contract_id' => $contract->id,
                            'year' => $item,
                            'request_token' => $contract->request_token,
                            'value' => $value,
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
        if ($request->ajax()) {
            $term = trim($request->term);
            $client_vendor = Client_vendor::selectRaw("id, name as text")
                ->where('type', 'Client')
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($client_vendor);
            $morePages = true;
            $pagination_obj = json_encode($client_vendor);
            if (empty($client_vendor->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $client_vendor->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * Ngambil data semua data service
     */
    public function get_service_all(Request $request)
    {
        if ($request->ajax()) {
            $term = trim($request->term);
            $service = Service::selectRaw("id, name as text")
                ->where('name', 'like', '%' . $term . '%')
                ->orderBy('name')->simplePaginate(10);
            $total_count = count($service);
            $morePages = true;
            $pagination_obj = json_encode($service);
            if (empty($service->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $service->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
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
        if ($request->ajax()) {
            $term = trim($request->term);
            $unit = Unit::selectRaw("id, vehicle_no as text")
                ->where('vehicle_no', 'like', '%' . $term . '%')
                ->orderBy('vehicle_no')->simplePaginate(10);
            $total_count = count($unit);
            $morePages = true;
            $pagination_obj = json_encode($unit);
            if (empty($unit->nextPageUrl())) {
                $morePages = false;
            }
            $result = [
                "results" => $unit->items(),
                "pagination" => [
                    "more" => $morePages
                ],
                "total_count" => $total_count
            ];
            return response()->json($result);
        }
    }

    /**
     * ngambil detail contract
     */
    public function get_detail(Request $request, $contract_id)
    {
        try {
            $contract = Contract::find($contract_id);
            $contract_fmf = Contract_fmf::where('contract_id', $contract_id)->get();
            $contract_rate = Contract_rate::where('contract_id', $contract_id)->get();
            $unit_target = Unit_target::where('contract_id', $contract_id)->get();
            $view = 'contract.detail';
            return response()->view($view, compact('contract', 'contract_fmf', 'contract_rate', 'unit_target'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
