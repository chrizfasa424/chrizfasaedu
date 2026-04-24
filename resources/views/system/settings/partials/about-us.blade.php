@push('styles')
<style>
    .teacher-marquee-input {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(45, 29, 92, 0.22);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.18), transparent 42%),
            linear-gradient(180deg, #ffffff 0%, #f9f7ff 100%);
        color: #0f172a;
        font-size: 1.05rem;
        line-height: 1.45;
        padding: 0.72rem 0.95rem;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.85),
            0 8px 18px -14px rgba(15, 23, 42, 0.35);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .teacher-marquee-input::placeholder {
        color: #7c8aa0;
        opacity: 1;
    }

    .teacher-marquee-input:hover {
        border-color: rgba(45, 29, 92, 0.34);
    }

    .teacher-marquee-input:focus,
    .teacher-marquee-input:focus-visible {
        outline: none;
        border-color: #2D1D5C;
        box-shadow:
            0 0 0 4px rgba(45, 29, 92, 0.16),
            0 14px 28px -18px rgba(45, 29, 92, 0.45);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.24), transparent 42%),
            linear-gradient(180deg, #ffffff 0%, #f6f3ff 100%);
    }

    .teacher-marquee-input-label {
        margin-bottom: 0.45rem;
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: #64748b;
    }

    .teacher-marquee-preview-shell {
        position: relative;
        height: 11rem;
        width: 100%;
        overflow: hidden;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.34);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.16), transparent 44%),
            linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .teacher-marquee-preview-image {
        height: 100%;
        width: 100%;
        object-fit: contain;
        background: #ffffff;
    }

    .teacher-marquee-preview-empty {
        display: flex;
        height: 100%;
        width: 100%;
        align-items: center;
        justify-content: center;
        border: 1px dashed rgba(148, 163, 184, 0.55);
        border-radius: 1rem;
        color: #94a3b8;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
</style>
@endpush

<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">About Us Label</label>
            <input type="text" name="about_label" value="{{ old('about_label', $publicPage['about_label'] ?? 'About Us') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">About Us Intro</label>
            <textarea name="about_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('about_intro', $publicPage['about_intro'] ?? '') }}</textarea>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">About Us Banner Manager</h2>
        <p class="mt-1 text-sm text-slate-500">Each card supports an image, bold title, and moderate supporting context.</p>
        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @for($i = 1; $i <= 6; $i++)
                @php $item = $aboutBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-bold text-slate-900">About Card {{ $i }}</p>
                    <input type="file" name="about_banner_{{ $i }}_image" accept="image/*" class="mt-3 w-full rounded-2xl border-slate-300">
                    <input type="text" name="about_banner_{{ $i }}_title" value="{{ old('about_banner_' . $i . '_title', $item['title'] ?? '') }}" class="mt-3 w-full rounded-2xl border-slate-300" placeholder="Card title">
                    <textarea name="about_banner_{{ $i }}_description" rows="4" class="js-ck-editor mt-3 w-full rounded-2xl border-slate-300" placeholder="Card context">{{ old('about_banner_' . $i . '_description', $item['description'] ?? '') }}</textarea>
                    <label class="mt-3 inline-flex items-center text-sm font-medium text-red-600">
                        <input type="checkbox" name="remove_about_banner_{{ $i }}" value="1" class="mr-2 rounded border-slate-300">
                        Remove current card image
                    </label>
                    <div class="mt-4">
                        @if(!empty($item['image']))
                            <img src="{{ asset('storage/' . ltrim($item['image'], '/')) }}" alt="About card {{ $i }}" class="h-40 w-full rounded-2xl border border-slate-200 object-cover">
                        @else
                            <div class="flex h-40 w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No image uploaded</div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Why Choose Us Section</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Section Label</label>
                <input type="text" name="why_choose_us_label" value="{{ old('why_choose_us_label', $publicPage['why_choose_us_label'] ?? 'Why Choose Us') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="lg:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Intro Text</label>
                <textarea name="why_choose_us_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('why_choose_us_intro', $publicPage['why_choose_us_intro'] ?? '') }}</textarea>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            @for($i = 1; $i <= 4; $i++)
                @php $item = $whyChooseUsBannerSlots[$i - 1] ?? ['image' => null, 'title' => '', 'description' => '']; @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-sm font-bold text-slate-900">Why Choose Us Card {{ $i }}</p>
                    <input type="file" name="why_banner_{{ $i }}_image" accept="image/*" class="mt-3 w-full rounded-2xl border-slate-300">
                    <input type="text" name="why_banner_{{ $i }}_title" value="{{ old('why_banner_' . $i . '_title', $item['title'] ?? '') }}" class="mt-3 w-full rounded-2xl border-slate-300" placeholder="Card title">
                    <textarea name="why_banner_{{ $i }}_description" rows="4" class="js-ck-editor mt-3 w-full rounded-2xl border-slate-300" placeholder="Card context">{{ old('why_banner_' . $i . '_description', $item['description'] ?? '') }}</textarea>
                    <label class="mt-3 inline-flex items-center text-sm font-medium text-red-600">
                        <input type="checkbox" name="remove_why_banner_{{ $i }}" value="1" class="mr-2 rounded border-slate-300">
                        Remove current card image
                    </label>
                    <div class="mt-4">
                        @if(!empty($item['image']))
                            <img src="{{ asset('storage/' . ltrim($item['image'], '/')) }}" alt="Why choose us card {{ $i }}" class="h-32 w-full rounded-2xl border border-slate-200 object-cover">
                        @else
                            <div class="flex h-32 w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-400">No image uploaded</div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Teachers Marquee (Homepage)</h2>
        <p class="mt-1 text-sm text-slate-500">This marquee displays between the Hero and Why Choose Us sections on the homepage.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Section Label</label>
                <input type="text" name="teachers_marquee_label" value="{{ old('teachers_marquee_label', $publicPage['teachers_marquee_label'] ?? 'Our Teachers') }}" class="teacher-marquee-input">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Section Heading</label>
                <input type="text" name="teachers_marquee_heading" value="{{ old('teachers_marquee_heading', $publicPage['teachers_marquee_heading'] ?? 'Meet Our Teaching Team') }}" class="teacher-marquee-input">
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Section Intro</label>
                <input type="text" name="teachers_marquee_intro" value="{{ old('teachers_marquee_intro', $publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.') }}" class="teacher-marquee-input">
            </div>
        </div>

        @php
            $teacherRows = old('teacher_marquee');
            if (!is_array($teacherRows)) {
                $teacherRows = collect($teacherMarqueeItems ?? [])
                    ->map(function ($item) {
                        return [
                            'name' => $item['name'] ?? '',
                            'role' => $item['role'] ?? '',
                            'existing_image' => $item['image'] ?? '',
                            'remove_image' => 0,
                            'remove_row' => 0,
                        ];
                    })
                    ->values()
                    ->all();
            }
            if (empty($teacherRows)) {
                $teacherRows = [[
                    'name' => '',
                    'role' => '',
                    'existing_image' => '',
                    'remove_image' => 0,
                    'remove_row' => 0,
                ]];
            }
        @endphp

        <div class="mt-4 space-y-4" data-teacher-marquee-list data-next-index="{{ count($teacherRows) }}">
            @foreach($teacherRows as $index => $row)
                @php
                    $existingImage = trim((string) ($row['existing_image'] ?? ''));
                    $removeImageChecked = !empty($row['remove_image']);
                    $removeRowChecked = !empty($row['remove_row']);
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4" data-teacher-marquee-row data-existing-preview-url="{{ $existingImage !== '' ? asset('storage/' . ltrim($existingImage, '/')) : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-bold text-slate-900">Teacher {{ $loop->iteration }}</p>
                        <label class="inline-flex items-center text-xs font-medium text-red-600">
                            <input type="hidden" name="teacher_marquee[{{ $index }}][remove_row]" value="0">
                            <input type="checkbox" name="teacher_marquee[{{ $index }}][remove_row]" value="1" {{ $removeRowChecked ? 'checked' : '' }} class="mr-2 rounded border-slate-300">
                            Remove teacher row
                        </label>
                    </div>

                    <input type="hidden" name="teacher_marquee[{{ $index }}][existing_image]" value="{{ $existingImage }}">

                    <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div class="lg:col-span-1">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Photo</label>
                            <input type="file" name="teacher_marquee[{{ $index }}][image]" accept="image/*" class="w-full rounded-2xl border-slate-300" data-teacher-image-input>
                            <label class="mt-3 inline-flex items-center text-xs font-medium text-red-600">
                                <input type="hidden" name="teacher_marquee[{{ $index }}][remove_image]" value="0">
                                <input type="checkbox" name="teacher_marquee[{{ $index }}][remove_image]" value="1" {{ $removeImageChecked ? 'checked' : '' }} class="mr-2 rounded border-slate-300" data-teacher-remove-image-toggle>
                                Remove current image
                            </label>
                        </div>
                        <div class="lg:col-span-2 grid grid-cols-1 gap-3">
                            <div>
                                <label class="teacher-marquee-input-label">Name</label>
                                <input type="text" name="teacher_marquee[{{ $index }}][name]" value="{{ $row['name'] ?? '' }}" class="teacher-marquee-input" placeholder="Teacher name">
                            </div>
                            <div>
                                <label class="teacher-marquee-input-label">Role / Subject (Optional)</label>
                                <input type="text" name="teacher_marquee[{{ $index }}][role]" value="{{ $row['role'] ?? '' }}" class="teacher-marquee-input" placeholder="Role or subject">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 teacher-marquee-preview-shell" data-teacher-image-preview-shell>
                        <img
                            src="{{ $existingImage !== '' ? asset('storage/' . ltrim($existingImage, '/')) : '' }}"
                            alt="Teacher {{ $loop->iteration }}"
                            class="teacher-marquee-preview-image{{ $existingImage === '' ? ' hidden' : '' }}"
                            data-teacher-image-preview
                        >
                        <div class="teacher-marquee-preview-empty{{ $existingImage !== '' ? ' hidden' : '' }}" data-teacher-image-placeholder>No image uploaded</div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" data-add-teacher-marquee-row class="mt-4 inline-flex items-center rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
            Add Another Teacher
        </button>

        <template id="teacher-marquee-row-template">
            <div class="rounded-2xl border border-slate-200 bg-white p-4" data-teacher-marquee-row data-existing-preview-url="">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-bold text-slate-900">Teacher __DISPLAY_INDEX__</p>
                    <label class="inline-flex items-center text-xs font-medium text-red-600">
                        <input type="hidden" name="teacher_marquee[__INDEX__][remove_row]" value="0">
                        <input type="checkbox" name="teacher_marquee[__INDEX__][remove_row]" value="1" class="mr-2 rounded border-slate-300">
                        Remove teacher row
                    </label>
                </div>

                <input type="hidden" name="teacher_marquee[__INDEX__][existing_image]" value="">

                <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Photo</label>
                        <input type="file" name="teacher_marquee[__INDEX__][image]" accept="image/*" class="w-full rounded-2xl border-slate-300" data-teacher-image-input>
                        <label class="mt-3 inline-flex items-center text-xs font-medium text-red-600">
                            <input type="hidden" name="teacher_marquee[__INDEX__][remove_image]" value="0">
                            <input type="checkbox" name="teacher_marquee[__INDEX__][remove_image]" value="1" class="mr-2 rounded border-slate-300" data-teacher-remove-image-toggle>
                            Remove current image
                        </label>
                    </div>
                    <div class="lg:col-span-2 grid grid-cols-1 gap-3">
                        <div>
                            <label class="teacher-marquee-input-label">Name</label>
                            <input type="text" name="teacher_marquee[__INDEX__][name]" value="" class="teacher-marquee-input" placeholder="Teacher name">
                        </div>
                        <div>
                            <label class="teacher-marquee-input-label">Role / Subject (Optional)</label>
                            <input type="text" name="teacher_marquee[__INDEX__][role]" value="" class="teacher-marquee-input" placeholder="Role or subject">
                        </div>
                    </div>
                </div>

                <div class="mt-4 teacher-marquee-preview-shell" data-teacher-image-preview-shell>
                    <img src="" alt="Teacher preview" class="teacher-marquee-preview-image hidden" data-teacher-image-preview>
                    <div class="teacher-marquee-preview-empty" data-teacher-image-placeholder>No image uploaded</div>
                </div>
            </div>
        </template>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Legal Pages (Privacy and Cookies)</h2>
        <p class="mt-1 text-sm text-slate-500">Edit the public Privacy Policy, Cookies Policy, and cookie popup text shown to website visitors.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Effective Date</label>
                <input
                    type="text"
                    name="legal_effective_date"
                    value="{{ old('legal_effective_date', $publicPage['legal_effective_date'] ?? '') }}"
                    class="w-full rounded-2xl border-slate-300"
                    placeholder="e.g. April 24, 2026"
                >
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Cookie Banner Title</label>
                <input
                    type="text"
                    name="cookie_banner_title"
                    value="{{ old('cookie_banner_title', $publicPage['cookie_banner_title'] ?? 'Cookie Notice') }}"
                    class="w-full rounded-2xl border-slate-300"
                >
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Cookie Banner Accept Button</label>
                <input
                    type="text"
                    name="cookie_banner_accept_text"
                    value="{{ old('cookie_banner_accept_text', $publicPage['cookie_banner_accept_text'] ?? 'Accept Cookies') }}"
                    class="w-full rounded-2xl border-slate-300"
                >
            </div>
            <div class="lg:col-span-3">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Cookie Banner Message</label>
                <textarea name="cookie_banner_text" rows="3" class="w-full rounded-2xl border-slate-300" placeholder="Cookie notice text shown on first visit.">{{ old('cookie_banner_text', $publicPage['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.') }}</textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Cookie Banner Reject Button</label>
                <input
                    type="text"
                    name="cookie_banner_reject_text"
                    value="{{ old('cookie_banner_reject_text', $publicPage['cookie_banner_reject_text'] ?? 'Reject Optional') }}"
                    class="w-full rounded-2xl border-slate-300"
                >
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-slate-700">Privacy Policy Page</h3>
                <div class="mt-3 space-y-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Privacy Title</label>
                        <input type="text" name="privacy_policy_title" value="{{ old('privacy_policy_title', $publicPage['privacy_policy_title'] ?? 'Privacy Policy') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Privacy Intro</label>
                        <textarea name="privacy_policy_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('privacy_policy_intro', $publicPage['privacy_policy_intro'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Privacy Content</label>
                        <textarea name="privacy_policy_content" rows="10" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('privacy_policy_content', $publicPage['privacy_policy_content'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-slate-700">Cookies Policy Page</h3>
                <div class="mt-3 space-y-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Cookies Title</label>
                        <input type="text" name="cookies_policy_title" value="{{ old('cookies_policy_title', $publicPage['cookies_policy_title'] ?? 'Cookies Policy') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Cookies Intro</label>
                        <textarea name="cookies_policy_intro" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('cookies_policy_intro', $publicPage['cookies_policy_intro'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Cookies Content</label>
                        <textarea name="cookies_policy_content" rows="10" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('cookies_policy_content', $publicPage['cookies_policy_content'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save About Us Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'about',
    '_sectionLabel' => trim((string) ($publicPage['about_label'] ?? 'About Us')),
    '_items'        => $publicPage['about'] ?? [],
    '_publicPage'   => $publicPage,
])

@push('scripts')
<script>
    (() => {
        const addButton = document.querySelector('[data-add-teacher-marquee-row]');
        const list = document.querySelector('[data-teacher-marquee-list]');
        const template = document.getElementById('teacher-marquee-row-template');

        const bindTeacherRowPreview = (row) => {
            if (!row || row.dataset.previewBound === '1') {
                return;
            }

            row.dataset.previewBound = '1';

            const input = row.querySelector('[data-teacher-image-input]');
            const removeImageToggle = row.querySelector('[data-teacher-remove-image-toggle]');
            const preview = row.querySelector('[data-teacher-image-preview]');
            const placeholder = row.querySelector('[data-teacher-image-placeholder]');
            const existingUrl = (row.dataset.existingPreviewUrl || '').trim();

            if (!input || !preview || !placeholder) {
                return;
            }

            const showPreview = (src) => {
                preview.src = src;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };

            const showPlaceholder = () => {
                preview.src = '';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            };

            const refresh = () => {
                const file = input.files && input.files[0] ? input.files[0] : null;

                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (event.target && typeof event.target.result === 'string') {
                            showPreview(event.target.result);
                        }
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                if (removeImageToggle && removeImageToggle.checked) {
                    showPlaceholder();
                    return;
                }

                if (existingUrl !== '') {
                    showPreview(existingUrl);
                    return;
                }

                showPlaceholder();
            };

            input.addEventListener('change', () => {
                if (input.files && input.files.length > 0 && removeImageToggle) {
                    removeImageToggle.checked = false;
                }
                refresh();
            });

            if (removeImageToggle) {
                removeImageToggle.addEventListener('change', refresh);
            }

            refresh();
        };

        const bindAllTeacherRowPreviews = () => {
            document.querySelectorAll('[data-teacher-marquee-row]').forEach(bindTeacherRowPreview);
        };

        bindAllTeacherRowPreviews();

        if (!addButton || !list || !template) {
            return;
        }

        addButton.addEventListener('click', () => {
            const nextIndex = Number(list.dataset.nextIndex || list.children.length || 0);
            const displayIndex = list.querySelectorAll('[data-teacher-marquee-row]').length + 1;
            const html = template.innerHTML
                .replaceAll('__INDEX__', String(nextIndex))
                .replaceAll('__DISPLAY_INDEX__', String(displayIndex));

            const fragment = document.createRange().createContextualFragment(html.trim());
            list.appendChild(fragment);
            list.dataset.nextIndex = String(nextIndex + 1);
            bindAllTeacherRowPreviews();
        });
    })();
</script>
@endpush
