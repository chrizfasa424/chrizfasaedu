<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'student_id', 'session_id', 'term_id',
        'invoice_number', 'total_amount', 'discount_amount',
        'scholarship_amount', 'net_amount', 'amount_paid',
        'balance', 'status',
        'due_date', 'late_fee_applied', 'late_fee_amount',
        'notes', 'generated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'late_fee_applied' => 'boolean',
        'due_date' => 'date',
        'status' => PaymentStatus::class,
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function session() { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function term() { return $this->belongsTo(AcademicTerm::class, 'term_id'); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function updateBalance(): void
    {
        $this->amount_paid = $this->payments()->where('status', 'confirmed')->sum('amount');
        $this->balance = $this->net_amount - $this->amount_paid;
        $this->status = match(true) {
            $this->balance <= 0 => PaymentStatus::PAID,
            $this->amount_paid > 0 => PaymentStatus::PARTIAL,
            $this->due_date?->isPast() => PaymentStatus::OVERDUE,
            default => PaymentStatus::PENDING,
        };
        $this->save();
    }

    public static function generateInvoiceNumber(int $schoolId): string
    {
        $count = static::where('school_id', $schoolId)->count() + 1;
        return sprintf('INV-%s-%06d', date('Y'), $count);
    }
}
