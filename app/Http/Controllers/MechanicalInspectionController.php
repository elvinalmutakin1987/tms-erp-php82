<?php

namespace App\Http\Controllers;

use App\Models\Mechanical_inspection;
use App\Models\Mechanical_inspection_detail;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use CleaniqueCoders\RunningNumber\Generator;
use Illuminate\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Support\Str;

class MechanicalInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $mechanical_inspection = Mechanical_inspection::query();
            if (request()->unit_id != 'All') {
                $mechanical_inspection = $mechanical_inspection->where('unit_id', request()->unit_id);
            }
            if (request()->date_start != '') {
                $mechanical_inspection = $mechanical_inspection->where('date', '>=', request()->date_start);
            }
            if (request()->date_end != '') {
                $mechanical_inspection = $mechanical_inspection->where('date', '<=', request()->date_end);
            }
            $mechanical_inspection = $mechanical_inspection->orderBy('date', 'desc')->get();
            return DataTables::of($mechanical_inspection)
                ->addIndexColumn()
                ->addColumn('action', function ($item) {
                    $button = '
                    <div class="col">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">Action</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item exportPdfButton" href="' . route('mechanicalinspection.export_pdf', $item->id) . '">Export PDF</a>
                                </li>
                                <li>
                                    <a class="dropdown-item printButton" href="' . route('mechanicalinspection.print', $item->id) . '" target="_blank">Print</a>
                                </li>
                               <li>
                                    <a class="dropdown-item detailButton" href="#" data-bs-toggle="modal" data-bs-target="#formDetail"
                                    data-id="' . $item->id . '">Detail</a>
                                </li>';
                    /**
                     * user superadmin dan yang punya akses edit aja yang bisa muncul
                     */
                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('inspection.edit')):
                        $button .= '<li>
                                    <a class="dropdown-item editButton" href="#" data-bs-toggle="modal" data-bs-target="#formModal"
                                    data-id="' . $item->id . '">Edit</a>
                                </li>';
                    endif;

                    /**
                     * user superadmin dan yang punya akses delete aja yang bisa muncul
                     */
                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('inspection.delete')):
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
                    $total_item = $item->mechanical_inspection_detail()->count();
                    $total_broken = $item->mechanical_inspection_detail()->where('check', 1)->count();
                    $total_good = $item->mechanical_inspection_detail()->where('check', 0)->count();
                    $percent = $total_good / $total_item * 100;
                    return $percent;
                })
                ->addColumn('broken', function ($item) {
                    $total_broken = $item->mechanical_inspection_detail()->where('check', 1)->count();
                    return $total_broken;
                })
                ->addColumn('condition', function ($item) {
                    $total_item = $item->mechanical_inspection_detail()->count();
                    $total_good = $item->mechanical_inspection_detail()->where('check', 0)->count();
                    $condition = $total_good / $total_item * 100;
                    return Number::format($condition, precision: 0);
                })
                ->make();
        }
        $inspection_item = config('mechanical-inspection');
        $breadcrum = [
            'module' => 'Equipment',
            'route-module' => null,
            'sub-module' => 'Mechanical Inspection',
            'route-sub-module' => 'mechanicalinspection.index',
        ];
        return view('mechanical_inspection.index', compact('breadcrum', 'inspection_item'));
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
                'inspector' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'check',
                    'remarks',
                    'inspected_by',
                ),
                ['input_method' => 'Web']
            );
            $mechanical_inspection = Mechanical_inspection::create($data);
            foreach ($request->inspection_item as $key => $item) {
                $mechanical_inspection->mechanical_inspection_detail()->updateOrCreate(
                    [
                        'inspection_item' => $item,
                        'inspection_group' => $request->inspection_group[$key],
                    ],
                    [
                        'inspection_group' => $request->inspection_group[$key],
                        'check' => (int) ($request->check[$item][$request->inspection_group[$key]] ?? 0),
                        'remarks' => $request->remarks[$key],
                        'inspected_by' => $request->inspected_by[$key]
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
    public function show(Mechanical_inspection $mechanical_inspection)
    {
        $mechanical_inspection_detail = $mechanical_inspection->mechanical_inspection_detail;
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'data' => $mechanical_inspection,
            'mechanical_inspection_detail' => $mechanical_inspection_detail
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mechanical_inspection $mechanical_inspection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'unit_id' => ['required', 'not_in:All'],
                'date' => 'required',
                'inspector' => 'required',
            ]);
            $data = array_merge(
                $request->except(
                    '_token',
                    '_method',
                    'inspection_group',
                    'inspection_item',
                    'check',
                    'remarks',
                    'inspected_by'
                ),
                ['input_method' => 'Web']
            );
            $lockMechanical_inspection = Mechanical_inspection::where('id', $mechanical_inspection->id)->lockForUpdate()->first();
            $lockMechanical_inspection->lockForUpdate($data);
            foreach ($request->inspection_item as $key => $item) {
                $mechanical_inspection->mechanical_inspection_detail()->updateOrCreate(
                    [
                        'inspection_item' => $item,
                        'inspection_group' => $request->inspection_group[$key],
                    ],
                    [
                        'inspection_group' => $request->inspection_group[$key],
                        'check' => (int) ($request->check[$item][$request->inspection_group[$key]] ?? 0),
                        'remarks' => $request->remarks[$key],
                        'inspected_by' => $request->inspected_by[$key]
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
    public function destroy(Mechanical_inspection $mechanical_inspection)
    {
        DB::beginTransaction();
        try {
            $mechanical_inspection->mechanical_inspection_detail()->delete();
            $mechanical_inspection->delete();
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
     * Ngambil tabel list mechanical inspection nya
     */
    public function get_table_add(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        try {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $view = 'mechanical_inspection.table-add';
            $inspection_item = config('mechanical-inspection');
            $inspection_prev_no = Generator::make()
                ->type('insp')
                ->formatter($presenter)
                ->preview();
            $html = view($view, compact('inspection_item'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'inspection_prev_no' => $inspection_prev_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Ngambil tabel list mechanical inspection nya
     */
    public function get_table_edit(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        try {
            $view = 'mechanical_inspection.table-edit';
            $inspection_item = config('mechanical-inspection');
            $html = view($view, compact('inspection_item', 'mechanical_inspection'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'inspection_no' => $mechanical_inspection->inspection_no
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }


    /**
     * ngambil detail mechanical inspection
     */
    public function get_detail(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        try {
            $inspection_item = config('mechanical-inspection');
            $inspection_detail = $mechanical_inspection->mechanical_inspection_detail;
            $view = 'mechanical_inspection.detail';
            return response()->view($view, compact('mechanical_inspection', 'inspection_detail', 'inspection_item'), 200);
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
    public function print(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        $inspection_item = config('mechanical-inspection');

        $pdf = Pdf::loadView('mechanical_inspection.print', [
            'mechanical_inspection' => $mechanical_inspection,
            'inspection_item' => $inspection_item
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
        $safeFilename = Str::of($mechanical_inspection->inspection_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->stream("report-{$safeFilename}.pdf");
    }

    /**
     * export pdf
     */

    public function export_pdf(Request $request, Mechanical_inspection $mechanical_inspection)
    {
        $inspection_item = config('mechanical-inspection');

        $pdf = Pdf::loadView('mechanical_inspection.print', [
            'mechanical_inspection' => $mechanical_inspection,
            'inspection_item' => $inspection_item
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
        $safeFilename = Str::of($mechanical_inspection->inspection_no)
            ->replace(['/', '\\'], '-')   // ganti 
            ->toString();
        return $pdf->download("report-{$safeFilename}.pdf");
    }
}
