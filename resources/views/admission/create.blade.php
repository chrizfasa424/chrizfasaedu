@extends('layouts.app')
@section('title', 'New Admission')
@section('header', 'New Admission')

@section('content')
<div class="space-y-6 max-w-3xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('admission.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Admissions</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">New Application</span>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admission.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Applicant Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Other Names</label>
                        <input type="text" name="other_names" value="{{ old('other_names') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Class Applied For <span class="text-red-500">*</span></label>
                        <input type="text" name="class_applied_for" value="{{ old('class_applied_for') }}" required placeholder="e.g. JSS 1"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">State of Origin</label>
                        <input type="text" name="state_of_origin" value="{{ old('state_of_origin') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">LGA</label>
                        <input type="text" name="lga" value="{{ old('lga') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Previous School</label>
                    <input type="text" name="previous_school" value="{{ old('previous_school') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Parent / Guardian Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Parent Name <span class="text-red-500">*</span></label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Parent Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Parent Email</label>
                        <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Occupation</label>
                        <input type="text" name="parent_occupation" value="{{ old('parent_occupation') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5">
                <h3 class="text-sm font-semibold text-slate-700 mb-4">Documents</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Photo</label>
                        <input type="file" name="photo" accept="image/*"
                            class="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-1.5 file:text-sm hover:file:bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Birth Certificate</label>
                        <input type="file" name="birth_certificate"
                            class="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-1.5 file:text-sm hover:file:bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Previous Result</label>
                        <input type="file" name="previous_result"
                            class="block text-sm text-slate-600 file:mr-3 file:rounded-lg file:border file:border-slate-300 file:bg-white file:px-3 file:py-1.5 file:text-sm hover:file:bg-slate-50">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('admission.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Submit Application</button>
            </div>
        </form>
    </div>

</div>
@endsection
