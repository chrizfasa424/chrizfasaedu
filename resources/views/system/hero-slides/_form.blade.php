@php
    use Illuminate\Support\Str;

    $isEdit = $slide->exists;
    $imagePath = (string) ($slide->image_path ?? '');
    $placeholderVisual = $isEdit && Str::startsWith($imagePath, 'schools/logos/');
    $currentImage = $isEdit && $imagePath !== '' ? asset('storage/' . ltrim($imagePath, '/')) : null;
    $previewTitle = old('title', $slide->title ?: 'A modern learning environment for KG, Primary, and Secondary students');
    $previewSubtitle = old('subtitle', $slide->subtitle ?: 'Use this space to explain the value of the slide and guide families toward the right next action.');
    $previewBadge = old('badge_text', $slide->badge_text ?: 'Standard and Industry Professional School');
    $previewButtonOne = old('button_1_text', $slide->button_1_text ?: 'Start Admission');
    $previewButtonTwo = old('button_2_text', $slide->button_2_text ?: 'Explore Programs');
    $previewCardTitle = old('right_card_title', $slide->right_card_title ?: 'School Highlights');
    $previewCardText = old('right_card_text', $slide->right_card_text ?: 'A focused overlay message that supports the slide visual and strengthens the main story.');
    $previewSchool = old('school_name', $slide->school_name ?: auth()->user()->school?->name ?: 'ChrizFasa Academy');
    $previewSchoolInitials = Str::upper(
        collect(preg_split('/\s+/', trim($previewSchool)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('')
    ) ?: 'CA';
@endphp

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_0.95fr]">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-[#DDD8ED] bg-gradient-to-br from-[#24164A] via-[#2D1D5C] to-[#43316F] text-white shadow-[0_28px_80px_-42px_rgba(17,24,39,0.55)]">
            <div class="grid gap-6 px-6 py-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-7">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/55">Hero Slide Studio</p>
                    <h3 class="mt-4 text-3xl font-black leading-tight">{{ $isEdit ? 'Refine this hero slide with stronger story, CTA, and image direction.' : 'Build a polished hero slide that feels like a modern school landing page.' }}</h3>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200/90">Each slide should communicate one clear message. Keep the heading strong, the subtitle supportive, and the image visually clean enough to feel premium on desktop and mobile.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Recommended Source</p>
                        <p class="mt-3 text-base font-bold text-white">1800x1400 or larger</p>
                        <p class="mt-2 text-sm text-slate-200/80">Use a sharp landscape photo so cropping still looks clean inside the hero visual frame.</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/55">Visible Crop</p>
                        <p class="mt-3 text-base font-bold text-[#DFE753]">Centered tall display panel</p>
                        <p class="mt-2 text-sm text-slate-200/80">Keep people, buildings, and key details near the center so they are not cropped out.</p>
                    </div>
                </div>

                <div id="hero-preview-mobile" class="relative hidden overflow-hidden rounded-[1.9rem] border border-[#D6DFF0] bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.82),transparent_38%),radial-gradient(circle_at_bottom_right,rgba(223,231,83,0.24),transparent_34%)] p-4">
                    <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(45, 29, 92, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45, 29, 92, 0.08) 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="relative mx-auto max-w-[22rem] overflow-hidden rounded-[1.6rem] border border-[#D6DFF0] bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]">
                        <div class="bg-[#2D1D5C] px-4 py-3 text-white">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/10">
                                    <span id="hero-preview-mobile-badge" class="text-sm font-black tracking-[0.24em] text-[#DFE753]">{{ $previewSchoolInitials }}</span>
                                </div>
                                <div>
                                    <p id="hero-preview-mobile-brand" class="text-sm font-semibold leading-tight">{{ $previewSchool }}</p>
                                    <p class="text-[11px] text-white/70">Mobile homepage preview</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <p id="hero-preview-mobile-pill" class="inline-flex items-center rounded-full border border-[#DFE753] bg-white px-3 py-1 text-[11px] font-semibold tracking-tight text-[#2D1D5C]">{{ $previewBadge }}</p>
                            <h3 id="hero-preview-mobile-title" class="mt-4 font-[Georgia,serif] text-[2rem] font-semibold leading-[0.98] tracking-[-0.04em] text-slate-900">{{ $previewTitle }}</h3>
                            <p id="hero-preview-mobile-subtitle" class="mt-4 text-sm leading-7 text-slate-600">{{ $previewSubtitle }}</p>
                            <div class="mt-5 space-y-3">
                                <span id="hero-preview-mobile-btn-one" class="inline-flex w-full items-center justify-center rounded-full bg-[#2D1D5C] px-5 py-3 text-sm font-bold text-white">{{ $previewButtonOne }}</span>
                                <span id="hero-preview-mobile-btn-two" class="inline-flex w-full items-center justify-center rounded-full border border-[#2D1D5C]/25 bg-white px-5 py-3 text-sm font-bold text-[#2D1D5C]">{{ $previewButtonTwo }}</span>
                            </div>
                            <div class="relative mt-5 overflow-hidden rounded-[1.4rem] border border-[#2D1D5C]/10 bg-[#24164A]">
                                <div class="relative aspect-[4/4.2] overflow-hidden">
                                    <img id="hero-preview-mobile-image" src="{{ $currentImage }}" alt="Hero mobile preview" class="{{ $currentImage && !$placeholderVisual ? '' : 'hidden ' }}h-full w-full object-cover">
                                    <div id="hero-preview-mobile-crop-guide" class="{{ $currentImage && !$placeholderVisual ? '' : 'hidden ' }}absolute inset-0 pointer-events-none">
                                        <div class="absolute inset-x-[14%] inset-y-[10%] rounded-[1.1rem] border border-dashed border-[#DFE753]/80"></div>
                                    </div>
                                    <div id="hero-preview-mobile-placeholder" class="{{ $currentImage && !$placeholderVisual ? 'hidden ' : '' }}absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_28%),linear-gradient(135deg,#201244_0%,#2D1D5C_45%,#4D3A87_100%)]"></div>
                                    <div id="hero-preview-mobile-copy" class="{{ $currentImage && !$placeholderVisual ? 'hidden ' : '' }}absolute inset-x-4 bottom-4 rounded-[1rem] border border-white/12 bg-white/10 p-3 text-xs leading-6 text-white/90 backdrop-blur">Upload a real school photo to replace this premium placeholder.</div>
                                </div>
                            </div>
                            <div class="mt-4 rounded-[1.25rem] border border-[#2D1D5C]/12 bg-[#FBFAFE] p-4">
                                <p id="hero-preview-mobile-school" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/60">{{ $previewSchool }}</p>
                                <h4 id="hero-preview-mobile-card-title" class="mt-2 text-base font-bold text-slate-900">{{ $previewCardTitle }}</h4>
                                <p id="hero-preview-mobile-card-text" class="mt-2 text-sm leading-6 text-slate-600">{{ $previewCardText }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Content Layer</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Slide message and positioning</h3>
                </div>
                <span class="inline-flex rounded-full bg-[#F3F0FB] px-3 py-1 text-xs font-semibold text-[#2D1D5C]">Left content column</span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Badge Text</label>
                    <input id="hero-badge-input" type="text" name="badge_text" value="{{ old('badge_text', $slide->badge_text) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="120" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Main Heading</label>
                    <textarea id="hero-title-input" name="title" rows="3" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="255" required>{{ old('title', $slide->title) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Keep this bold, direct, and easy to read within 2 to 4 short lines.</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Subtitle</label>
                    <textarea id="hero-subtitle-input" name="subtitle" rows="4" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="1500" required>{{ old('subtitle', $slide->subtitle) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Use this area to reassure parents and explain the value behind the slide headline.</p>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Call To Action</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Buttons and navigation flow</h3>
                </div>
                <span class="inline-flex rounded-full bg-[#F3F0FB] px-3 py-1 text-xs font-semibold text-[#2D1D5C]">Two CTA buttons</span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Button 1 Text</label>
                    <input id="hero-btn-one-text" type="text" name="button_1_text" value="{{ old('button_1_text', $slide->button_1_text) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="80" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Button 1 Link</label>
                    <input type="text" name="button_1_link" value="{{ old('button_1_link', $slide->button_1_link) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="255" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Button 2 Text</label>
                    <input id="hero-btn-two-text" type="text" name="button_2_text" value="{{ old('button_2_text', $slide->button_2_text) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="80" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Button 2 Link</label>
                    <input type="text" name="button_2_link" value="{{ old('button_2_link', $slide->button_2_link) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="255" required>
                </div>
            </div>

            <div class="mt-4 rounded-[1.5rem] border border-[#E7E2F4] bg-[#F8F7FD] p-4 text-sm text-slate-600">
                Use internal paths like <span class="font-semibold text-slate-900">/apply</span>, anchors like <span class="font-semibold text-slate-900">#programs</span>, or full links only when needed.
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Right Visual Card</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Floating message card</h3>
                </div>
                <span class="inline-flex rounded-full bg-[#F3F0FB] px-3 py-1 text-xs font-semibold text-[#2D1D5C]">Image overlay support</span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">School Name</label>
                    <input id="hero-school-input" type="text" name="school_name" value="{{ old('school_name', $slide->school_name ?: auth()->user()->school?->name) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="160" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Right Card Title</label>
                    <input id="hero-card-title-input" type="text" name="right_card_title" value="{{ old('right_card_title', $slide->right_card_title) }}" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="160" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Right Card Text</label>
                    <textarea id="hero-card-text-input" name="right_card_text" rows="4" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" maxlength="1000" required>{{ old('right_card_text', $slide->right_card_text) }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Visual Media</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Upload a premium slide image</h3>
                </div>
                <span id="hero-image-status" class="inline-flex rounded-full {{ $placeholderVisual ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                    {{ $placeholderVisual ? 'Placeholder image in use' : ($isEdit && $slide->image_path ? 'Custom image uploaded' : 'Awaiting upload') }}
                </span>
            </div>

            <div class="mt-6 space-y-5">
                <label class="block rounded-[1.6rem] border border-dashed border-[#BDB2DD] bg-[#F7F5FC] p-5 text-sm text-slate-600">
                    <span class="block text-sm font-semibold text-slate-900">Hero Image</span>
                    <span class="mt-2 block text-xs leading-6 text-slate-500">Best fit: upload a sharp landscape source image at 1800x1400 or larger, then keep important faces or buildings near the center because the homepage crops into a tall visual frame.</span>
                    <input id="hero-image-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-4 block w-full rounded-2xl border-slate-300 bg-white file:mr-4 file:rounded-full file:border-0 file:bg-[#2D1D5C] file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-[#43316F]" {{ $isEdit ? '' : 'required' }}>
                </label>

                <div class="grid gap-4 md:grid-cols-3">
                    <article class="rounded-[1.4rem] border border-[#E7E2F4] bg-[#F8F7FD] p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Source Ratio</p>
                        <div class="mt-3 flex items-center gap-3">
                            <div class="flex h-10 w-16 items-center justify-center rounded-lg bg-[#2D1D5C] text-[10px] font-bold uppercase tracking-[0.2em] text-white">3:2</div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Wide landscape photo</p>
                                <p class="text-xs text-slate-500">2000x1500 or 1800x1400 works well</p>
                            </div>
                        </div>
                    </article>
                    <article class="rounded-[1.4rem] border border-[#E7E2F4] bg-[#F8F7FD] p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Visible Crop</p>
                        <div class="mt-3 flex items-center gap-3">
                            <div class="flex h-12 w-10 items-center justify-center rounded-lg border-2 border-[#DFE753] bg-[#2D1D5C]/90"></div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Tall display frame</p>
                                <p class="text-xs text-slate-500">Homepage shows a narrow portrait crop</p>
                            </div>
                        </div>
                    </article>
                    <article class="rounded-[1.4rem] border border-[#E7E2F4] bg-[#F8F7FD] p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Safe Focus Area</p>
                        <div class="mt-3 flex items-center gap-3">
                            <div class="relative h-12 w-16 rounded-lg bg-[#2D1D5C]">
                                <div class="absolute inset-x-3 inset-y-1 rounded border border-dashed border-[#DFE753]"></div>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Keep subject centered</p>
                                <p class="text-xs text-slate-500">Avoid text or faces at edges</p>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="rounded-[1.5rem] border border-[#E7E2F4] bg-[#FBFAFE] p-5">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Cropping Guidance</p>
                            <h4 class="mt-2 text-lg font-bold text-slate-900">Upload like a homepage banner, not like a logo or flyer</h4>
                            <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">Choose a real classroom, campus, lab, event, or student-life image. Small logos, posters, and heavily text-based graphics will not feel premium inside the hero frame.</p>
                        </div>
                        <div class="rounded-[1.25rem] border border-[#2D1D5C]/10 bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Best Banner Rule</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">Keep your main subject inside the middle 60% of the source image.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Live Preview</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Homepage-style slide preview</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">This preview mirrors the public hero more closely, including the premium header shell, the text composition, and the right-side image card.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-[#F3F0FB] px-3 py-1 text-xs font-semibold text-[#2D1D5C]">Public hero match</span>
                    <div class="inline-flex rounded-full border border-[#D9D4EA] bg-[#F8F7FD] p-1">
                        <button type="button" id="hero-preview-desktop-tab" data-preview-target="desktop" class="rounded-full bg-[#2D1D5C] px-3 py-1.5 text-xs font-semibold text-white transition">Desktop</button>
                        <button type="button" id="hero-preview-mobile-tab" data-preview-target="mobile" class="rounded-full px-3 py-1.5 text-xs font-semibold text-[#2D1D5C] transition">Mobile</button>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-[2rem] border border-[#E3DEF0] bg-[#EEF6FF] p-4 shadow-inner">
                <div id="hero-preview-desktop" class="relative overflow-hidden rounded-[1.9rem] border border-[#D6DFF0] bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.82),transparent_38%),radial-gradient(circle_at_bottom_right,rgba(223,231,83,0.24),transparent_34%)] p-4 sm:p-5">
                    <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(rgba(45, 29, 92, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45, 29, 92, 0.08) 1px, transparent 1px); background-size: 30px 30px;"></div>

                    <div class="relative overflow-hidden rounded-[1.5rem] border border-white/60 bg-[#2D1D5C] px-4 py-3 shadow-[0_18px_45px_-24px_rgba(15,23,42,0.35)]">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 text-white">
                                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border border-white/10 bg-white/10 shadow-sm">
                                    <span id="hero-preview-school-badge" class="text-sm font-black tracking-[0.24em] text-[#DFE753]">{{ $previewSchoolInitials }}</span>
                                </div>
                                <span id="hero-preview-brand" class="font-display text-lg font-semibold tracking-tight text-white">{{ $previewSchool }}</span>
                            </div>
                            <div class="hidden items-center gap-2 md:flex">
                                <span class="inline-flex rounded-full border border-white/15 bg-white px-4 py-2 text-xs font-semibold text-[#2D1D5C]">Programs</span>
                                <span class="inline-flex rounded-full border border-white/15 bg-white px-4 py-2 text-xs font-semibold text-[#2D1D5C]">Admissions</span>
                                <span class="inline-flex rounded-full border border-white/15 bg-white px-4 py-2 text-xs font-semibold text-[#2D1D5C]">Academics</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-5 grid gap-6 lg:grid-cols-[1.02fr_0.98fr] lg:gap-8">
                        <div class="max-w-3xl pt-2">
                            <p id="hero-preview-badge" class="inline-flex items-center rounded-full border border-[#DFE753] bg-white/85 px-4 py-1.5 text-sm font-semibold tracking-tight text-[#2D1D5C] shadow-sm backdrop-blur">{{ $previewBadge }}</p>
                            <h1 id="hero-preview-title" class="mt-7 max-w-4xl font-[Georgia,serif] text-4xl font-semibold leading-[0.94] tracking-[-0.04em] text-slate-900 sm:text-5xl">{{ $previewTitle }}</h1>
                            <p id="hero-preview-subtitle" class="mt-6 max-w-2xl text-base leading-relaxed text-slate-600 sm:text-lg">{{ $previewSubtitle }}</p>

                            <div class="mt-8 flex flex-wrap gap-4">
                                <span id="hero-preview-btn-one" class="inline-flex items-center rounded-full bg-[#2D1D5C] px-7 py-3.5 text-sm font-bold text-white shadow-[0_18px_35px_-16px_rgba(15,23,42,0.35)]">{{ $previewButtonOne }}</span>
                                <span id="hero-preview-btn-two" class="inline-flex items-center rounded-full border border-[#2D1D5C]/30 bg-white/90 px-7 py-3.5 text-sm font-bold text-[#2D1D5C] shadow-sm backdrop-blur">{{ $previewButtonTwo }}</span>
                            </div>

                            <div class="mt-8 flex items-center gap-2">
                                <span class="h-2.5 w-7 rounded-full bg-[#2D1D5C]"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#2D1D5C]/25"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#2D1D5C]/25"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#2D1D5C]/25"></span>
                            </div>
                        </div>

                        <div class="relative lg:pl-4">
                            <div class="relative overflow-hidden rounded-[2rem] border border-[#2D1D5C]/10 bg-white shadow-[0_30px_80px_-35px_rgba(15,23,42,0.5)]">
                                <div class="relative aspect-[4/4.15] min-h-[24rem] overflow-hidden bg-[#24164A] sm:min-h-[28rem]">
                                    <img id="hero-preview-image" src="{{ $currentImage }}" alt="Hero preview" class="{{ $currentImage && !$placeholderVisual ? '' : 'hidden ' }}h-full w-full object-cover">
                                    <div id="hero-preview-crop-guide" class="{{ $currentImage && !$placeholderVisual ? '' : 'hidden ' }}absolute inset-0 pointer-events-none">
                                        <div class="absolute inset-x-[18%] inset-y-[8%] rounded-[1.4rem] border border-dashed border-[#DFE753]/80 bg-transparent shadow-[inset_0_0_0_1px_rgba(255,255,255,0.08)]"></div>
                                        <div class="absolute bottom-4 left-4 rounded-full bg-slate-950/60 px-3 py-1.5 text-[11px] font-semibold tracking-[0.18em] text-white/90 backdrop-blur">SAFE FOCUS AREA</div>
                                    </div>
                                    <div id="hero-preview-placeholder" class="{{ $currentImage && !$placeholderVisual ? 'hidden ' : '' }}absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_28%),linear-gradient(135deg,#201244_0%,#2D1D5C_45%,#4D3A87_100%)]"></div>
                                    <div id="hero-preview-placeholder-rings" class="{{ $currentImage && !$placeholderVisual ? 'hidden ' : '' }}absolute inset-0">
                                        <div class="absolute -right-10 top-8 h-44 w-44 rounded-full border border-white/10 bg-white/5 blur-sm"></div>
                                        <div class="absolute -left-12 bottom-8 h-36 w-36 rounded-full border border-[#DFE753]/35 bg-[#DFE753]/12 blur-sm"></div>
                                    </div>
                                    <div id="hero-preview-placeholder-copy" class="{{ $currentImage && !$placeholderVisual ? 'hidden ' : '' }}absolute inset-0 p-6 text-white">
                                        <div class="flex h-full flex-col justify-between">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85">Preview State</p>
                                                    <h4 class="mt-4 max-w-xs text-2xl font-black leading-tight">Upload a wide school image to improve this slide instantly</h4>
                                                </div>
                                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/15 bg-white/10 shadow-xl">
                                                    <span id="hero-preview-placeholder-badge" class="text-lg font-black tracking-[0.24em] text-[#DFE753]">{{ $previewSchoolInitials }}</span>
                                                </div>
                                            </div>
                                            <div class="rounded-[1.4rem] border border-white/12 bg-white/10 p-4 backdrop-blur">
                                                <p id="hero-preview-placeholder-text" class="text-sm leading-6 text-white/88">The homepage crops the image into a tall visual panel. Upload a sharp wide photo and keep the subject close to the center for the best result.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/30 via-slate-900/5 to-transparent"></div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
                                </div>
                            </div>

                            <div class="absolute -bottom-6 left-4 max-w-[18rem] rounded-[1.5rem] border border-[#2D1D5C]/15 bg-white/95 p-4 shadow-[0_24px_55px_-24px_rgba(15,23,42,0.4)] backdrop-blur sm:left-[-1.25rem] sm:p-5">
                                <p id="hero-preview-school" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/60">{{ $previewSchool }}</p>
                                <h2 id="hero-preview-card-title" class="mt-2 text-lg font-bold leading-tight text-slate-900">{{ $previewCardTitle }}</h2>
                                <p id="hero-preview-card-text" class="mt-2 text-sm leading-relaxed text-slate-600">{{ $previewCardText }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Publishing</p>
            <h3 class="mt-3 text-xl font-black text-slate-900">Order and visibility</h3>

            <div class="mt-6 space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Order</label>
                    <select name="order" class="w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-[#2D1D5C] focus:ring-[#2D1D5C]/20" required>
                        @for($i = 1; $i <= $maxSlides; $i++)
                            <option value="{{ $i }}" @selected((int) old('order', $slide->order ?: 1) === $i)>Slide {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <label class="flex items-start gap-3 rounded-[1.5rem] border border-[#E7E2F4] bg-[#F8F7FD] px-4 py-4">
                    <input type="checkbox" name="is_active" value="1" class="mt-1 rounded border-gray-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" @checked((bool) old('is_active', $slide->is_active ?? true))>
                    <span>
                        <span class="block text-sm font-semibold text-slate-900">Active on website</span>
                        <span class="mt-1 block text-sm leading-6 text-slate-500">Only active slides are shown in the homepage slider. Keep no more than four active slides for a clean premium rotation.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-full bg-[#2D1D5C] px-6 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                {{ $isEdit ? 'Update Slide' : 'Create Slide' }}
            </button>
            <a href="{{ route('system.hero-slides.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
                Cancel
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const fallbackValues = {
            badge: 'Standard and Industry Professional School',
            title: 'A modern learning environment for KG, Primary, and Secondary students',
            subtitle: 'Use this space to explain the value of the slide and guide families toward the right next action.',
            buttonOne: 'Start Admission',
            buttonTwo: 'Explore Programs',
            school: 'ChrizFasa Academy',
            cardTitle: 'School Highlights',
            cardText: 'A focused overlay message that supports the slide visual and strengthens the main story.',
        };

        const initialsFromName = (name) => {
            const parts = (name || '').trim().split(/\s+/).filter(Boolean).slice(0, 2);
            const initials = parts.map((part) => part.charAt(0)).join('').toUpperCase();
            return initials || 'CA';
        };

        const bindText = (inputId, previewId, fallback, callback) => {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            if (!input || !preview) return;
            const sync = () => {
                const value = input.value.trim();
                const resolved = value !== '' ? value : fallback;
                preview.textContent = resolved;
                if (typeof callback === 'function') {
                    callback(resolved);
                }
            };
            input.addEventListener('input', sync);
            sync();
        };

        bindText('hero-badge-input', 'hero-preview-badge', fallbackValues.badge);
        bindText('hero-badge-input', 'hero-preview-mobile-pill', fallbackValues.badge);
        bindText('hero-title-input', 'hero-preview-title', fallbackValues.title);
        bindText('hero-title-input', 'hero-preview-mobile-title', fallbackValues.title);
        bindText('hero-subtitle-input', 'hero-preview-subtitle', fallbackValues.subtitle);
        bindText('hero-subtitle-input', 'hero-preview-mobile-subtitle', fallbackValues.subtitle);
        bindText('hero-btn-one-text', 'hero-preview-btn-one', fallbackValues.buttonOne);
        bindText('hero-btn-one-text', 'hero-preview-mobile-btn-one', fallbackValues.buttonOne);
        bindText('hero-btn-two-text', 'hero-preview-btn-two', fallbackValues.buttonTwo);
        bindText('hero-btn-two-text', 'hero-preview-mobile-btn-two', fallbackValues.buttonTwo);
        bindText('hero-card-title-input', 'hero-preview-card-title', fallbackValues.cardTitle);
        bindText('hero-card-title-input', 'hero-preview-mobile-card-title', fallbackValues.cardTitle);
        bindText('hero-card-text-input', 'hero-preview-card-text', fallbackValues.cardText);
        bindText('hero-card-text-input', 'hero-preview-mobile-card-text', fallbackValues.cardText);
        bindText('hero-school-input', 'hero-preview-school', fallbackValues.school);
        bindText('hero-school-input', 'hero-preview-mobile-school', fallbackValues.school);
        bindText('hero-school-input', 'hero-preview-brand', fallbackValues.school);
        bindText('hero-school-input', 'hero-preview-mobile-brand', fallbackValues.school, (resolved) => {
            const badge = document.getElementById('hero-preview-school-badge');
            const mobileBadge = document.getElementById('hero-preview-mobile-badge');
            const placeholderBadge = document.getElementById('hero-preview-placeholder-badge');
            const initials = initialsFromName(resolved);
            if (badge) badge.textContent = initials;
            if (mobileBadge) mobileBadge.textContent = initials;
            if (placeholderBadge) placeholderBadge.textContent = initials;
        });

        const imageInput = document.getElementById('hero-image-input');
        const imagePreview = document.getElementById('hero-preview-image');
        const mobileImagePreview = document.getElementById('hero-preview-mobile-image');
        const cropGuide = document.getElementById('hero-preview-crop-guide');
        const mobileCropGuide = document.getElementById('hero-preview-mobile-crop-guide');
        const placeholder = document.getElementById('hero-preview-placeholder');
        const placeholderRings = document.getElementById('hero-preview-placeholder-rings');
        const placeholderCopy = document.getElementById('hero-preview-placeholder-copy');
        const mobilePlaceholder = document.getElementById('hero-preview-mobile-placeholder');
        const mobilePlaceholderCopy = document.getElementById('hero-preview-mobile-copy');
        const imageStatus = document.getElementById('hero-image-status');
        const desktopPreview = document.getElementById('hero-preview-desktop');
        const mobilePreview = document.getElementById('hero-preview-mobile');
        const desktopTab = document.getElementById('hero-preview-desktop-tab');
        const mobileTab = document.getElementById('hero-preview-mobile-tab');

        if (!imageInput || !imagePreview || !placeholder || !placeholderRings || !placeholderCopy || !desktopPreview || !mobilePreview || !desktopTab || !mobileTab) {
            return;
        }

        const activatePreview = (mode) => {
            const desktopActive = mode === 'desktop';
            desktopPreview.classList.toggle('hidden', !desktopActive);
            mobilePreview.classList.toggle('hidden', desktopActive);
            desktopTab.className = desktopActive
                ? 'rounded-full bg-[#2D1D5C] px-3 py-1.5 text-xs font-semibold text-white transition'
                : 'rounded-full px-3 py-1.5 text-xs font-semibold text-[#2D1D5C] transition';
            mobileTab.className = desktopActive
                ? 'rounded-full px-3 py-1.5 text-xs font-semibold text-[#2D1D5C] transition'
                : 'rounded-full bg-[#2D1D5C] px-3 py-1.5 text-xs font-semibold text-white transition';
        };

        desktopTab.addEventListener('click', () => activatePreview('desktop'));
        mobileTab.addEventListener('click', () => activatePreview('mobile'));
        activatePreview('desktop');

        const showPlaceholder = () => {
            imagePreview.classList.add('hidden');
            if (mobileImagePreview) mobileImagePreview.classList.add('hidden');
            if (cropGuide) cropGuide.classList.add('hidden');
            if (mobileCropGuide) mobileCropGuide.classList.add('hidden');
            placeholder.classList.remove('hidden');
            placeholderRings.classList.remove('hidden');
            placeholderCopy.classList.remove('hidden');
            if (mobilePlaceholder) mobilePlaceholder.classList.remove('hidden');
            if (mobilePlaceholderCopy) mobilePlaceholderCopy.classList.remove('hidden');
        };

        const showUploadedImage = (src) => {
            imagePreview.src = src;
            imagePreview.classList.remove('hidden');
            if (mobileImagePreview) {
                mobileImagePreview.src = src;
                mobileImagePreview.classList.remove('hidden');
            }
            if (cropGuide) cropGuide.classList.remove('hidden');
            if (mobileCropGuide) mobileCropGuide.classList.remove('hidden');
            placeholder.classList.add('hidden');
            placeholderRings.classList.add('hidden');
            placeholderCopy.classList.add('hidden');
            if (mobilePlaceholder) mobilePlaceholder.classList.add('hidden');
            if (mobilePlaceholderCopy) mobilePlaceholderCopy.classList.add('hidden');
        };

        imageInput.addEventListener('change', () => {
            const file = imageInput.files && imageInput.files[0];

            if (!file) {
                return;
            }

            if (imageStatus) {
                imageStatus.textContent = 'Previewing uploaded image';
                imageStatus.className = 'inline-flex rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-semibold';
            }

            const objectUrl = URL.createObjectURL(file);
            showUploadedImage(objectUrl);
        });

        if (!imagePreview.getAttribute('src') || imagePreview.classList.contains('hidden')) {
            showPlaceholder();
        }
    })();
</script>
@endpush
