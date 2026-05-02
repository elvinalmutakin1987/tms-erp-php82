<?php

namespace App\Http\Controllers;

use App\Models\Purchase_requisition;
use App\Models\Request_quotation;
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


class RequestQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $purchase_requisition = Purchase_requisition::query();
            return DataTables::of($purchase_requisition)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('purchaserequisition.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('purchaserequisition.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                                <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    /**
                     * status draft
                     * user superadmin dan yang punya akses edit aja yang bisa muncul
                     */
                    if ($item->status == 'Draft'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaserequisition.edit')):
                            $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                        endif;
                    endif;

                    /**
                     * status approved
                     * buat edit status jadi done. sambil check penerimaan barang
                     */
                    if ($item->status == 'Approved' || $item->status == 'Received'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->id == $item->user_id):
                            $button .= '<li>
                                     <a class="dropdown-item receiveButton" href="#" data-bs-toggle="modal" data-bs-target="#formReceive"
                                    data-id="' . $item->id . '">Receive</a>
                                </li>';
                        endif;
                    endif;

                    /**
                     * status bukan done, bisa di hapus.
                     * user superadmin dan yang punya akses delete aja yang bisa muncul
                     */
                    if ($item->status != 'Done' && $item->status != 'Approved' && $item->status != 'Approval' && $item->status != 'Received'):
                        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('purchaserequisition.delete')):
                            $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>';
                        endif;
                    endif;

                    $button .= '</ul>
                        </div>
                    </div>
                    ';

                    return $button;
                })
                ->make();
        }
        $uom = config('uom');
        $system_setting = config('system_setting');
        $breadcrum = [
            'module' => 'Purchase Order',
            'route-module' => null,
            'sub-module' => 'Request Quoation',
            'route-sub-module' => 'requestquotation.index',
        ];
        return view('request_quotation.index', compact('breadcrum', 'uom', 'system_setting'));
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
    public function show(Request_quotation $request_quotation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request_quotation $request_quotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Request_quotation $request_quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request_quotation $request_quotation)
    {
        //
    }
}
