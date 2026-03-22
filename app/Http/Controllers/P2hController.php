<?php

namespace App\Http\Controllers;

use App\Models\P2h;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class P2hController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $p2h = P2h::query();
            if (request()->unit_id != 'All') {
                $p2h = $p2h->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $p2h = $p2h->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $p2h = $p2h->where('date', '<=', request()->date_end);
            }
            $p2h = $p2h->orderBy('date', 'desc')->get();
            return DataTables::of($p2h)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('p2h.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('p2h.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                                <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>
                                <li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('p2h.delete')):
                        $button .= '<li>
                                    <a class="dropdown-item" href="#" onclick="delete_(\'' . $item->id . '\')">Delete</a>
                                </li>';
                    endif;
                    $button .= '</ul>
                        </div>
                    </div>
                    ';
                    return $button;
                })
                ->addColumn('unit', function ($item) {
                    $unit = Unit::find($item->unit_id);
                    return $unit->vehicle_no;
                })
                ->addColumn('result', function ($item) {
                    $total_item = $item->p2h_detail()->count();
                    $total_broken = $item->p2h_detail()->where('check', 1)->count();
                    $total_good = $item->p2h_detail()->where('check', 0)->count();
                    $percent = $total_good / $total_item * 100;
                    return $percent;
                })
                ->addColumn('broken', function ($item) {
                    $total_broken = $item->p2h_detail()->where('check', 1)->count();
                    return $total_broken;
                })
                ->addColumn('condition', function ($item) {
                    $total_item = $item->p2h_detail()->count();
                    $total_good = $item->p2h_detail()->where('check', 0)->count();
                    $condition = $total_good / $total_item * 100;
                    return Number::format($condition, precision: 0);
                })
                ->make();
        }
        $p2hitem = config('p2hitem');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'P2h',
            'route-sub-module' => 'p2h.index',
        ];
        return view('p2h.index', compact('breadcrum', 'p2hitem'));
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
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
                'driver' => 'required',
                'km_start' => 'required',
                'km_finish' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'check',
                    'defect_listed',
                    'action_taken'
                ),
                ['input_method' => 'Web']
            );
            $p2h = P2h::create($data);
            foreach ($request->inspection_item as $i => $item) {
                $p2h->p2h_detail()->updateOrCreate(
                    [
                        'inspection_item' => $item,
                        'inspection_group' => $request->inspection_group[$i]
                    ],
                    [
                        'inspection_group' => $request->inspection_group[$i],
                        'check' => (int) ($request->check[$item][$request->inspection_group[$i]] ?? 0),
                        'defect_listed' => $request->defect_listed[$i],
                        'action_taken' => $request->action_taken[$i]
                    ]
                );
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
    public function show(P2h $p2h)
    {
        $p2h_detail = $p2h->p2h_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $p2h,
            'p2h_detail' => $p2h_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(P2h $p2h)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, P2h $p2h)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
                'driver' => 'required',
                'km_start' => 'required',
                'km_finish' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'check',
                    'defect_listed',
                    'action_taken',
                ),
                ['input_method' => 'Web']
            );
            $p2h->update($data);
            foreach ($request->inspection_item as $key => $item) {
                $p2h->p2h_detail()->updateOrCreate(
                    [
                        'inspection_item' => $item,
                        'inspection_group' => $request->inspection_group[$key]
                    ],
                    [
                        'inspection_group' => $request->inspection_group[$key],
                        'check' => (int) ($request->check[$item][$request->inspection_group[$key]] ?? 0),
                        'defect_listed' => $request->defect_listed[$key],
                        'action_taken' => $request->action_taken[$key]
                    ]
                );
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
     * Remove the specified resource from storage.
     */
    public function destroy(P2h $p2h)
    {
        DB::beginTransaction();
        try {
            $p2h->p2h_detail()->delete();
            $p2h->delete();
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

    /**
     * Ngambil tabel list p2h nya
     */
    public function get_table_add(Request $request, P2h $p2h)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $view = 'p2h.table-add';
            $p2hitem = config('p2hitem');
            $p2h_prev_no = Generator::make()
                ->type('p2h')
                ->formatter($presenter)
                ->preview();
            $html = view($view, compact('p2hitem'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'p2h_prev_no' => $p2h_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list p2h nya
     */
    public function get_table_edit(Request $request, P2h $p2h)
    {
        try {
            $view = 'p2h.table-edit';
            $unit = Unit::find($p2h->unit_id);
            $p2hitem = config('p2hitem');
            if ($unit->type == 'Light') {
                $p2hitem = config('p2hitem');
            } elseif ($unit->type == 'Fuel Truck') {
                $p2hitem = config('p2hitem-fuel');
            } else {
                $p2hitem = config('p2hitem-light');
            }
            $html = view($view, compact('p2hitem', 'p2h'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'p2h_no' => $p2h->p2h_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngambil detail p2h
     */
    public function get_detail(Request $request, P2h $p2h)
    {
        try {
            $p2hitem = config('p2hitem');
            $p2h_detail = $p2h->p2h_detail;
            $view = 'p2h.detail';
            return response()->view($view, compact('p2h', 'p2h_detail', 'p2hitem'), 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * ngeprint
     */
    public function print(Request $request, P2h $p2h)
    {
        $unit = Unit::find($p2h->unit_id);
        $p2hitem = config('p2hitem');
        if ($unit->type == 'Light') {
            $p2hitem = config('p2hitem');
        } elseif ($unit->type == 'Fuel Truck') {
            $p2hitem = config('p2hitem-fuel');
        } else {
            $p2hitem = config('p2hitem-light');
        }

        $pdf = Pdf::loadView('p2h.print', [
            'p2h' => $p2h,
            'p2hitem' => $p2hitem
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            255, // X (geser kiri/kanan kalau perlu)
            58,  // Y (geser atas/bawah kalau perlu)
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $font,
            10,
            [0, 0, 0]
        );
        $safeFilename = Str::of($p2h->p2h_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, P2h $p2h)
    {
        $unit = Unit::find($p2h->unit_id);
        $p2hitem = config('p2hitem');
        if ($unit->type == 'Light') {
            $p2hitem = config('p2hitem');
        } elseif ($unit->type == 'Fuel Truck') {
            $p2hitem = config('p2hitem-fuel');
        } else {
            $p2hitem = config('p2hitem-light');
        }

        $pdf = Pdf::loadView('p2h.print', [
            'p2h' => $p2h,
            'p2hitem' => $p2hitem
        ])->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        // Tulis nomor halaman ke semua halaman
        $canvas->page_text(
            255, // X (geser kiri/kanan kalau perlu)
            58,  // Y (geser atas/bawah kalau perlu)
            "Page {PAGE_NUM} of {PAGE_COUNT}",
            $font,
            10,
            [0, 0, 0]
        );
        $safeFilename = Str::of($p2h->p2h_no)
            ->replace(['/', '\\'], '-')   // ganti slash
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }

    /**
     * Ngambil item p2h nya berdasarkan tipe unit
     */
    public function get_p2h_item(Request $request)
    {
        try {
            $unit = Unit::find($request->unit_id);
            if ($unit->type == 'Light') {
                $item = config('p2hitem');
            } elseif ($unit->type == 'Fuel Truck') {
                $item = config('p2hitem-fuel');
            } else {
                $item = config('p2hitem-light');
            }
            $view = 'p2h.table-item';
            $html = view($view, compact('item'))->render();
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
}
