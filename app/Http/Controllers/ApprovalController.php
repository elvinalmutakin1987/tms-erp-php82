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

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            // $approval_step = Approval_step::where('user_id', Auth::user()->id)->pluck('id');
            $approval_step = Approval_step::where('user_id', Auth::user()->id)->get();
            if ($approval_step->isEmpty()) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->addColumn('action', function () {
                        return '';
                    })
                    ->make(true);
            }
            $approval_process = Approval_process::query();
            $apporval_process = $approval_process->whereIn('approval_step_id', $approval_step->pluck('id'));
            $approval_process = $approval_process->orderBy('id', 'desc')->get();
            return DataTables::of($approval_process)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="approve_(\'' . $item->id . '\')">Approved</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="reject_(\'' . $item->id . '\')">Reject</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('number', function ($item) {
                    return "";
                })
                ->addColumn('type', function ($item) {
                    return "";
                })
                ->make();
        }
        $breadcrum = [
            'module' => 'Approval',
            'route-module' => null,
            'sub-module' => '',
            'route-sub-module' => 'approval.index',
        ];
        return view('approval.index', compact('breadcrum'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
