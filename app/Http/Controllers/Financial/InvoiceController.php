<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'session', 'term']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $invoices = $query->latest()->paginate(25);
        return view('financial.invoices.index', compact('invoices'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:academic_terms,id',
        ]);

        $students = Student::active()->inClass($validated['class_id'])->get();
        $fees = FeeStructure::where('session_id', $validated['session_id'])
            ->where(function ($q) use ($validated) {
                $q->where('class_id', $validated['class_id'])->orWhereNull('class_id');
            })
            ->where('is_active', true)
            ->get();

        $count = 0;
        DB::transaction(function () use ($students, $fees, $validated, &$count) {
            foreach ($students as $student) {
                $existing = Invoice::where('student_id', $student->id)
                    ->where('session_id', $validated['session_id'])
                    ->where('term_id', $validated['term_id'])
                    ->exists();

                if ($existing) continue;

                $totalAmount = $fees->sum('amount');

                $invoice = Invoice::create([
                    'school_id' => auth()->user()->school_id,
                    'student_id' => $student->id,
                    'session_id' => $validated['session_id'],
                    'term_id' => $validated['term_id'],
                    'invoice_number' => Invoice::generateInvoiceNumber(auth()->user()->school_id),
                    'total_amount' => $totalAmount,
                    'net_amount' => $totalAmount,
                    'balance' => $totalAmount,
                    'status' => 'pending',
                    'due_date' => $fees->first()?->due_date,
                    'generated_by' => auth()->id(),
                ]);

                foreach ($fees as $fee) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'fee_structure_id' => $fee->id,
                        'description' => $fee->name,
                        'amount' => $fee->amount,
                        'discount' => 0,
                        'net_amount' => $fee->amount,
                    ]);
                }
                $count++;
            }
        });

        return back()->with('success', "{$count} invoices generated.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student.schoolClass', 'items.feeStructure', 'payments']);
        return view('financial.invoices.show', compact('invoice'));
    }
}
