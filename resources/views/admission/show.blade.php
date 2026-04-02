@extends('layouts.app')

@section('title', 'Application Details')
@section('header', 'Admission Application')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $admission->first_name }} {{ $admission->last_name }}</h2>
                <p class="text-sm text-gray-500 mt-1">Application No: <span class="font-mono font-semibold text-gray-700">{{ $admission->application_number }}</span></p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $admission->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $admission->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                {{ $admission->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                {{ $admission->status->value === 'enrolled' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $admission->status->value === 'screening' ? 'bg-purple-100 text-purple-800' : '' }}
            ">
                {{ ucfirst($admission->status->value) }}
            </span>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Date of Birth</dt>
                <dd class="font-medium text-gray-900">{{ $admission->date_of_birth?->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Gender</dt>
                <dd class="font-medium text-gray-900">{{ ucfirst($admission->gender) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Class Applied For</dt>
                <dd class="font-medium text-gray-900">{{ $admission->class_applied_for }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">State of Origin</dt>
                <dd class="font-medium text-gray-900">{{ $admission->state_of_origin ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">LGA</dt>
                <dd class="font-medium text-gray-900">{{ $admission->lga ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Previous School</dt>
                <dd class="font-medium text-gray-900">{{ $admission->previous_school ?? 'N/A' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-gray-500">Address</dt>
                <dd class="font-medium text-gray-900">{{ $admission->address ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Parent / Guardian</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Name</dt>
                <dd class="font-medium text-gray-900">{{ $admission->parent_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Phone</dt>
                <dd class="font-medium text-gray-900">{{ $admission->parent_phone }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium text-gray-900">{{ $admission->parent_email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Occupation</dt>
                <dd class="font-medium text-gray-900">{{ $admission->parent_occupation ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    @auth
    @if($admission->review_notes)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-2">Review Notes</h3>
        <p class="text-sm text-gray-700">{{ $admission->review_notes }}</p>
        @if($admission->screening_score)
        <p class="text-sm text-gray-500 mt-2">Screening Score: <strong>{{ $admission->screening_score }}%</strong></p>
        @endif
    </div>
    @endif

    @if(in_array(auth()->user()->role->value, ['super_admin','school_admin','principal']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Review Application</h3>
        <form action="{{ route('admission.review', $admission) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="screening" {{ $admission->status->value === 'screening' ? 'selected' : '' }}>Screening</option>
                        <option value="approved" {{ $admission->status->value === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $admission->status->value === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Screening Score (%)</label>
                    <input type="number" name="screening_score" min="0" max="100" value="{{ $admission->screening_score }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Review Notes</label>
                <textarea name="review_notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $admission->review_notes }}</textarea>
            </div>
            <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700">
                Update Review
            </button>
        </form>
    </div>

    @if($admission->status->value === 'approved')
    <form action="{{ route('admission.enroll', $admission) }}" method="POST">
        @csrf
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700"
            onclick="return confirm('Enroll this student and create their account?')">
            Enroll Student
        </button>
    </form>
    @endif
    @endif
    @endauth
</div>
@endsection
