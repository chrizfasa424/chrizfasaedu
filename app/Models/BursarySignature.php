<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BursarySignature extends Model
{
    use HasFactory, BelongsToSchool, HasAuditTrail;

    public const ROLE_PRINCIPAL = 'principal';
    public const ROLE_VICE_PRINCIPAL = 'vice_principal';
    public const ROLE_BURSAR = 'bursar';

    public const ROLE_OPTIONS = [
        self::ROLE_PRINCIPAL,
        self::ROLE_VICE_PRINCIPAL,
        self::ROLE_BURSAR,
    ];

    protected $fillable = [
        'school_id',
        'name',
        'title',
        'signature_role',
        'signature_path',
        'is_active',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getSignatureRoleLabelAttribute(): string
    {
        return match ((string) $this->signature_role) {
            self::ROLE_PRINCIPAL => 'Principal',
            self::ROLE_VICE_PRINCIPAL => 'Vice Principal',
            self::ROLE_BURSAR => 'Bursar',
            default => ucwords(str_replace('_', ' ', (string) $this->signature_role)),
        };
    }
}
