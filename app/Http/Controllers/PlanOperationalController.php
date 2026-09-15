<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Plan_detail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PlanOperationalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $location = Plan::query();
            return DataTables::of($location)
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
            'sub-module' => 'Plan - Operatinal',
            'route-sub-module' => 'plan.operational.index',
        ];
        return view('plan.operational.index', compact('breadcrum'));
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
            $recenlyCreated = true;
            $request->validate([
                'year' => 'required',
            ]);
            $data = array_merge(
                $request->only([
                    'year',
                    'type'
                ]),
                [
                    'request_token' => $request->request_token,
                    'department' => 'Operational',
                ]
            );
            $plan = Plan::where('year', $request->year)
                ->where('department', 'Operational')
                ->where('type', $request->type)
                ->first();
            if ($plan) {
                $recenlyCreated = false;
                $plan->update($data);
            } else {
                $plan = Plan::create($data);
            }
            if ($recenlyCreated === false) {
                $plan->plan_detail()->delete();
            }
            $details = [];
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'value-')) {
                    $monthKey = str_replace('value-', '', $key);
                    $details[] = [
                        'request_token' => $plan->request_token,
                        'periode' => $plan->year . '-' . str_pad($monthKey, 2, '0', STR_PAD_LEFT),
                        'month' => str_pad($monthKey, 2, '0', STR_PAD_LEFT),
                        'value' => blank($value) ? null : (float) $value,
                    ];
                }
            }
            if ($details !== []) {
                $plan->plan_detail()->createMany($details);
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
    public function show(Plan $plan)
    {
        $plan_detail = Plan_detail::where('plan_id', $plan->id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $plan,
            'detail' => $plan_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'year' => 'required',
            ]);
            $data = array_merge(
                $request->only([
                    'year',
                    'type'
                ]),
                [
                    'department' => 'Operational',
                ]
            );
            $plan->update($data);
            $plan->plan_detail()->delete();
            $details = [];
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'value-')) {
                    $monthKey = str_replace('value-', '', $key);
                    $details[] = [
                        'request_token' => $plan->request_token,
                        'periode' => $plan->year . '-' . str_pad($monthKey, 2, '0', STR_PAD_LEFT),
                        'month' => str_pad($monthKey, 2, '0', STR_PAD_LEFT), // Format MM
                        'value' => blank($value) ? null : (float) $value,
                    ];
                }
            }
            if ($details !== []) {
                $plan->plan_detail()->createMany($details);
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
    public function destroy(Plan $plan)
    {
        DB::beginTransaction();
        try {
            $plan->plan_detail()->delete();
            $plan->delete();
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
