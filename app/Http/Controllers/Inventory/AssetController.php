<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->paginate(20);
        return view('inventory.index', compact('assets'));
    }

    public function store(Request $request)
    {
        Asset::create($request->validate([
            'name' => 'required|string',
            'asset_code' => 'nullable|string',
            'category' => 'nullable|string',
            'location' => 'nullable|string',
            'quantity' => 'integer|min:1',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'numeric|min:0',
            'supplier' => 'nullable|string',
            'condition' => 'in:new,good,fair,poor,damaged',
        ]));
        return back()->with('success', 'Asset recorded.');
    }
}
