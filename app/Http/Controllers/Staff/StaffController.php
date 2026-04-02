<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with('user');
        if ($request->filled('department')) $query->where('department', $request->department);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
        }
        $staff = $query->latest()->paginate(25);
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'role' => 'required|in:teacher,admin,principal,accountant,librarian,nurse,driver,staff',
            'department' => 'nullable|string',
            'designation' => 'nullable|string',
            'qualification' => 'nullable|string',
            'date_of_employment' => 'nullable|date',
            'basic_salary' => 'nullable|numeric|min:0',
            'gender' => 'required|in:male,female',
        ]);

        $user = User::create([
            'school_id' => auth()->user()->school_id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make('changeme123'),
            'role' => $validated['role'],
        ]);

        Staff::create([
            'school_id' => auth()->user()->school_id,
            'user_id' => $user->id,
            'department' => $validated['department'],
            'designation' => $validated['designation'],
            'qualification' => $validated['qualification'],
            'date_of_employment' => $validated['date_of_employment'],
            'basic_salary' => $validated['basic_salary'] ?? 0,
            'gender' => $validated['gender'],
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff member added.');
    }

    public function show(Staff $staff)
    {
        $staff->load(['user', 'classTeaching', 'subjectsTeaching', 'attendances', 'salary']);
        return view('staff.show', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $staff->update($request->validate([
            'department' => 'nullable|string',
            'designation' => 'nullable|string',
            'basic_salary' => 'nullable|numeric|min:0',
            'status' => 'in:active,on_leave,terminated,retired',
        ]));
        return back()->with('success', 'Staff updated.');
    }
}
