<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Scholarship;

class InvoiceService
{
    public function generateForStudent(Student $student, int $sessionId, int $termId): Invoice
    {
        $fees = FeeStructure::where('session_id', $sessionId)
            ->where(function ($q) use ($student, $termId) {
                $q->where('class_id', $student->class_id)->orWhereNull('class_id');
            })
            ->where(function ($q) use ($termId) {
                $q->where('term_id', $termId)->orWhereNull('term_id');
            })
            ->where('is_active', true)
            ->get();

        $totalAmount = $fees->sum('amount');
        $scholarshipDiscount = $this->calculateScholarship($student, $totalAmount);
        $netAmount = $totalAmount - $scholarshipDiscount;

        $invoice = Invoice::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'session_id' => $sessionId,
            'term_id' => $termId,
            'invoice_number' => Invoice::generateInvoiceNumber($student->school_id),
            'total_amount' => $totalAmount,
            'scholarship_amount' => $scholarshipDiscount,
            'net_amount' => $netAmount,
            'balance' => $netAmount,
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

        return $invoice;
    }

    protected function calculateScholarship(Student $student, float $totalAmount): float
    {
        $scholarship = Scholarship::whereHas('students', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('status', 'active');
        })->where('is_active', true)->first();

        if (!$scholarship) return 0;

        return $scholarship->type === 'percentage'
            ? ($totalAmount * $scholarship->value / 100)
            : min($scholarship->value, $totalAmount);
    }
}
