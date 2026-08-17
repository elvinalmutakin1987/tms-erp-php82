@php
    use App\Models\Maintenance;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();
    $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();
    $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
    $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
    $totalDays = (int) $calendarStart->diffInDays($calendarEnd) + 1;

    $hoursByDate = [];
    $main = collect();

    if (isset($unit_id)) {
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
    }

    $totalMaintenanceHours = array_sum($hoursByDate);
    $totalBreakdown = (int) round($totalMaintenanceHours, 0, PHP_ROUND_HALF_UP);

    $weeks = collect();

    for ($i = 0; $i < $totalDays; $i += 7) {
        $week = [];

        for ($j = 0; $j < 7; $j++) {
            $week[] = $calendarStart->copy()->addDays($i + $j);
        }

        $weeks->push($week);
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
            <td>{{ $startDate->format('d F') }} - {{ $endDate->format('d F Y') }}</td>
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
            <h4>{{ $currentMonth->format('F Y') }}</h4>
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
                                return $date->year === $currentMonth->year && $date->month === $currentMonth->month;
                            });
                        @endphp

                        @if ($hasCurrentMonth)
                            <tr>
                                @foreach ($week as $date)
                                    @php
                                        $isCurrentMonth =
                                            $date->year === $currentMonth->year &&
                                            $date->month === $currentMonth->month;

                                        $dateKey = $date->format('Y-m-d');
                                        $totalHours = $hoursByDate[$dateKey] ?? 0;
                                        $cellClass = '';

                                        if ($isCurrentMonth) {
                                            $cellClass =
                                                $totalHours > 0 ? 'calendar-day-danger' : 'calendar-day-success';
                                        }

                                        if ($isCurrentMonth && $date->isToday()) {
                                            $cellClass .= ' today';
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
                <tr>
                    <td class="notes-number">
                        {{ $loop->iteration }}.
                    </td>
                    <td class="notes-date">
                        {{ Carbon::parse($d->start)->format('d F Y') }},
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
