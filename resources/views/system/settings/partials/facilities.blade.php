<form action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Facilities Label</label>
            <input type="text" name="facilities_label" value="{{ old('facilities_label', $publicPage['facilities_label'] ?? 'Facilities') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Facilities Intro</label>
            <textarea name="facilities_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('facilities_intro', $publicPage['facilities_intro'] ?? '') }}</textarea>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Facilities Items</label>
            <textarea name="facilities" rows="10" class="w-full rounded-2xl border-slate-300">{{ old('facilities', $facilitiesText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one facility per line.</p>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Facilities Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'facilities',
    '_sectionLabel' => trim((string) ($publicPage['facilities_label'] ?? 'Facilities')),
    '_items'        => $publicPage['facilities'] ?? [],
    '_publicPage'   => $publicPage,
])
