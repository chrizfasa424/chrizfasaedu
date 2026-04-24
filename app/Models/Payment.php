<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_CONFIRMED_LEGACY = 'confirmed';

    protected $fillable = [
        'school_id', 'invoice_id', 'student_id',
        'payment_method_id',
        'payment_reference', 'transaction_id',
        'gateway_reference',
        'amount', 'amount_expected', 'payment_date',
        'payment_method', // paystack, flutterwave, bank_transfer, cash, pos
        'payment_gateway', 'gateway_response',
        'gateway_name',
        'status',
        'paid_at', 'confirmed_by',
        'bank_name', 'account_name', 'receipt_number',
        'notes', 'proof_file_path', 'proof_original_name',
        'submitted_by', 'verified_by', 'verified_at',
        'verification_note', 'rejection_reason',
        'approved_at', 'cancelled_at',
        'meta_json',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_expected' => 'decimal:2',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'gateway_response' => 'array',
        'meta_json' => 'array',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function confirmer() { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class, 'payment_method_id'); }
    public function receipt() { return $this->hasOne(Receipt::class); }

    public static function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid()) . '-' . time();
    }

    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_CONFIRMED_LEGACY]);
    }

    public function isSuccessful(): bool
    {
        return in_array((string) $this->status, [self::STATUS_APPROVED, self::STATUS_CONFIRMED_LEGACY], true);
    }
}
