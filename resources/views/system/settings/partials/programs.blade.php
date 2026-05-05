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
            <textarea name="programs_intro" rows="4" class="w-full rounded-2xl border-slate-300">{{ old('programs_intro', strip_tags((string) ($publicPage['programs_intro'] ?? ''))) }}</textarea>
        </div>
        <input type="hidden" name="program_items_text" value="{{ old('program_items_text', $programItemsText) }}">
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">Save Programs Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'programs',
    '_sectionLabel' => trim((string) ($publicPage['programs_label'] ?? 'Programs')),
    '_items'        => $publicPage['programs'] ?? [],
    '_publicPage'   => $publicPage,
])
