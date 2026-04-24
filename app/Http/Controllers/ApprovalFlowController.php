<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApprovalFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $approval_flow = Approval_flow::query();
            return DataTables::of($approval_flow)
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
                ->addColumn('step_total', function ($item) {
                    return Approval_step::where('approval_flow_id', $item->id)->count();
                })
                ->make();
        }
        $department = config('department');
        $approvable_model = config('approvable_model');
        $breadcrum = [
            'module' => 'Setting',
            'route-module' => null,
            'sub-module' => 'Approval Flow',
            'route-sub-module' => 'approval_flow.index',
        ];
        return view('approvalflow.index', compact('breadcrum', 'department', 'approvable_model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'department' => [
                    'required',
                    Rule::unique('approval_flows', 'department')
                        ->where(fn(Builder $query) => $query->where(
                            'approvable_model',
                            $request->approvable_model
                        )),
                ],
                'approvable_model' => ['required'],
            ]);
            $data = array_merge($request->except(
                '_token',
                '_method',
                'approver_id',
                'action',
                'user_id',
                'slc_action',
                'txt_order',
                'username',
                'order'
            ));
            $approval_flow = Approval_flow::create($data);
            if ($request->user_id) {
                foreach ($request->user_id as $key => $user_id) {
                    $detail[] = [
                        'approval_flow_id' => $approval_flow->id,
                        'user_id' => $user_id,
                        'action' => $request->action[$key],
                        'order' => $request->order[$key],
                    ];
                }
                $approval_flow->approval_step()->createMany($detail);
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
    public function show(Approval_flow $approval_flow)
    {
        $approval_step = Approval_step::where('approval_flow_id', $approval_flow->id)->orderBy('order')->get();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $approval_flow,
            'approval_step' => $approval_step
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Approval_flow $approval_flow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Approval_flow $approval_flow)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'department' => [
                    'required',
                    Rule::unique('approval_flows', 'department')
                        ->where(fn(Builder $query) => $query->where(
                            'approvable_model',
                            $request->approvable_model
                        ))
                        ->ignore($approval_flow->id),
                ],
                'approvable_model' => ['required'],
            ]);
            $data = array_merge($request->except(
                '_token',
                '_method',
                'approver_id',
                'action',
                'user_id',
                'slc_action',
                'txt_order',
                'username',
                'order'
            ));
            $approval_flow->update($data);
            if ($request->user_id) {
                foreach ($request->user_id as $key => $user_id) {
                    $detail[] = [
                        'approval_flow_id' => $approval_flow->id,
                        'user_id' => $request->user_id[$key],
                        'action' => $request->action[$key],
                        'order' => $request->order[$key],
                    ];
                }
                $approval_flow->approval_step()->sync($detail);
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
    public function destroy(Approval_flow $approval_flow)
    {
        DB::beginTransaction();
        try {
            $approval_flow->approval_step()->delete();
            $approval_flow->delete();
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
     * Ngambil data semua data user
     */
    public function get_user_all(Request $request)
    {
        try {
            $user = User::whereNot('username', 'superadmin')->orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil data semua data user
     */
    public function get_step_list(Request $request)
    {
        try {
            $approval_flow = Approval_flow::find($request->approval_flow_id);
            $approval_step = Approval_step::where('approval_flow_id', $request->approval_flow_id)->orderBy('order')->get();
            $view = 'approvalflow.step-list';
            return response()->view($view, compact('approval_flow', 'approval_step'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
