<?php

namespace App\Exports;

use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet; // Ganti dari BeforeSheet ke AfterSheet

class UnitsExport implements FromView, WithEvents
{
    public function view(): View
    {
        return view('unit_expired.export', [
            'unit' => Unit::all()
        ]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            // Gunakan AfterSheet agar pengaturan diterapkan setelah sheet dibuat
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setShowGridlines(false);
            },
        ];
    }
}
