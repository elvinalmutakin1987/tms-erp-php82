<?php

use Carbon\Carbon;

if (! function_exists('addTime')) {
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
}

if (! function_exists('countTime')) {
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
}

if (! function_exists('countDateTime')) {
    function countDateTime(string $time1, string $time2): string
    {
        $start = Carbon::parse($time1);
        $end = Carbon::parse($time2);

        $totalSeconds = (int) abs($start->diffInSeconds($end));

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
