{{--
  Shared submenu item editor.
  Required variables passed via @include:
    $_section      — section key, e.g. 'admissions', 'student-life'
    $_sectionLabel — human label, e.g. 'Admissions', 'Student Life'
    $_items        — array of items (each may be plain string OR ['title'=>...])
    $_publicPage   — the full $publicPage array
--}}
@php
    $_normalizedItems = collect($_items ?? [])
        ->map(fn ($i) => is_array($i) ? $i : ['title' => trim((string) $i), 'description' => ''])
        ->filter(fn ($i) => !empty($i['title']))
        ->values()
        ->all();

    $_images  = (array) data_get($_publicPage ?? [], 'submenu_images.'  . ($_section ?? ''), []);
    $_content = (array) data_get($_publicPage ?? [], 'submenu_content.' . ($_section ?? ''), []);
@endphp

@if(!empty($_normalizedItems))

{{-- ── Cover Images ────────────────────────────────────────── --}}
<div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-6 py-5">
        <h3 class="font-display text-lg font-semibold text-slate-900">{{ $_sectionLabel ?? 'Section' }} — Submenu Cover Images</h3>
        <p class="mt-1 text-sm text-slate-500">Upload a full-width hero image for each item's detail page. Displays at the top of <code class="rounded bg-slate-100 px-1 text-xs">/menu/{{ $_section }}/{slug}</code>.</p>
    </div>

    @if(session('success'))
        <div class="mx-6 mt-4 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="divide-y divide-slate-100">
        @foreach($_normalizedItems as $_item)
            @php
                $_itemTitle = trim((string) ($_item['title'] ?? ''));
                $_itemSlug  = \Illuminate\Support\Str::slug($_itemTitle);
                $_existingImg = trim((string) ($_images[$_itemSlug] ?? ''));
            @endphp
            <div class="flex flex-wrap items-center gap-5 px-6 py-5">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $_itemTitle }}</p>
                    <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $_itemSlug }}</p>
                    @if($_existingImg !== '')
                        <img src="{{ asset('storage/' . ltrim($_existingImg, '/')) }}" alt="{{ $_itemTitle }}"
                            class="mt-3 h-20 w-full max-w-xs rounded-xl border border-slate-200 object-cover shadow-sm">
                    @else
                        <div class="mt-3 flex h-20 w-full max-w-xs items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No image yet</div>
                    @endif
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:min-w-[200px]">
                    <form action="{{ route('settings.submenu-image.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                        @csrf
                        <input type="hidden" name="section" value="{{ $_section }}">
                        <input type="hidden" name="slug"    value="{{ $_itemSlug }}">
                        <input type="file" name="image" accept="image/*" required
                            class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#2D1D5C] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white">
                        <button type="submit"
                            class="rounded-xl bg-[#2D1D5C] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                            {{ $_existingImg !== '' ? 'Replace Image' : 'Upload Image' }}
                        </button>
                    </form>
                    @if($_existingImg !== '')
                        <form action="{{ route('settings.submenu-image.remove') }}" method="POST"
                            onsubmit="return confirm('Remove image for {{ addslashes($_itemTitle) }}?')">
                            @csrf
                            <input type="hidden" name="section" value="{{ $_section }}">
                            <input type="hidden" name="slug"    value="{{ $_itemSlug }}">
                            <button type="submit"
                                class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                Remove
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── Per-item Page Content ───────────────────────────────── --}}
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-6 py-5">
        <h3 class="font-display text-lg font-semibold text-slate-900">{{ $_sectionLabel ?? 'Section' }} — Submenu Page Content</h3>
        <p class="mt-1 text-sm text-slate-500">Customise the description and both highlight boxes shown on each item's detail page. Leave blank to use the global defaults.</p>
    </div>

    <div class="divide-y divide-slate-100">
        @foreach($_normalizedItems as $_item)
            @php
                $_itemTitle = trim((string) ($_item['title'] ?? ''));
                $_itemSlug  = \Illuminate\Support\Str::slug($_itemTitle);
                $_saved     = (array) ($_content[$_itemSlug] ?? []);
            @endphp

            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4 transition hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center gap-3">
                        @php $_hasContent = !empty($_saved['description']) || !empty($_saved['highlight_one_title']) || !empty($_saved['highlight_one_text']) || !empty($_saved['highlight_two_title']) || !empty($_saved['highlight_two_text']); @endphp
                        @if($_hasContent)
                            <span class="inline-flex h-2 w-2 rounded-full bg-green-500" title="Has custom content"></span>
                        @else
                            <span class="inline-flex h-2 w-2 rounded-full bg-slate-300" title="Using global defaults"></span>
                        @endif
                        <span class="text-sm font-semibold text-slate-800">{{ $_itemTitle }}</span>
                    </div>
                    <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>

                <div class="border-t border-slate-100 bg-slate-50/50 px-6 pb-6 pt-5 space-y-6">

                    {{-- Page Images --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-4">Page Images (2 slots)</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach(['image_one' => 'Image 1', 'image_two' => 'Image 2'] as $_slot => $_slotLabel)
                                @php $_imgPath = trim((string) ($_saved[$_slot] ?? '')); @endphp
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="mb-3 text-xs font-semibold text-slate-600">{{ $_slotLabel }}</p>
                                    @if($_imgPath !== '')
                                        <img src="{{ asset('storage/' . ltrim($_imgPath, '/')) }}"
                                             alt="{{ $_slotLabel }}"
                                             class="mb-3 h-32 w-full rounded-xl border border-slate-200 object-cover shadow-sm">
                                    @else
                                        <div class="mb-3 flex h-32 w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">No image yet</div>
                                    @endif
                                    <form action="{{ route('settings.submenu-content-image.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                        @csrf
                                        <input type="hidden" name="section" value="{{ $_section }}">
                                        <input type="hidden" name="slug"    value="{{ $_itemSlug }}">
                                        <input type="hidden" name="slot"    value="{{ $_slot }}">
                                        <input type="file" name="image" accept="image/*" required
                                               class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-2 file:rounded-lg file:border-0 file:bg-[#2D1D5C] file:px-2.5 file:py-1 file:text-xs file:font-semibold file:text-white">
                                        <button type="submit"
                                                class="rounded-xl bg-[#2D1D5C] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                                            {{ $_imgPath !== '' ? 'Replace' : 'Upload' }}
                                        </button>
                                    </form>
                                    @if($_imgPath !== '')
                                        <form action="{{ route('settings.submenu-content-image.remove') }}" method="POST" class="mt-2"
                                              onsubmit="return confirm('Remove {{ $_slotLabel }}?')">
                                            @csrf
                                            <input type="hidden" name="section" value="{{ $_section }}">
                                            <input type="hidden" name="slug"    value="{{ $_itemSlug }}">
                                            <input type="hidden" name="slot"    value="{{ $_slot }}">
                                            <button type="submit"
                                                    class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Text Content --}}
                    <form action="{{ route('settings.submenu-content.save') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="section" value="{{ $_section }}">
                        <input type="hidden" name="slug"    value="{{ $_itemSlug }}">

                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Description
                                <span class="ml-1 font-normal text-slate-400 text-xs">— overrides the plain description from the items textarea</span>
                            </label>
                            <textarea name="description" rows="5"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]/30">{{ old('description', $_saved['description'] ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Highlight Box 1</p>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Title</label>
                                <input type="text" name="highlight_one_title"
                                    value="{{ old('highlight_one_title', $_saved['highlight_one_title'] ?? '') }}"
                                    placeholder="e.g. What Students Gain"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]/30">
                                <label class="mb-1.5 mt-4 block text-sm font-semibold text-slate-700">Body</label>
                                <textarea name="highlight_one_text" rows="4"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]/30">{{ old('highlight_one_text', $_saved['highlight_one_text'] ?? '') }}</textarea>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Highlight Box 2</p>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Title</label>
                                <input type="text" name="highlight_two_title"
                                    value="{{ old('highlight_two_title', $_saved['highlight_two_title'] ?? '') }}"
                                    placeholder="e.g. How We Deliver"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]/30">
                                <label class="mb-1.5 mt-4 block text-sm font-semibold text-slate-700">Body</label>
                                <textarea name="highlight_two_text" rows="4"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]/30">{{ old('highlight_two_text', $_saved['highlight_two_text'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-[#2D1D5C] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Save — {{ $_itemTitle }}
                            </button>
                            @if($_hasContent)
                                <span class="text-xs text-green-600 font-medium">Custom content active</span>
                            @else
                                <span class="text-xs text-slate-400">Using global defaults</span>
                            @endif
                        </div>
                    </form>
                </div>
            </details>
        @endforeach
    </div>
</div>

@endif
