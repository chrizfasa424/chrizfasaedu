<?php

namespace App\Services\Payments;

use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentFeeSyncService
{
    public function syncCurrentForStudent(Student $student, ?School $school = null): void
    {
        $schoolModel = $school ?: $student->school;
        if (!$schoolModel) {
            return;
        }

        $this->ensureStudentClassAssigned($student, $schoolModel);
        if (!$student->class_id) {
            return;
        }

        $session = $schoolModel->currentSession()
            ?: $schoolModel->sessions()->orderByDesc('is_current')->orderByDesc('id')->first();

        if (!$session) {
            return;
        }

        $fallbackTermId = (int) (
            $session->terms()->where('is_current', true)->value('id')
            ?: $session->terms()->orderByDesc('is_current')->orderBy('id')->value('id')
            ?: 0
        );

        if ($fallbackTermId <= 0) {
            return;
        }

        $this->syncForStudentScope($student, (int) $session->id, $fallbackTermId);
    }

    public function syncForStudentScope(Student $student, int $sessionId, int $fallbackTermId): void
    {
        if (!$student->class_id) {
            return;
        }

        $fees = FeeStructure::query()
            ->where('school_id', (int) $student->school_id)
            ->where('is_active', true)
            ->where('session_id', $sessionId)
            ->where(function ($query) use ($student) {
                $query->whereNull('class_id')
                    ->orWhere('class_id', (int) $student->class_id);
            })
            ->get();

        if ($fees->isEmpty()) {
            return;
        }

        $feesByTerm = $fees
            ->groupBy(fn (FeeStructure $fee) => (int) ($fee->term_id ?: $fallbackTermId))
            ->filter(fn (Collection $group, int $termId) => $termId > 0 && $group->isNotEmpty());

        foreach ($feesByTerm as $termId => $termFees) {
            DB::transaction(function () use ($student, $sessionId, $termId, $termFees) {
                $invoice = Invoice::query()->firstOrCreate(
                    [
                        'school_id' => (int) $student->school_id,
                        'student_id' => (int) $student->id,
                        'session_id' => (int) $sessionId,
                        'term_id' => (int) $termId,
                    ],
                    [
                        'invoice_number' => Invoice::generateInvoiceNumber((int) $student->school_id),
                        'total_amount' => 0,
                        'discount_amount' => 0,
                        'scholarship_amount' => 0,
                        'net_amount' => 0,
                        'amount_paid' => 0,
                        'balance' => 0,
                        'status' => 'pending',
                        'due_date' => $termFees->pluck('due_date')->filter()->min(),
                    ]
                );

                $hasSuccessfulPayments = $invoice->payments()->successful()->exists();

                foreach ($termFees as $fee) {
                    $item = InvoiceItem::query()
                        ->where('invoice_id', (int) $invoice->id)
                        ->where('fee_structure_id', (int) $fee->id)
                        ->first();

                    if (!$item) {
                        InvoiceItem::query()->create([
                            'invoice_id' => (int) $invoice->id,
                            'fee_structure_id' => (int) $fee->id,
                            'description' => (string) $fee->name,
                            'amount' => (float) $fee->amount,
                            'discount' => 0,
                            'net_amount' => (float) $fee->amount,
                        ]);
                        continue;
                    }

                    if (!$hasSuccessfulPayments) {
                        $item->update([
                            'description' => (string) $fee->name,
                            'amount' => (float) $fee->amount,
                            'net_amount' => (float) $fee->amount,
                        ]);
                    }
                }

                $total = (float) $invoice->items()->sum('net_amount');
                $dueDate = $invoice->due_date ?: $termFees->pluck('due_date')->filter()->min();

                $invoice->update([
                    'total_amount' => $total,
                    'net_amount' => $total,
                    'due_date' => $dueDate,
                ]);

                $invoice->updateBalance();
            });
        }
    }

    protected function ensureStudentClassAssigned(Student $student, School $school): void
    {
        if ($student->class_id) {
            return;
        }

        $student->loadMissing('admission');
        $appliedFor = trim((string) optional($student->admission)->class_applied_for);
        if ($appliedFor === '') {
            return;
        }

        $token = $this->normalizeClassToken($appliedFor);
        if ($token === '') {
            return;
        }

        $classes = SchoolClass::query()
            ->where('school_id', (int) $school->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'grade_level']);

        $matched = $classes->first(function (SchoolClass $class) use ($token) {
            if ($this->normalizeClassToken((string) $class->name) === $token) {
                return true;
            }

            $grade = $class->grade_level;
            if ($grade) {
                $gradeValueToken = $this->normalizeClassToken((string) $grade->value);
                $gradeLabelToken = $this->normalizeClassToken((string) $grade->label());

                return $gradeValueToken === $token || $gradeLabelToken === $token;
            }

            return false;
        });

        if (!$matched) {
            return;
        }

        $student->forceFill(['class_id' => (int) $matched->id])->save();
        $student->refresh();
    }

    protected function normalizeClassToken(string $value): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower(trim($value)));
    }
}
