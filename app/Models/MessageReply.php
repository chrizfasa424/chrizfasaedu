<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $fillable = [
        'message_id',
        'sender_id',
        'body',
        'read_by_admin_at',
    ];

    protected $casts = [
        'read_by_admin_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeUnreadByAdmin($query)
    {
        return $query->whereNull('read_by_admin_at');
    }
}
