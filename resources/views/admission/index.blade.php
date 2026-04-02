@extends('layouts.app')

@section('title', 'Admissions')
@section('header', 'Admission Applications')

@section('content')
<div class="flex justify-between items-center mb-6">
    <form method="GET" class="flex gap-3">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
        </select>
        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-emerald-700">Filter</button>
    </form>
    <a href="{{ route('admission.apply') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700">
        + New Application
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">App. No</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Student Name</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Class</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Parent Phone</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Date</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($admissions as $admission)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $admission->application_number }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $admission->first_name }} {{ $admission->last_name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $admission->class_applied_for }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $admission->parent_phone }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $admission->created_at->format('d M Y') }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $admission->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $admission->status->value === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $admission->status->value === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $admission->status->value === 'enrolled' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $admission->status->value === 'screening' ? 'bg-purple-100 text-purple-800' : '' }}
                    ">{{ ucfirst($admission->status->value) }}</span>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('admission.show', $admission) }}" class="text-emerald-600 hover:underline text-xs font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-10 text-center text-gray-400">No applications found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($admissions->hasPages())
<div class="mt-5">
    {{ $admissions->links() }}
</div>
@endif
@endsection
