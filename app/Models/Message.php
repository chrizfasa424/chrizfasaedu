<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id', 'sender_id', 'recipient_id', 'recipient_type',
        'subject', 'body', 'channel', // sms, email, in_app
        'status', // sent, delivered, failed, read
        'sent_at', 'read_at',
        'sms_reference', 'email_reference',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function recipient() { return $this->belongsTo(User::class, 'recipient_id'); }
}
