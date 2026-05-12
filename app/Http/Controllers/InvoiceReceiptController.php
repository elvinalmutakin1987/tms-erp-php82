<?php

namespace App\Http\Controllers;

use App\Models\Purchase_order;
use App\Models\Approval_flow;
use App\Models\Approval_process;
use App\Models\Approval_status;
use App\Models\Approval_step;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_order = Purchase_order::where('payment_status', 'Waiting Invoice')
                ->orderBy('id', 'desc')
                ->get();
            return DataTables::of($purchase_order)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Receive Invoice</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $job = config('job');
        $breadcrum = [
            'module' => 'Procurement',
            'route-module' => null,
            'sub-module' => 'Purchase Order',
            'route-sub-module' => 'purchaseorder.index',
        ];
        return view('purchase_order.index', compact('breadcrum', 'uom', 'system_setting', 'job'));
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
    public function show(Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase_order $purchase_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase_order $purchase_order)
    {
        //
    }
}
