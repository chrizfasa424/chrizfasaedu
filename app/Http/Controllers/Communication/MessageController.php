<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\SchoolClass;
use App\Services\MessageDispatchService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // ── List sent messages ─────────────────────────────────
    public function index()
    {
        $messages = Message::with(['sender', 'schoolClass'])
            ->withCount(['recipients', 'replies'])
            ->withCount(['recipients as read_count' => fn ($q) => $q->whereNotNull('read_at')])
            ->withCount(['replies as unread_replies_count' => fn ($q) => $q->whereNull('read_by_admin_at')])
            ->latest()
            ->paginate(20);

        $totalUnreadReplies = auth()->user()->unreadAdminRepliesCount();

        return view('communication.messages.index', compact('messages', 'totalUnreadReplies'));
    }

    // ── Compose form ───────────────────────────────────────
    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('communication.messages.create', compact('classes'));
    }

    // ── Send message ───────────────────────────────────────
    public function store(Request $request, MessageDispatchService $dispatcher)
    {
        $validated = $request->validate([
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
            'audience' => 'required|in:all_students,all_parents,all_portal,class',
            'class_id' => 'required_if:audience,class|nullable|exists:classes,id',
        ]);

        $dispatcher->send($validated, auth()->user());

        return redirect()->route('messages.index')
            ->with('success', 'Message sent successfully.');
    }

    // ── View message + replies ─────────────────────────────
    public function show(Message $message)
    {
        $this->authorizeSchool($message);

        // Mark all replies on this message as read by admin
        MessageReply::where('message_id', $message->id)
            ->whereNull('read_by_admin_at')
            ->update(['read_by_admin_at' => now()]);

        $message->load([
            'sender',
            'schoolClass',
            'replies.sender',
        ]);

        $recipientsCount = $message->recipients()->count();
        $readCount       = $message->recipients()->whereNotNull('read_at')->count();

        return view('communication.messages.show', compact('message', 'recipientsCount', 'readCount'));
    }

    // ── Delete message ─────────────────────────────────────
    public function destroy(Message $message)
    {
        $this->authorizeSchool($message);
        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted.');
    }

    // ── Unread reply count (JSON) ──────────────────────────
    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadAdminRepliesCount(),
        ]);
    }

    // ── Guard: message belongs to user's school ────────────
    private function authorizeSchool(Message $message): void
    {
        if ($message->school_id !== auth()->user()->school_id) {
            abort(403);
        }
    }
}
