<form id="about-settings-form" action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="about-us">
    <input type="hidden" name="about_label" value="{{ old('about_label', $publicPage['about_label'] ?? 'About Us') }}">
    <input type="hidden" name="about_intro" value="{{ old('about_intro', strip_tags((string) ($publicPage['about_intro'] ?? ''))) }}">
    <input type="hidden" name="about_items_text" value="{{ old('about_items_text', $aboutItemsText ?? '') }}">
    <input type="hidden" name="why_choose_us_label" value="{{ old('why_choose_us_label', $publicPage['why_choose_us_label'] ?? 'Why Choose Us') }}">
    <input type="hidden" name="why_choose_us_intro" value="{{ old('why_choose_us_intro', strip_tags((string) ($publicPage['why_choose_us_intro'] ?? ''))) }}">
    <input type="hidden" name="why_choose_us" value="{{ old('why_choose_us', $whyChooseUsText ?? '') }}">
    <input type="hidden" name="teachers_marquee_label" value="{{ old('teachers_marquee_label', $publicPage['teachers_marquee_label'] ?? 'Our Qualified Teachers') }}">
    <input type="hidden" name="teachers_marquee_heading" value="{{ old('teachers_marquee_heading', $publicPage['teachers_marquee_heading'] ?? 'Our Qualified Teachers') }}">
    <input type="hidden" name="teachers_marquee_intro" value="{{ old('teachers_marquee_intro', $publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.') }}">
    <input type="hidden" name="legal_effective_date" value="{{ old('legal_effective_date', $publicPage['legal_effective_date'] ?? '') }}">
    <input type="hidden" name="privacy_policy_title" value="{{ old('privacy_policy_title', $publicPage['privacy_policy_title'] ?? 'Privacy Policy') }}">
    <input type="hidden" name="privacy_policy_intro" value="{{ old('privacy_policy_intro', strip_tags((string) ($publicPage['privacy_policy_intro'] ?? ''))) }}">
    <input type="hidden" name="privacy_policy_content" value="{{ old('privacy_policy_content', strip_tags((string) ($publicPage['privacy_policy_content'] ?? ''))) }}">
    <input type="hidden" name="cookies_policy_title" value="{{ old('cookies_policy_title', $publicPage['cookies_policy_title'] ?? 'Cookies Policy') }}">
    <input type="hidden" name="cookies_policy_intro" value="{{ old('cookies_policy_intro', strip_tags((string) ($publicPage['cookies_policy_intro'] ?? ''))) }}">
    <input type="hidden" name="cookies_policy_content" value="{{ old('cookies_policy_content', strip_tags((string) ($publicPage['cookies_policy_content'] ?? ''))) }}">
    <input type="hidden" name="cookie_banner_title" value="{{ old('cookie_banner_title', $publicPage['cookie_banner_title'] ?? 'Cookie Notice') }}">
    <input type="hidden" name="cookie_banner_text" value="{{ old('cookie_banner_text', $publicPage['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.') }}">
    <input type="hidden" name="cookie_banner_accept_text" value="{{ old('cookie_banner_accept_text', $publicPage['cookie_banner_accept_text'] ?? 'Accept Cookies') }}">
    <input type="hidden" name="cookie_banner_reject_text" value="{{ old('cookie_banner_reject_text', $publicPage['cookie_banner_reject_text'] ?? 'Reject Optional') }}">

    @php
        $aboutTeacherRows = old('teacher_marquee', $teacherMarqueeItems ?? []);
        if (empty($aboutTeacherRows)) {
            $aboutTeacherRows = [
                ['existing_image' => '', 'name' => '', 'role' => ''],
            ];
        }
    @endphp

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Our Qualified Teachers</h2>
                <p class="mt-1 text-sm text-slate-500">Upload teacher photos and details shown on the homepage cards.</p>
            </div>
            <button
                type="button"
                data-add-teacher-card
                class="inline-flex items-center rounded-xl border border-dashed border-[#25333E] bg-white px-3 py-2 text-xs font-semibold text-[#25333E] transition hover:bg-[#25333E] hover:text-white"
            >
                Add Teacher
            </button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" data-teacher-list data-next-index="{{ count($aboutTeacherRows) }}">
            @foreach($aboutTeacherRows as $index => $row)
                @php
                    $existingImage = trim((string) ($row['existing_image'] ?? ($row['image'] ?? '')));
                    $name = trim((string) ($row['name'] ?? ''));
                    $role = trim((string) ($row['role'] ?? ''));
                    $removeImage = !empty($row['remove_image']);
                    $removeRow = !empty($row['remove_row']);
                    $normalizedExistingImage = str_replace('\\', '/', ltrim($existingImage, '/'));
                    if (\Illuminate\Support\Str::startsWith($normalizedExistingImage, 'storage/')) {
                        $normalizedExistingImage = \Illuminate\Support\Str::after($normalizedExistingImage, 'storage/');
                    }
                    $normalizedExistingImage = ltrim($normalizedExistingImage, '/');
                    $existingImageUrl = '';
                    if ($existingImage !== '' && $normalizedExistingImage !== '') {
                        $existingImageUrl = \App\Support\MediaAsset::url($normalizedExistingImage) ?? '';
                    }
                @endphp
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $removeRow ? 'hidden' : '' }}"
                    data-teacher-card
                    data-existing-image-url="{{ $existingImageUrl }}"
                >
                    <input type="hidden" name="teacher_marquee[{{ $index }}][existing_image]" value="{{ $existingImage }}">
                    <input type="hidden" name="teacher_marquee[{{ $index }}][remove_image]" value="{{ $removeImage ? '1' : '0' }}" data-teacher-remove-image-input>
                    <input type="hidden" name="teacher_marquee[{{ $index }}][remove_row]" value="{{ $removeRow ? '1' : '0' }}" data-teacher-remove-row-input>

                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-900">Teacher {{ $loop->iteration }}</p>
                        <button
                            type="button"
                            data-remove-teacher-card
                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                        >
                            Remove
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Photo</label>
                            <input type="file" name="teacher_marquee[{{ $index }}][image]" accept="image/*" class="w-full rounded-xl border-slate-300" data-teacher-image-input>
                            <label class="mt-2 inline-flex items-center text-xs font-medium text-red-600">
                                <input type="checkbox" class="mr-2 rounded border-slate-300" data-teacher-remove-image-toggle {{ $removeImage ? 'checked' : '' }}>
                                Remove current image
                            </label>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Teacher Name</label>
                            <input type="text" name="teacher_marquee[{{ $index }}][name]" value="{{ $name }}" class="w-full rounded-xl border-slate-300" placeholder="e.g. Rosy Janner">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Role</label>
                            <input type="text" name="teacher_marquee[{{ $index }}][role]" value="{{ $role }}" class="w-full rounded-xl border-slate-300" placeholder="e.g. Senior Finance Lecturer">
                        </div>
                    </div>

                    <div class="mt-4 flex justify-center">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50" style="width: 200px; height: 200px;">
                            <img
                                src="{{ $existingImageUrl }}"
                                alt="Teacher preview"
                                class="h-full w-full object-cover {{ $existingImage === '' || $removeImage ? 'hidden' : '' }}"
                                style="object-position: center top;"
                                data-teacher-image-preview
                            >
                            <div class="flex h-full w-full items-center justify-center text-center text-xs font-semibold uppercase tracking-[0.1em] text-slate-400 {{ $existingImage !== '' && !$removeImage ? 'hidden' : '' }}" data-teacher-image-placeholder>
                                No image uploaded
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template id="teacher-card-template">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-teacher-card data-existing-image-url="">
                <input type="hidden" name="teacher_marquee[__INDEX__][existing_image]" value="">
                <input type="hidden" name="teacher_marquee[__INDEX__][remove_image]" value="0" data-teacher-remove-image-input>
                <input type="hidden" name="teacher_marquee[__INDEX__][remove_row]" value="0" data-teacher-remove-row-input>

                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-900">Teacher __DISPLAY_INDEX__</p>
                    <button type="button" data-remove-teacher-card class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                        Remove
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Photo</label>
                        <input type="file" name="teacher_marquee[__INDEX__][image]" accept="image/*" class="w-full rounded-xl border-slate-300" data-teacher-image-input>
                        <label class="mt-2 inline-flex items-center text-xs font-medium text-red-600">
                            <input type="checkbox" class="mr-2 rounded border-slate-300" data-teacher-remove-image-toggle>
                            Remove current image
                        </label>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Teacher Name</label>
                        <input type="text" name="teacher_marquee[__INDEX__][name]" value="" class="w-full rounded-xl border-slate-300" placeholder="e.g. Rosy Janner">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Role</label>
                        <input type="text" name="teacher_marquee[__INDEX__][role]" value="" class="w-full rounded-xl border-slate-300" placeholder="e.g. Senior Finance Lecturer">
                    </div>
                </div>

                <div class="mt-4 flex justify-center">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50" style="width: 200px; height: 200px;">
                        <img src="" alt="Teacher preview" class="hidden h-full w-full object-cover" style="object-position: center top;" data-teacher-image-preview>
                        <div class="flex h-full w-full items-center justify-center text-center text-xs font-semibold uppercase tracking-[0.1em] text-slate-400" data-teacher-image-placeholder>
                            No image uploaded
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </section>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">
        Save Our Qualified Teachers
    </button>
