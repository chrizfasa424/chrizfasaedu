<?php

namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::with(['rooms', 'warden.user'])->paginate(15);
        return view('hostel.index', compact('hostels'));
    }

    public function store(Request $request)
    {
        Hostel::create($request->validate([
            'name' => 'required|string',
            'type' => 'required|in:male,female',
            'warden_id' => 'nullable|exists:staff,id',
            'capacity' => 'integer|min:1',
            'fee_amount' => 'numeric|min:0',
        ]));
        return back()->with('success', 'Hostel created.');
    }

    public function allocateRoom(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:hostel_rooms,id',
        ]);

        $room = HostelRoom::findOrFail($validated['room_id']);
        abort_if(!$room->isAvailable(), 422, 'Room is full.');

        \App\Models\Student::findOrFail($validated['student_id'])->update([
            'hostel_room_id' => $room->id,
            'is_boarding' => true,
        ]);
        $room->increment('occupied');

        return back()->with('success', 'Room allocated.');
    }
}
