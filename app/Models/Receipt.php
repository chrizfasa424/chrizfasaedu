<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    protected $fillable = [
        'school_id',
        'payment_id',
        'receipt_number',
        'pdf_path',
        'generated_by',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

