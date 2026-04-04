<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Academic Excellence Label</label>
            <input type="text" name="academics_label" value="{{ old('academics_label', $publicPage['academics_label'] ?? 'Academic Excellence') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Academics Intro</label>
            <textarea name="academics_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('academics_intro', $publicPage['academics_intro'] ?? '') }}</textarea>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Academics Context Text</label>
            <textarea name="academics_support_text" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('academics_support_text', $publicPage['academics_support_text'] ?? '') }}</textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Highlight Card 1 Title</label>
            <input type="text" name="academic_highlight_1_title" value="{{ old('academic_highlight_1_title', $publicPage['academic_highlights'][0]['title'] ?? 'STEM-First Curriculum') }}" class="w-full rounded-2xl border-slate-300">
            <label class="mb-2 mt-4 block text-sm font-semibold text-slate-700">Highlight Card 1 Context</label>
            <textarea name="academic_highlight_1_description" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('academic_highlight_1_description', $publicPage['academic_highlights'][0]['description'] ?? '') }}</textarea>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Highlight Card 2 Title</label>
            <input type="text" name="academic_highlight_2_title" value="{{ old('academic_highlight_2_title', $publicPage['academic_highlights'][1]['title'] ?? 'Student Leadership') }}" class="w-full rounded-2xl border-slate-300">
            <label class="mb-2 mt-4 block text-sm font-semibold text-slate-700">Highlight Card 2 Context</label>
            <textarea name="academic_highlight_2_description" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('academic_highlight_2_description', $publicPage['academic_highlights'][1]['description'] ?? '') }}</textarea>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Academic Visual Manager</h2>
        <p class="mt-1 text-sm text-slate-500">Upload the two right-side images used in the Academic Excellence section on the homepage.</p>
        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
            @for($i = 1; $i <= 2; $i++)
                @php $visual = $academicsVisualSlots[$i - 1]['image'] ?? null; @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Academic Image {{ $i }}</label>
                    <input type="file" name="academic_image_{{ $i }}" accept="image/*" class="w-full rounded-2xl border-slate-300">
                    <label class="mt-4 inline-flex items-center text-sm font-medium text-red-600">
                        <input type="checkbox" name="remove_academic_image_{{ $i }}" value="1" class="mr-2 rounded border-slate-300">
                        Remove current image
                    </label>
                    <div class="mt-4">
                        @if($visual)
                            <img src="{{ asset('storage/' . ltrim($visual, '/')) }}" alt="Academic image {{ $i }}" class="h-40 w-full rounded-2xl border border-slate-200 object-cover">
                        @else
                            <div class="flex h-40 w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No image uploaded</div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Academics Items (Legacy)</label>
        <textarea name="academics_items_text" rows="10" class="w-full rounded-2xl border-slate-300">{{ old('academics_items_text', $academicsItemsText) }}</textarea>
        <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Description</p>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Academic Excellence Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'academics',
    '_sectionLabel' => trim((string) ($publicPage['academics_label'] ?? 'Academic Excellence')),
    '_items'        => $publicPage['academics'] ?? [],
    '_publicPage'   => $publicPage,
])
