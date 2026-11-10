<?php

namespace App\Services;

use Carbon\Carbon;

class TimeCountService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    function addTime(string $time1, string $time2): string
    {
        [$hour1, $minute1] = array_map('intval', explode(':', $time1));
        [$hour2, $minute2] = array_map('intval', explode(':', $time2));

        $totalMinute1 = ($hour1 * 60) + $minute1;
        $totalMinute2 = ($hour2 * 60) + $minute2;

        $totalMinute = ($totalMinute1 + $totalMinute2) % (24 * 60);

        $hour = intdiv($totalMinute, 60);
        $minute = $totalMinute % 60;

        return sprintf('%02d:%02d', $hour, $minute);
    }

    function countTime(string $time1, string $time2): string
    {
        [$hour1, $minute1] = array_map('intval', explode(':', $time1));
        [$hour2, $minute2] = array_map('intval', explode(':', $time2));

        $totalMinute1 = ($hour1 * 60) + $minute1;
        $totalMinute2 = ($hour2 * 60) + $minute2;

        $totalMinute = ($totalMinute2 - $totalMinute1) % (24 * 60);

        $hour = intdiv($totalMinute, 60);
        $minute = $totalMinute % 60;

        return sprintf('%02d:%02d', $hour, $minute);
    }

    function countDateTime(string $time1, string $time2): string
    {
        // Normalisasi format waktu (16.30 menjadi 16:30)
        $time1 = str_replace('.', ':', $time1);
        $time2 = str_replace('.', ':', $time2);

        // Parse waktu
        $start = Carbon::createFromFormat('!H:i', $time1);
        $end = Carbon::createFromFormat('!H:i', $time2);

        // Jika waktu akhir lebih kecil, berarti melewati tengah malam
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        // Hitung selisih detik
        $totalSeconds = $end->timestamp - $start->timestamp;

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
