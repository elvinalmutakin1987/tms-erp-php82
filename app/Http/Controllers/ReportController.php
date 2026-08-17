<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Unit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use Imagick;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $t =  $request->query('t');
        $sub_module = "Unit Breakdown Summary";
        if ($t == 'breakdown-summary') {
            $sub_module = "Unit Breakdown Summary";
        } elseif ($t == 'invoice-monitoring') {
            $sub_module = "Invoice Monitoring";
        } elseif ($t == 'po-monitoring') {
            $sub_module = "PO Monitoring";
        }
        $breadcrum = [
            'module' => 'Report',
            'route-module' => null,
            'sub-module' => $sub_module,
            'route-sub-module' => 'report.index',
        ];
        return view('report', compact('breadcrum', 't'));
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


    /**
     * Buat lihat hasilnya
     */
    public function get_result(Request $request)
    {
        $compact = [];
        $year = $request->year;
        $month = $request->month;
        $view = 'report.breakdown_summary.result';
        $t = $request->t;
        if ($request->t == 'breakdown-summary') {
            $unit_id = $request->unit_id;
            $unit = Unit::find($unit_id);
            $compact = compact(
                'year',
                'month',
                'unit',
                'unit_id',
                't'
            );
            $view = 'report.breakdown_summary.result';
        } elseif ($request->t == 'invoice-monitoring') {
            $compact = compact(
                'year',
                'month',
                't'
            );
            $view = 'report.invoice_monitoring.result';
        } elseif ($request->t == 'po-monitoring') {
            $compact = compact(
                'year',
                'month',
                't'
            );
            $view = 'report.po_monitoring.result';
        }
        $html = view($view, $compact)->render();
        return response()->json([
            'success' => true,
            'message' => 'Data showed',
            'html' => $html
        ], 200);
    }


    /**
     * Buat print
     */
    public function print(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $t = $request->t;
        $view = 'report.breakdown_summary.print';
        $compact = [];
        $display = 'potrait';
        if ($t == 'breakdown-summary') {
            $view = 'report.breakdown_summary.print';
            $unit_id = $request->unit_id;
            $unit = Unit::find($unit_id);
            $compact = [
                'year' => $year,
                'month' => $month,
                'unit_id' => $unit_id,
                'unit' => $unit,
                't' => $t
            ];
        } elseif ($t == 'invoice-monitoring') {
            $view = 'report.invoice_monitoring.print';
            $compact = [
                'year' => $year,
                'month' => $month,
                't' => $t
            ];
            $display = 'landscape';
        } elseif ($t == 'po-monitoring') {
            $compact = [
                'year' => $year,
                'month' => $month,
                't' => $t
            ];
            $view = 'report.po_monitoring.print';
            $display = 'landscape';
        }

        $pdf = Pdf::loadView($view, $compact)->setPaper('a4', $display);

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        if ($t == 'breakdown-summary') {
            $qrText =
                'PT. Tunas Mitra Sejati' .
                "\n" .
                "\n" .
                'Breakdown Summary ' .
                "\n" .
                'Unit : ' .
                $unit->vehicle_no .
                "\n" .
                'Periode : ' .
                $startDate->format('d F') .
                ' - ' .
                $endDate->format('d F Y');
            $qrImage = QrCode::format('png')
                ->size(150)
                ->margin(1)
                ->generate($qrText);

            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);

            // Posisi QR Code di atas page number
            $qrSize = 55;
            $qrX = $width - 120;
            $qrY = $height - 100;

            $canvas->image(
                $qrBase64,
                $qrX,
                $qrY,
                $qrSize,
                $qrSize
            );
        }

        $safeFilename = "report_" . $t;
        return $pdf->stream("{$safeFilename}.pdf");
    }

    /**
     * Buat export pdf nya
     */
    public function export_pdf(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $t = $request->t;
        $view = 'report.breakdown_summary.print';
        $compact = [];
        if ($t == 'breakdown-summary') {
            $view = 'report.breakdown_summary.print';
            $unit_id = $request->unit_id;
            $unit = Unit::find($unit_id);
            $compact = [
                'year' => $year,
                'month' => $month,
                'unit_id' => $unit_id,
                'unit' => $unit,
                't' => $t
            ];
        } elseif ($t == 'invoice-monitoring') {
            $view = 'report.invoice_monitoring.print';
        } elseif ($t == 'po-monitoring') {
            $view = 'report.po_monitoring.print';
        }

        $pdf = Pdf::loadView($view, $compact)->setPaper('a4', 'portrait');

        // WAJIB: render dulu
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Ambil canvas + font
        $canvas = $dompdf->getCanvas(); // kalau error, ganti jadi: $dompdf->get_canvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('Helvetica', 'normal');

        $width  = $canvas->get_width();
        $height = $canvas->get_height();

        $qrText =
            'PT. Tunas Mitra Sejati' .
            "\n" .
            "\n" .
            'Breakdown Summary ' .
            "\n" .
            'Unit : ' .
            $unit->vehicle_no .
            "\n" .
            'Periode : ' .
            $startDate->format('d F') .
            ' - ' .
            $endDate->format('d F Y');
        $qrImage = QrCode::format('png')
            ->size(150)
            ->margin(1)
            ->generate($qrText);

        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);

        // Posisi QR Code di atas page number
        $qrSize = 55;
        $qrX = $width - 120;
        $qrY = $height - 100;

        $canvas->image(
            $qrBase64,
            $qrX,
            $qrY,
            $qrSize,
            $qrSize
        );
        $safeFilename = "report_" . $t;
        return $pdf->download("{$safeFilename}.pdf");
    }

    /**
     * Untuk export excel
     */
    public function export(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $t = $request->t;
        $unit_id = $request->unit_id ?? null;
        return Excel::download(new ReportExport($year, $month, $t, $unit_id), 'report_' . $t . '_' . date('YmdHis') . '.xlsx');
    }

    public function export_image(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $t = $request->t;

        $periode = Carbon::create($year, $month, 1)->format('Y-m');

        $directory = storage_path('app/temp');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdfPath = $directory . '/invoice-monitoring-' . $periode . '.pdf';
        $pngPath = $directory . '/invoice-monitoring-' . $periode . '.png';

        $pdf = Pdf::loadView(
            'report.invoice_monitoring.export_image',
            [
                'year' => $year,
                'month' => $month,
                't' => $t,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PAPER LANDSCAPE
        |--------------------------------------------------------------------------
        | Intinya: width harus lebih besar dari height
        | jangan cuma mengandalkan string 'landscape'
        */

        $pdf->setPaper([0, 0, 4200, 1800]);

        $pdf->save($pdfPath);

        /*
        |--------------------------------------------------------------------------
        | Convert PDF -> PNG
        |--------------------------------------------------------------------------
        */

        $image = new Imagick();
        $image->setResolution(200, 200);
        $image->readImage($pdfPath . '[0]');
        $image->setImageBackgroundColor('white');
        $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

        /*
        |--------------------------------------------------------------------------
        | Paksa landscape kalau ternyata masih portrait
        |--------------------------------------------------------------------------
        */

        if ($image->getImageHeight() > $image->getImageWidth()) {
            $image->rotateImage('white', 90);
        }

        $image->setImageFormat('png');
        $image->setImageCompressionQuality(100);
        $image->writeImage($pngPath);

        $image->clear();
        $image->destroy();

        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        return response()
            ->download(
                $pngPath,
                'invoice-monitoring-' . $periode . '.png',
                ['Content-Type' => 'image/png']
            )
            ->deleteFileAfterSend(true);
    }
}
