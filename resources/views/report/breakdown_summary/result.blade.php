@php
    use App\Models\Maintenance;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    /* ============================================================
       DEBUG HELPER — deteksi baris error
       Set $__debug = false di production
       ============================================================ */
    $__debug = true;

    $__report = function ($e, $line = null, $label = '') use ($__debug) {
        if (!$__debug) {
            return;
        }
        $lineInfo = $line ? "Blade line ~{$line}" : "Compiled line {$e->getLine()}";
        echo '<div style="background:#fee;color:#900;padding:8px;border:1px solid #900;' .
            'margin:4px 0;font-family:monospace;font-size:12px;text-align:left">' .
            '<b>ERROR</b>' .
            ($label ? " [{$label}]" : '') .
            ': ' .
            e($e->getMessage()) .
            '<br><b>Location:</b> ' .
            $lineInfo .
            '<br><b>Compiled line:</b> ' .
            $e->getLine() .
            '</div>';
    };

    // Nilai default agar UI tidak error jika terjadi kegagalan di blok try
    $weeks = collect();
    $hoursByDate = [];
    $main = collect();
    $totalBreakdown = 0;
    $startDate = null;
    $endDate = null;
    $currentMonth = null;
    $calendarStart = null;
    $calendarEnd = null;
    $totalDays = 0;
@endphp

@php
    $__block_init_line = __LINE__ + 1;
    try {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        $totalDays = (int) $calendarStart->diffInDays($calendarEnd) + 1;
    } catch (\Throwable $e) {
        $__report($e, $__block_init_line, 'Init - Date Generation');
    }
@endphp

@php
    $__block_maintenance_line = __LINE__ + 1;
    try {
        if (isset($unit_id) && $currentMonth) {
            $monthStart = $currentMonth->copy()->startOfDay();
            $monthEnd = $currentMonth->copy()->addMonth()->startOfDay();

            $maintenances = Maintenance::where('unit_id', $unit_id)
                ->whereNotNull('start')
                ->whereNotNull('finish')
                ->where('start', '<', $monthEnd)
                ->where('finish', '>', $monthStart)
                ->get();

            foreach ($maintenances as $maintenance) {
                $maintenanceStart = Carbon::parse($maintenance->start);
                $maintenanceFinish = Carbon::parse($maintenance->finish);

                if ($maintenanceFinish->lte($maintenanceStart)) {
                    continue;
                }

                $rangeStart = $maintenanceStart->gt($monthStart) ? $maintenanceStart->copy() : $monthStart->copy();
                $rangeFinish = $maintenanceFinish->lt($monthEnd) ? $maintenanceFinish->copy() : $monthEnd->copy();

                $cursor = $rangeStart->copy()->startOfDay();

                while ($cursor->lt($rangeFinish)) {
                    $dayStart = $cursor->copy();
                    $dayEnd = $dayStart->copy()->addDay();

                    $partStart = $rangeStart->gt($dayStart) ? $rangeStart->copy() : $dayStart->copy();
                    $partFinish = $rangeFinish->lt($dayEnd) ? $rangeFinish->copy() : $dayEnd->copy();

                    if ($partStart->lt($partFinish)) {
                        $seconds = $partFinish->getTimestamp() - $partStart->getTimestamp();
                        $hours = $seconds / 3600;
                        $dateKey = $dayStart->format('Y-m-d');

                        $hoursByDate[$dateKey] = ($hoursByDate[$dateKey] ?? 0) + $hours;
                    }

                    $cursor = $dayEnd;
                }
            }

            $main = Maintenance::where('unit_id', $unit_id)
                ->whereNotNull('start')
                ->whereNotNull('finish')
                ->whereYear('start', $year)
                ->whereMonth('start', $month)
                ->get();

            $totalMaintenanceHours = array_sum($hoursByDate);
            $totalBreakdown = (int) round($totalMaintenanceHours, 0, PHP_ROUND_HALF_UP);
        }
    } catch (\Throwable $e) {
        $__report($e, $__block_maintenance_line, 'Maintenance - Query & Calculation');
    }
@endphp

@php
    $__block_calendar_line = __LINE__ + 1;
    try {
        if ($calendarStart && $totalDays > 0) {
            for ($i = 0; $i < $totalDays; $i += 7) {
                $week = [];
                for ($j = 0; $j < 7; $j++) {
                    $week[] = $calendarStart->copy()->addDays($i + $j);
                }
                $weeks->push($week);
            }
        }
    } catch (\Throwable $e) {
        $__report($e, $__block_calendar_line, 'Calendar - Grid Generation');
    }
@endphp

