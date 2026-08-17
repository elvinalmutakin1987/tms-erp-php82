@php
    use App\Models\Maintenance;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    // Bulan dan tahun yang sedang dipilih
    $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();

    // Awal kalender dimulai dari hari Minggu
    $calendarStart = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);

    // Akhir kalender sampai hari Sabtu
    $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

    // Total hari yang akan ditampilkan
    $totalDays = $calendarStart->diffInDays($calendarEnd) + 1;

    /*
    |--------------------------------------------------------------------------
    | Hitung Maintenance per tanggal
    |--------------------------------------------------------------------------
    */

    $hoursByDate = [];

    if (isset($unit_id)) {
        // Awal bulan
        $monthStart = $currentMonth->copy()->startOfDay();

        // Akhir bulan secara exclusive
        // Contoh Agustus => 1 September 00:00:00
        $monthEnd = $currentMonth->copy()->addMonth()->startOfDay();

        /*
         * Ambil maintenance yang bersinggungan
         * dengan bulan yang sedang ditampilkan
         */
        $maintenances = Maintenance::where('unit_id', $unit_id)
            ->whereNotNull('start')
            ->whereNotNull('finish')
            ->where('start', '<', $monthEnd)
            ->where('finish', '>', $monthStart)
            ->get();

        foreach ($maintenances as $maintenance) {
            $maintenanceStart = Carbon::parse($maintenance->start);
            $maintenanceFinish = Carbon::parse($maintenance->finish);

            // Skip kalau finish <= start
            if ($maintenanceFinish->lte($maintenanceStart)) {
                continue;
            }

            /*
             * Batasi interval hanya ke bulan
             * yang sedang ditampilkan
             */
            $rangeStart = $maintenanceStart->gt($monthStart) ? $maintenanceStart->copy() : $monthStart->copy();

            $rangeFinish = $maintenanceFinish->lt($monthEnd) ? $maintenanceFinish->copy() : $monthEnd->copy();

            /*
             * Mulai perhitungan dari tanggal pertama
             */
            $cursor = $rangeStart->copy()->startOfDay();

            while ($cursor->lt($rangeFinish)) {
                $dayStart = $cursor->copy();
                $dayEnd = $dayStart->copy()->addDay();

                /*
                 * Start pada tanggal tersebut
                 */
                $partStart = $rangeStart->gt($dayStart) ? $rangeStart->copy() : $dayStart->copy();

                /*
                 * Finish pada tanggal tersebut
                 */
                $partFinish = $rangeFinish->lt($dayEnd) ? $rangeFinish->copy() : $dayEnd->copy();

                if ($partStart->lt($partFinish)) {
                    $seconds = $partFinish->getTimestamp() - $partStart->getTimestamp();

                    $hours = $seconds / 3600;

                    $dateKey = $dayStart->format('Y-m-d');

                    /*
                     * Kalau ada lebih dari 1 maintenance
                     * pada tanggal yang sama, jumlahkan
                     */
                    $hoursByDate[$dateKey] = ($hoursByDate[$dateKey] ?? 0) + $hours;
                }

                $cursor = $dayEnd;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Total Breakdown
    |--------------------------------------------------------------------------
    */

    // Total seluruh jam maintenance
    $totalMaintenanceHours = array_sum($hoursByDate);

    // Pembulatan seperti Excel:
    // =ROUND(total_hours, 0)
    $totalBreakdown = (int) round($totalMaintenanceHours, 0, PHP_ROUND_HALF_UP);
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

    /*
    |--------------------------------------------------------------------------
    | Nomor tanggal
    |--------------------------------------------------------------------------
    */

    .calendar-date {
        position: absolute;
        top: 4px;
        right: 6px;

        font-size: 10px;
        line-height: 1;
        font-weight: 500;
        color: #0d6efd;
    }

    /*
    |--------------------------------------------------------------------------
    | Isi utama
    |--------------------------------------------------------------------------
    */

    .calendar-content {
        width: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 14px 3px 3px 3px;
    }

    .calendar-text {
        width: 100%;

        font-size: 18px;
        font-weight: 600;
        line-height: 1.2;

        text-align: center;

        word-break: break-word;
    }

    /*
    |--------------------------------------------------------------------------
    | Warna berdasarkan Maintenance
    |--------------------------------------------------------------------------
    */

    /* Tidak ada maintenance */
    .calendar-day-success {
        background-color: #e8f5e9 !important;
    }

    /* Ada maintenance */
    .calendar-day-danger {
        background-color: #f5c2c7 !important;
    }

    /*
    |--------------------------------------------------------------------------
    | Hari ini
    |--------------------------------------------------------------------------
    */

    .calendar-day.today .calendar-date {
        font-weight: 700;
        text-decoration: underline;
    }
</style>


<h6 class="mb-4" style="display: inline-block;">
    <table style="width:100%">

        <tr>
            <td style="width:150px">
                Unit
            </td>

            <td style="width:5px">
                :
            </td>

            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $unit?->vehicle_no ?? '' }}
            </td>
        </tr>


        <tr>
            <td style="width:150px">
                Periode
            </td>

            <td style="width:5px">
                :
            </td>

            <td>
                &nbsp;&nbsp;&nbsp;

                {{ $startDate->format('d F') }}

                -

                {{ $endDate->format('d F Y') }}
            </td>
        </tr>


        <tr>
            <td style="width:150px">
                Total Breakdown
            </td>

            <td style="width:5px">
                :
            </td>

            <td>
                &nbsp;&nbsp;&nbsp;

                <strong>
                    {{ $totalBreakdown }} Hours
                </strong>
            </td>
        </tr>

    </table>
</h6>



@isset($unit_id)

    <div class="calendar-container mb-4">

        <div class="text-center mb-3">
            <h4>
                {{ $currentMonth->format('F Y') }}
            </h4>
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

                    @php
                        $weeks = collect();

                        for ($i = 0; $i < $totalDays; $i += 7) {
                            $week = [];

                            for ($j = 0; $j < 7; $j++) {
                                $week[] = $calendarStart->copy()->addDays($i + $j);
                            }

                            $weeks->push($week);
                        }
                    @endphp


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

                                        /*
                                         * Apakah tanggal masuk bulan aktif
                                         */
                                        $isCurrentMonth =
                                            $date->year === $currentMonth->year &&
                                            $date->month === $currentMonth->month;

                                        /*
                                         * Key tanggal
                                         */
                                        $dateKey = $date->format('Y-m-d');

                                        /*
                                         * Ambil total jam maintenance
                                         */
                                        $totalHours = $hoursByDate[$dateKey] ?? 0;

                                        /*
                                         * Tentukan warna cell
                                         */
                                        $cellClass = '';

                                        if ($isCurrentMonth) {
                                            $cellClass =
                                                $totalHours > 0 ? 'calendar-day-danger' : 'calendar-day-success';
                                        }

                                        /*
                                         * Tambahkan class today
                                         */
                                        if ($isCurrentMonth && $date->isToday()) {
                                            $cellClass .= ' today';
                                        }

                                    @endphp


                                    <td data-date="{{ $dateKey }}" class="calendar-day {{ $cellClass }}">

                                        @if ($isCurrentMonth)
                                            {{-- Nomor tanggal --}}
                                            <div class="calendar-date">
                                                {{ $date->day }}
                                            </div>


                                            {{-- Isi utama --}}
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

    <div class="md-4">
        @php
            $main = Maintenance::where('unit_id', $unit_id)
                ->whereNotNull('start')
                ->whereNotNull('finish')
                ->whereYear('start', $year)
                ->whereMonth('start', $month)
                ->get();
        @endphp
        Notes : <br>
        <table style="width: 100%" class="mb-4">
            @foreach ($main as $d)
                <tr>
                    <td width="30px">{{ $loop->iteration }} .</td>
                    <td width="120px">{{ Carbon::parse($d->start)->format('d F Y') }} ,</td>
                    <td>{!! $d->remarks !!}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="d-md-flex d-grid align-items-center gap-1">
        <a href="{{ route('report.export_pdf', [
            't' => $t,
            'year' => $year,
            'month' => $month,
            'unit_id' => $unit_id,
        ]) }}"
            target="_blank" type="button" class="btn btn-success">Export
            PDF</a>
        <a href="{{ route('report.print', [
            't' => $t,
            'year' => $year,
            'month' => $month,
            'unit_id' => $unit_id,
        ]) }}"
            target="_blank" type="button" class="btn btn-primary ">Print</a>
    </div>
@else
    <div class="alert alert-danger mb-0">
        Failed to load data.
    </div>

@endisset
