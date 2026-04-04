<form action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Student Life Label</label>
            <input type="text" name="student_life_label" value="{{ old('student_life_label', $publicPage['student_life_label'] ?? 'Student Life') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Student Life Intro</label>
            <textarea name="student_life_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('student_life_intro', $publicPage['student_life_intro'] ?? '') }}</textarea>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Student Life Items</label>
            <textarea name="student_life_items_text" rows="12" class="w-full rounded-2xl border-slate-300">{{ old('student_life_items_text', $studentLifeItemsText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Description</p>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Student Life Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'student-life',
    '_sectionLabel' => trim((string) ($publicPage['student_life_label'] ?? 'Student Life')),
    '_items'        => $publicPage['student_life'] ?? [],
    '_publicPage'   => $publicPage,
])
