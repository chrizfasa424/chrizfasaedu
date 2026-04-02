<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id', 'invoice_id', 'student_id',
        'payment_reference', 'transaction_id',
        'amount', 'payment_method', // paystack, flutterwave, bank_transfer, cash, pos
        'payment_gateway', 'gateway_response',
        'status', // pending, confirmed, failed, reversed
        'paid_at', 'confirmed_by',
        'bank_name', 'account_name', 'receipt_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function confirmer() { return $this->belongsTo(User::class, 'confirmed_by'); }

    public static function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid()) . '-' . time();
    }
}
