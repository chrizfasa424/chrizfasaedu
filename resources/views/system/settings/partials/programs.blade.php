<form action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Programs Label</label>
            <input type="text" name="programs_label" value="{{ old('programs_label', $publicPage['programs_label'] ?? 'Programs') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Programs Intro</label>
            <textarea name="programs_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('programs_intro', $publicPage['programs_intro'] ?? '') }}</textarea>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Programs Items</label>
            <textarea name="program_items_text" rows="12" class="w-full rounded-2xl border-slate-300">{{ old('program_items_text', $programItemsText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Description</p>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Programs Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'programs',
    '_sectionLabel' => trim((string) ($publicPage['programs_label'] ?? 'Programs')),
    '_items'        => $publicPage['programs'] ?? [],
    '_publicPage'   => $publicPage,
])
