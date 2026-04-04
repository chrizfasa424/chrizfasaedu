<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Parents Label</label>
            <input type="text" name="parents_label" value="{{ old('parents_label', $publicPage['parents_label'] ?? 'Parents') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Parents Intro</label>
            <textarea name="parents_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('parents_intro', $publicPage['parents_intro'] ?? '') }}</textarea>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Parents Portal Button Text</label>
            <input type="text" name="parents_portal_button_text" value="{{ old('parents_portal_button_text', $publicPage['parents_portal_button_text'] ?? 'Parent Portal Login') }}" class="w-full rounded-2xl border-slate-300">
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Parents Banner Manager</h2>
        <p class="mt-1 text-sm text-slate-500">Upload parent-section cards with image, strong title, and moderate supporting text.</p>
        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @for($i = 1; $i <= 6; $i++)
                @php $item = $parentsBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-bold text-slate-900">Parents Card {{ $i }}</p>
                    <input type="file" name="parent_banner_{{ $i }}_image" accept="image/*" class="mt-3 w-full rounded-2xl border-slate-300">
                    <input type="text" name="parent_banner_{{ $i }}_title" value="{{ old('parent_banner_' . $i . '_title', $item['title'] ?? '') }}" class="mt-3 w-full rounded-2xl border-slate-300" placeholder="Card title">
                    <textarea name="parent_banner_{{ $i }}_description" rows="4" class="js-ck-editor mt-3 w-full rounded-2xl border-slate-300" placeholder="Card context">{{ old('parent_banner_' . $i . '_description', $item['description'] ?? '') }}</textarea>
                    <label class="mt-3 inline-flex items-center text-sm font-medium text-red-600">
                        <input type="checkbox" name="remove_parent_banner_{{ $i }}" value="1" class="mr-2 rounded border-slate-300">
                        Remove current card image
                    </label>
                    <div class="mt-4">
                        @if(!empty($item['image']))
                            <img src="{{ asset('storage/' . ltrim($item['image'], '/')) }}" alt="Parent card {{ $i }}" class="h-40 w-full rounded-2xl border border-slate-200 object-cover">
                        @else
                            <div class="flex h-40 w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No image uploaded</div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Parents Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'parents',
    '_sectionLabel' => trim((string) ($publicPage['parents_label'] ?? 'Parents')),
    '_items'        => $publicPage['parents'] ?? [],
    '_publicPage'   => $publicPage,
])
