<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\MessageReply;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PortalMessageController extends Controller
{
    private function portalUser()
    {
        return auth('portal')->user() ?? auth()->user();
    }

    // ── Inbox ──────────────────────────────────────────────
    public function index()
    {
        $user = $this->portalUser();

        $recipients = MessageRecipient::with(['message.sender'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(20);

        return view('portal.messages.index', compact('recipients'));
    }

    // ── View a single message ──────────────────────────────
    public function show(Message $message)
    {
        $user = $this->portalUser();

        // Ensure the user is actually a recipient of this message
        $recipient = MessageRecipient::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Mark as read
        $recipient->markRead();

        // Load message with sender
        $message->load('sender', 'schoolClass');

        // Load only this user's replies
        $replies = MessageReply::where('message_id', $message->id)
            ->where('sender_id', $user->id)
            ->orderBy('created_at')
            ->get();

        return view('portal.messages.show', compact('message', 'recipient', 'replies'));
    }

    // ── Submit a reply ─────────────────────────────────────
    public function reply(Request $request, Message $message)
    {
        $user = $this->portalUser();

        // Verify recipient
        $exists = MessageRecipient::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            abort(403, 'You are not a recipient of this message.');
        }

        $request->validate([
            'body' => 'required|string|max:50000',
        ]);

        $body = RichTextSanitizer::sanitize((string) $request->input('body', ''));
        if (RichTextSanitizer::plainTextLength($body) === 0) {
            throw ValidationException::withMessages([
                'body' => 'Reply body cannot be empty.',
            ]);
        }

        MessageReply::create([
            'message_id' => $message->id,
            'sender_id'  => $user->id,
            'body'       => $body,
        ]);

        return redirect()->route('portal.messages.show', $message)
            ->with('success', 'Reply sent.');
    }

    // ── Unread count (JSON) ────────────────────────────────
    public function unreadCount()
    {
        return response()->json([
            'count' => $this->portalUser()->unreadMessagesCount(),
        ]);
    }
}
