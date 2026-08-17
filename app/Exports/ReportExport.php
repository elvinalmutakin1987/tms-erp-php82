<?php

namespace App\Exports;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExport implements FromView, WithEvents
{
    protected $year;
    protected $month;
    protected $t;
    protected $unit_id;

    public function __construct($year, $month, $t, $unit_id = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->t = $t;
        $this->unit_id = $unit_id;
    }

    public function view(): View
    {
        $view = "report.breakdown_summary.export";
        $compact = [];
        if ($this->t == 'breakdown-summary') {
            $unit = Unit::find($this->unit_id);
            $view = "report.breakdown_summary.export";
            $compact = [
                'year' => $this->year,
                'month' => $this->month,
                't' => $this->t,
                'unit_id' => $this->unit_id,
                'unit' => $unit
            ];
        } elseif ($this->t == 'invoice-monitoring') {
            $view = "report.invoice_monitoring.export";
            $compact = [
                'year' => $this->year,
                'month' => $this->month
            ];
        } elseif ($this->t == 'po-monitoring') {
            $view = "report.po_monitoring.export";
            $compact = [
                'year' => $this->year,
                'month' => $this->month
            ];
        }
        return view($view, $compact);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setShowGridlines(false);
            },
        ];
    }
}
