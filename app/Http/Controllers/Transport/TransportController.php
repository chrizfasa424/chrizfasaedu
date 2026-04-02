<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $routes = TransportRoute::with(['driver.user', 'students'])->paginate(15);
        return view('transport.index', compact('routes'));
    }

    public function store(Request $request)
    {
        TransportRoute::create($request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'pickup_points' => 'nullable|array',
            'driver_id' => 'nullable|exists:staff,id',
            'vehicle_number' => 'nullable|string',
            'capacity' => 'integer|min:1',
            'fee_amount' => 'numeric|min:0',
        ]));
        return back()->with('success', 'Route created.');
    }
}
