<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        return view('communication.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:general,academic,financial,event',
            'audience' => 'required|in:all,students,parents,staff',
            'priority' => 'in:low,normal,high,urgent',
            'send_sms' => 'boolean',
            'send_email' => 'boolean',
        ]);

        Announcement::create([
            ...$validated,
            'school_id' => auth()->user()->school_id,
            'created_by' => auth()->id(),
            'published_at' => now(),
        ]);

        return back()->with('success', 'Announcement published.');
    }
}
