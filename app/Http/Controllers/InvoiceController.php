<?php

namespace App\Http\Controllers;

use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Daily_report;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
use App\Models\Purchase_requisition;
use App\Models\Unit;
use App\Models\Unit_target;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Spatie\Permission\Models\Permission;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $invoice = Invoice::query();
            if (request()->status != 'All') {
                $invoice = $invoice->where('status', request()->status);
            }
            if (request()->unit_id != '') {
                $invoice = $invoice->where('unit_id', request()->unit_id);
            }
            if (request()->year != 'All') {
                if (request()->month != 'All') {
                    $invoice = $invoice->where('periode', request()->year . "-" . request()->month);
                } else {
                    $invoice = $invoice->where('periode', 'like', request()->year . '-%');
                }
            }
            $invoice = $invoice->orderBy('id', 'desc')->get();
            $user = Auth::user();
            $permissionNames = [
                'invoice.edit',
                'invoice.update_progress',
                'invoice.delete',
            ];
            $guardName = config('auth.defaults.guard', 'web');
            $existingPermissions = Permission::query()
                ->whereIn('name', $permissionNames)
                ->where('guard_name', $guardName)
                ->pluck('name')
                ->flip();
            $canAccess = function (string $permission) use ($user, $existingPermissions) {
                if ($user->hasRole('superadmin')) {
                    return true;
                }
                if (! $existingPermissions->has($permission)) {
                    return false;
                }
                return $user->hasPermissionTo($permission);
            };
            return DataTables::of($invoice)
                ->addIndexColumn()
                ->addColumn('action', function ($item) use ($canAccess) {
                    $button = '
                        <div class="col">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item exportPdfButton" href="' . route('invoice.export_pdf', $item->id) . '">
                                            Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item printButton" href="' . route('invoice.print', $item->id) . '" target="_blank">
                                            Print
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail" data-id="' . $item->id . '">
                                            Detail
                                        </a>
                                    </li>
                    ';
                    /**
                     * Tombol Edit:
                     * - hanya muncul jika status Draft
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.edit
                     */
                    if (($item->status === 'Draft' && $canAccess('invoice.edit')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formEdit" data-id="' . $item->id . '">
                                    Edit
                                </a>
                            </li>
                        ';
                    }
                    /**
                     * Tombol Update Progress:
                     * - hanya muncul jika status Approved
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.update_progress
                     */
                    if (($item->status === 'Approved' && $canAccess('invoice.update_progress')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item updateButton" href="#" data-bs-toggle="modal" data-bs-target="#formUpdate" data-id="' . $item->id . '">
                                    Update Progress
                                </a>
                            </li>
                        ';
                    }
                    /**
                     * Tombol Delete:
                     * - hanya muncul jika status bukan Done
                     * - hanya untuk superadmin atau user dengan permission proforma_invoice.delete
                     */
                    if (($item->status !== 'Done' && $canAccess('invoice.delete')) || Auth::user()->hasRole('superadmin')) {
                        $button .= '
                            <li>
                                <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">
                                    Delete
                                </a>
                            </li>
                        ';
                    }
                    $button .= '
                                </ul>
                            </div>
                        </div>
                    ';
                    return $button;
                })
                ->addColumn('unit', function ($item) {
                    return $item->unit?->vehicle_no ?? '';
                })
                ->addColumn('proforma_no', function ($item) {
                    return $item->proforma_invoice->proforma_no ?? '';
                })
                ->addColumn('contract_no', function ($item) {
                    return $item->contract->contract_no ?? '';
                })
                ->addColumn('type', function ($item) {
                    return $item->contract->service->type ?? '';
                })
                ->addColumn('periode_', function ($item) {
                    return Carbon::parse($item->periode)->format('F Y') ?? '';
                })
                ->addColumn('price_', function ($item) {
                    return Number::format($item->price, precision: 0) ?? '';
                })
                ->addColumn('penalty_', function ($item) {
                    return Number::format($item->penalty, precision: 0) ?? '';
                })
                ->addColumn('total_', function ($item) {
                    return Number::format($item->total, precision: 0) ?? '';
                })
                ->rawColumns(['action'])
                ->make();
        }
        $contract = Contract::where('status', 'Active')->get();
        $breadcrum = [
            'module' => 'Finance',
            'route-module' => null,
            'sub-module' => 'Invoice',
            'route-sub-module' => 'invoice.index'
        ];
        return view('invoice.index', compact('breadcrum', 'contract'));
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
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
