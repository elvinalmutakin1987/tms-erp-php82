<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function get_unit_operation(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $unit_truck = ['Light Truck', 'Crane Truck', 'Prime Truck', 'Fuel Truck', 'Water Truck', 'Flat Deck Crane Truck'];
        $unit = Unit::whereIn('type', $unit_truck)
            ->withCount([
                'daily_report as trip_count' => function ($q) use ($date) {
                    $q->whereDate('date', $date);
                },
            ])
            ->orderByDesc('trip_count')
            ->get();
        return response()->json([
            'total' => $unit->count(),
            'data'  => $unit->map(fn($d) => [
                'vehicle_no' => $d->vehicle_no,
                'trip_count' => $d->trip_count,
            ]),
        ]);
    }
}