<style>
    .calendar-table {
        width: 100%;
        table-layout: fixed;
    }

    .calendar-table th {
        text-align: center;
        vertical-align: middle;
        font-weight: 700;
        padding: 6px;
    }

    .calendar-table td {
        width: 14.285714%;
        height: 50px;
        padding: 5px;
        vertical-align: top;
        position: relative;
    }

    .calendar-date {
        position: absolute;
        top: 4px;
        right: 6px;
        font-size: 10px;
        line-height: 1;
        font-weight: 500;
        color: #0d6efd;
    }

    .calendar-content {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 14px 3px 3px;
    }

    .calendar-text {
        width: 100%;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.2;
        text-align: center;
        word-break: break-word;
    }

    .calendar-day-success {
        background-color: #e8f5e9 !important;
    }

    .calendar-day-danger {
        background-color: #f5c2c7 !important;
    }

    .calendar-day.today .calendar-date {
        font-weight: 700;
        text-decoration: underline;
    }

    .report-info-table {
        width: 100%;
    }

    .report-info-table .label {
        width: 150px;
    }

    .report-info-table .separator {
        width: 10px;
        text-align: center;
    }

    .notes-table {
        width: 100%;
    }

    .notes-table .notes-number {
        width: 30px;
        vertical-align: top;
    }

    .notes-table .notes-date {
        width: 140px;
        vertical-align: top;
    }
</style>

<div class="mb-4">
    <table class="report-info-table">
        <tr>
            <td class="label">Unit</td>
            <td class="separator">:</td>
            <td>{{ $unit?->vehicle_no ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="separator">:</td>
            <td>
                {{ $startDate ? $startDate->format('d F') : '-' }} - {{ $endDate ? $endDate->format('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Total Breakdown</td>
            <td class="separator">:</td>
            <td><strong>{{ $totalBreakdown }} Hours</strong></td>
        </tr>
    </table>
</div>

@isset($unit_id)
    <div class="calendar-container mb-4">
        <div class="text-center mb-3">
            <h4>{{ $currentMonth ? $currentMonth->format('F Y') : '' }}</h4>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered calendar-table mb-0">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeks as $week)
                        @php
                            $hasCurrentMonth = collect($week)->contains(function ($date) use ($currentMonth) {
                                return $currentMonth &&
                                    $date->year === $currentMonth->year &&
                                    $date->month === $currentMonth->month;
                            });
                        @endphp

                        @if ($hasCurrentMonth)
                            <tr>
                                @foreach ($week as $date)
                                    @php
                                        $__block_calendar_cell = __LINE__ + 1;
                                        $isCurrentMonth = false;
                                        $dateKey = '';
                                        $totalHours = 0;
                                        $cellClass = '';

                                        try {
                                            $isCurrentMonth =
                                                $currentMonth &&
                                                $date->year === $currentMonth->year &&
                                                $date->month === $currentMonth->month;
                                            $dateKey = $date->format('Y-m-d');
                                            $totalHours = $hoursByDate[$dateKey] ?? 0;

                                            if ($isCurrentMonth) {
                                                $cellClass =
                                                    $totalHours > 0 ? 'calendar-day-danger' : 'calendar-day-success';
                                            }

                                            if ($isCurrentMonth && $date->isToday()) {
                                                $cellClass .= ' today';
                                            }
                                        } catch (\Throwable $e) {
                                            $__report($e, $__block_calendar_cell, 'Calendar - Cell Render');
                                        }
                                    @endphp

                                    <td data-date="{{ $dateKey }}" class="calendar-day {{ $cellClass }}">
                                        @if ($isCurrentMonth)
                                            <div class="calendar-date">
                                                {{ $date->day }}
                                            </div>
                                            <div class="calendar-content">
                                                <div class="calendar-text">
                                                    @if ($totalHours > 0)
                                                        {{ rtrim(rtrim(number_format($totalHours, 2, '.', ''), '0'), '.') }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-4">
        <div class="mb-1">Notes :</div>

        <table class="notes-table mb-4">
            @forelse ($main as $d)
                @php
                    $__block_notes_line = __LINE__ + 1;
                    $formattedDate = '-';
                    try {
                        if ($d->start) {
                            $formattedDate = Carbon::parse($d->start)->format('d F Y');
                        }
                    } catch (\Throwable $e) {
                        $__report($e, $__block_notes_line, "Notes - Date parse id={$d->id}");
                    }
                @endphp
                <tr>
                    <td class="notes-number">
                        {{ $loop->iteration }}.
                    </td>
                    <td class="notes-date">
                        {{ $formattedDate }},
                    </td>
                    <td>
                        {!! $d->remarks !!}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-muted">
                        Tidak ada notes pada periode ini.
                    </td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="d-md-flex d-grid align-items-center gap-1">
        <a href="{{ route('report.export_pdf', [
            't' => $t,
            'year' => $year,
            'month' => $month,
            'unit_id' => $unit_id,
        ]) }}"
            target="_blank" type="button" class="btn btn-success">
            Export PDF
        </a>

        <a href="{{ route('report.print', [
            't' => $t,
            'year' => $year,
            'month' => $month,
            'unit_id' => $unit_id,
        ]) }}"
            target="_blank" type="button" class="btn btn-primary">
            Print
        </a>
    </div>
@else
    <div class="alert alert-danger mb-0">
        Failed to load data.
    </div>
@endisset
