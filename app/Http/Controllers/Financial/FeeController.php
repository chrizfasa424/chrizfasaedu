<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    protected function feeCategories(): array
    {
        return ['tuition', 'development_levy', 'ict', 'uniform', 'exam', 'pta', 'transport', 'hostel', 'other'];
    }

    public function index(Request $request)
    {
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = (int) $request->integer('per_page', 10);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $fees = FeeStructure::with(['session', 'term', 'schoolClass'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();
        $sessions = AcademicSession::orderByDesc('is_current')->orderByDesc('start_date')->get();
        $terms = AcademicTerm::with('session')->orderByDesc('is_current')->orderBy('name')->get();
        $categories = $this->feeCategories();

        return view('financial.fees.index', compact('fees', 'classes', 'sessions', 'terms', 'categories', 'perPage', 'allowedPerPage'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $sessions = AcademicSession::orderByDesc('is_current')->orderByDesc('start_date')->get();
        $terms = AcademicTerm::with('session')->orderByDesc('is_current')->orderBy('name')->get();
        $categories = $this->feeCategories();

        return view('financial.fees.create', compact('classes', 'sessions', 'terms', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|in:' . implode(',', $this->feeCategories()),
            'amount' => 'required|numeric|min:0',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'nullable|exists:academic_terms,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'integer|exists:classes,id',
            'class_id' => 'nullable|exists:classes,id',
            'is_compulsory' => 'boolean',
            'is_active' => 'boolean',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $validated['is_compulsory'] = $request->boolean('is_compulsory');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['late_fee_amount'] = $request->filled('late_fee_amount') ? (float) $request->input('late_fee_amount') : 0;
        $validated['late_fee_after_days'] = $request->filled('late_fee_after_days') ? (int) $request->input('late_fee_after_days') : 30;

        $classIds = collect($request->input('class_ids', []))
            ->filter(fn ($id) => (string) $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($classIds->isEmpty() && $request->filled('class_id')) {
            $classIds = collect([(int) $request->input('class_id')]);
        }

        $payload = Arr::except($validated, ['class_ids', 'class_id']);
        $createdCount = 0;

        DB::transaction(function () use ($classIds, $payload, &$createdCount) {
            if ($classIds->isEmpty()) {
                FeeStructure::create(array_merge($payload, ['class_id' => null]));
                $createdCount = 1;
                return;
            }

            foreach ($classIds as $classId) {
                FeeStructure::create(array_merge($payload, ['class_id' => (int) $classId]));
                $createdCount++;
            }
        });

        $message = $createdCount > 1
            ? "Fee structures created for {$createdCount} classes."
            : 'Fee structure created.';

        return redirect()->route('financial.fees.index')->with('success', $message);
    }

    public function update(Request $request, FeeStructure $fee)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|in:' . implode(',', $this->feeCategories()),
            'amount' => 'required|numeric|min:0',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'nullable|exists:academic_terms,id',
            'class_id' => 'nullable|exists:classes,id',
            'is_compulsory' => 'boolean',
            'is_active' => 'boolean',
            'due_date' => 'nullable|date',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'late_fee_after_days' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $validated['is_compulsory'] = $request->boolean('is_compulsory');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['late_fee_amount'] = $request->filled('late_fee_amount') ? (float) $request->input('late_fee_amount') : 0;
        $validated['late_fee_after_days'] = $request->filled('late_fee_after_days') ? (int) $request->input('late_fee_after_days') : 30;

        $fee->update($validated);

        return back()->with('success', 'Fee updated.');
    }

    public function destroy(FeeStructure $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee deleted.');
    }
}
