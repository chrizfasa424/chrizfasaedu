@extends('layouts.app')

@section('title', 'Testimonials Moderation')
@section('header', 'Testimonials Moderation')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm text-indigo-800">
        Public testimonials are not shown until approved here. This helps prevent spam and protects content quality.
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-lg font-semibold text-gray-900">Pending Testimonials</h3>
        <p class="mt-1 text-sm text-gray-500">Review and approve submissions before they appear on the homepage.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Role/Context</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Rating</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Message</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Submitted</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingTestimonials as $testimonial)
                        <tr>
                            <td class="px-3 py-3 font-medium text-gray-900">{{ $testimonial->full_name }}</td>
                            <td class="px-3 py-3 text-gray-600">{{ $testimonial->role_title ?: '-' }}</td>
                            <td class="px-3 py-3 text-amber-600 font-semibold">{{ max(1, min(5, (int) $testimonial->rating)) }}/5</td>
                            <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($testimonial->message, 130) }}</td>
                            <td class="px-3 py-3 text-gray-500">{{ $testimonial->created_at?->diffForHumans() }}</td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <form action="{{ route('system.testimonials.approve', $testimonial->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <form action="{{ route('system.testimonials.reject', $testimonial->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No pending testimonials.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pendingTestimonials->links() }}</div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-lg font-semibold text-gray-900">Approved Testimonials</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Rating</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Message</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Approved</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($approvedTestimonials as $testimonial)
                        <tr>
                            <td class="px-3 py-3 font-medium text-gray-900">{{ $testimonial->full_name }}</td>
                            <td class="px-3 py-3 text-amber-600 font-semibold">{{ max(1, min(5, (int) $testimonial->rating)) }}/5</td>
                            <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($testimonial->message, 120) }}</td>
                            <td class="px-3 py-3 text-gray-500">{{ $testimonial->reviewed_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <form action="{{ route('system.testimonials.reject', $testimonial->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Move to Rejected</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500">No approved testimonials yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $approvedTestimonials->links() }}</div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-lg font-semibold text-gray-900">Rejected Testimonials</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Message</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Reviewed</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rejectedTestimonials as $testimonial)
                        <tr>
                            <td class="px-3 py-3 font-medium text-gray-900">{{ $testimonial->full_name }}</td>
                            <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($testimonial->message, 120) }}</td>
                            <td class="px-3 py-3 text-gray-500">{{ $testimonial->reviewed_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <form action="{{ route('system.testimonials.approve', $testimonial->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve Instead</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">No rejected testimonials.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $rejectedTestimonials->links() }}</div>
    </section>
</div>
@endsection
