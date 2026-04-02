<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $fees = FeeStructure::with(['session', 'term', 'schoolClass'])->latest()->paginate(20);
        return view('financial.fees.index', compact('fees'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('order')->get();
        return view('financial.fees.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|in:tuition,development_levy,ict,uniform,exam,pta,transport,hostel,other',
            'amount' => 'required|numeric|min:0',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'nullable|exists:academic_terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'is_compulsory' => 'boolean',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        FeeStructure::create($validated);

        return redirect()->route('financial.fees.index')->with('success', 'Fee structure created.');
    }

    public function update(Request $request, FeeStructure $fee)
    {
        $fee->update($request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'is_compulsory' => 'boolean',
            'due_date' => 'nullable|date',
        ]));

        return back()->with('success', 'Fee updated.');
    }

    public function destroy(FeeStructure $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee deleted.');
    }
}