</form>

<section id="about-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'about',
        '_sectionLabel' => trim((string) ($publicPage['about_label'] ?? 'About Us')),
        '_items'        => $publicPage['about'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>

@push('scripts')
<script>
    (() => {
        const list = document.querySelector('[data-teacher-list]');
        const template = document.getElementById('teacher-card-template');
        const addButton = document.querySelector('[data-add-teacher-card]');

        if (!list || !template || !addButton) {
            return;
        }

        const setImagePreviewState = (row, source) => {
            const preview = row.querySelector('[data-teacher-image-preview]');
            const placeholder = row.querySelector('[data-teacher-image-placeholder]');

            if (!preview || !placeholder) {
                return;
            }

            if (source) {
                preview.src = source;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                return;
            }

            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        };

        const bindRow = (row) => {
            if (!row || row.dataset.bound === '1') {
                return;
            }

            row.dataset.bound = '1';

            const input = row.querySelector('[data-teacher-image-input]');
            const removeImageToggle = row.querySelector('[data-teacher-remove-image-toggle]');
            const removeImageInput = row.querySelector('[data-teacher-remove-image-input]');
            const removeRowInput = row.querySelector('[data-teacher-remove-row-input]');
            const removeRowButton = row.querySelector('[data-remove-teacher-card]');
            const existingImageUrl = (row.dataset.existingImageUrl || '').trim();

            const refresh = () => {
                const hasFile = input && input.files && input.files.length > 0;

                if (removeImageInput && removeImageToggle) {
                    removeImageInput.value = removeImageToggle.checked ? '1' : '0';
                }

                if (hasFile) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (event.target && typeof event.target.result === 'string') {
                            setImagePreviewState(row, event.target.result);
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                    return;
                }

                if (removeImageToggle && removeImageToggle.checked) {
                    setImagePreviewState(row, '');
                    return;
                }

                setImagePreviewState(row, existingImageUrl);
            };

            if (input) {
                input.addEventListener('change', () => {
                    if (removeImageToggle && input.files && input.files.length > 0) {
                        removeImageToggle.checked = false;
                    }
                    refresh();
                });
            }

            if (removeImageToggle) {
                removeImageToggle.addEventListener('change', refresh);
            }

            if (removeRowButton) {
                removeRowButton.addEventListener('click', () => {
                    if (removeRowInput) {
                        removeRowInput.value = '1';
                    }
                    row.classList.add('hidden');
                });
            }

            refresh();
        };

        const bindAllRows = () => {
            list.querySelectorAll('[data-teacher-card]').forEach(bindRow);
        };

        bindAllRows();

        addButton.addEventListener('click', () => {
            const nextIndex = Number(list.dataset.nextIndex || 0);
            const visibleCount = Array.from(list.querySelectorAll('[data-teacher-card]')).filter((item) => !item.classList.contains('hidden')).length;
            const displayIndex = visibleCount + 1;
            const html = template.innerHTML
                .replaceAll('__INDEX__', String(nextIndex))
                .replaceAll('__DISPLAY_INDEX__', String(displayIndex));

            const fragment = document.createRange().createContextualFragment(html.trim());
            list.appendChild(fragment);
            list.dataset.nextIndex = String(nextIndex + 1);
            bindAllRows();
        });
    })();
</script>
@endpush
