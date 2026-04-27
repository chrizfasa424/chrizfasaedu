@push('styles')
<style>
    .admissions-field {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.45);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.16), transparent 42%),
            linear-gradient(180deg, #ffffff 0%, #f8f7ff 100%);
        color: #0f172a;
        font-size: 0.93rem;
        line-height: 1.45;
        padding: 0.8rem 0.95rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .admissions-field::placeholder {
        color: #94a3b8;
        opacity: 1;
    }

    .admissions-field:focus,
    .admissions-field:focus-visible {
        outline: none;
        border-color: #2D1D5C;
        box-shadow:
            0 0 0 4px rgba(45, 29, 92, 0.14),
            0 16px 28px -22px rgba(15, 23, 42, 0.5);
    }

    .admissions-label {
        margin-bottom: 0.45rem;
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }
</style>
@endpush

<form id="admissions-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="admissions">
    <input type="hidden" name="refresh_editor_feed" value="1">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-gradient-to-r from-[#2D1D5C] via-[#3A2B73] to-[#1F1A4B] px-5 py-6 text-white sm:px-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#DFE753]">Admissions Studio</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight">Admissions Page Settings</h2>
            <p class="mt-2 max-w-3xl text-sm text-indigo-100">Refine your admissions narrative, process highlights, and call-to-action details from one workspace.</p>
        </div>

        <div class="space-y-6 p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <label class="admissions-label" for="admissions-label">Admissions Label</label>
                    <input id="admissions-label" type="text" name="admissions_label" value="{{ old('admissions_label', $publicPage['admissions_label'] ?? 'Admissions') }}" class="admissions-field">
                </div>
                <div>
                    <label class="admissions-label" for="admissions-process-label">Admissions Process Label</label>
                    <input id="admissions-process-label" type="text" name="admissions_process_label" value="{{ old('admissions_process_label', $publicPage['admissions_process_label'] ?? 'Admissions Process') }}" class="admissions-field">
                </div>
                <div class="lg:col-span-2">
                    <label class="admissions-label" for="admissions-intro">Admissions Intro</label>
                    <textarea id="admissions-intro" name="admissions_intro" rows="5" class="js-ck-editor admissions-field">{{ old('admissions_intro', $publicPage['admissions_intro'] ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-100 pb-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/70">Editor Feed</p>
            <h3 class="text-xl font-extrabold tracking-tight text-slate-900">Admissions Content Feed</h3>
            <p class="text-sm text-slate-500">Update the card feed and step-by-step flow shown on the public admissions page.</p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <div>
                    <label class="admissions-label" for="admissions-items-text">Admissions Items</label>
                    <textarea id="admissions-items-text" name="admissions_items_text" rows="11" class="admissions-field">{{ old('admissions_items_text', $admissionsItemsText) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">Use one line per item with this format: `Title | Description`.</p>
                </div>

                <div>
                    <label class="admissions-label" for="admission-steps">Admission Steps</label>
                    <textarea id="admission-steps" name="admission_steps" rows="9" class="admissions-field">{{ old('admission_steps', $admissionStepsText) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">One step per line in the order families should follow.</p>
                </div>
            </div>

            <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Call To Action Controls</p>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="admissions-label" for="visit-booking-button-text">Visit Booking Button Text</label>
                        <input id="visit-booking-button-text" type="text" name="visit_booking_button_text" value="{{ old('visit_booking_button_text', $publicPage['visit_booking_button_text'] ?? 'Visit Booking') }}" class="admissions-field">
                    </div>
                    <div>
                        <label class="admissions-label" for="quick-apply-button-text">Quick Apply Button Text</label>
                        <input id="quick-apply-button-text" type="text" name="quick_apply_button_text" value="{{ old('quick_apply_button_text', $publicPage['quick_apply_button_text'] ?? 'Apply Now') }}" class="admissions-field">
                    </div>
                    <div>
                        <label class="admissions-label" for="visit-booking-url">Visit Booking URL</label>
                        <input id="visit-booking-url" type="url" name="visit_booking_url" value="{{ old('visit_booking_url', $publicPage['visit_booking_url'] ?? '') }}" class="admissions-field" placeholder="https://">
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <div class="sticky bottom-3 z-10 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
        <button type="submit" data-admissions-save-button class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">
            Save Admissions Page
        </button>
    </div>
</form>

<section id="admissions-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'admissions',
        '_sectionLabel' => trim((string) ($publicPage['admissions_label'] ?? 'Admissions')),
        '_items'        => $publicPage['admissions'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('admissions-settings-form');
        if (!form) {
            return;
        }

        const saveButton = form.querySelector('[data-admissions-save-button]');
        form.addEventListener('submit', () => {
            if (!saveButton) {
                return;
            }

            saveButton.disabled = true;
            saveButton.classList.add('opacity-80', 'cursor-not-allowed');
            saveButton.textContent = 'Saving And Refreshing Feed...';
        });

        const currentUrl = new URL(window.location.href);
        const refreshToken = currentUrl.searchParams.get('editor_feed_refresh');
        if (!refreshToken) {
            return;
        }

        const feed = document.getElementById('admissions-editor-feed');
        if (feed) {
            feed.classList.add('ring-2', 'ring-[#2D1D5C]/20', 'rounded-3xl');
            setTimeout(() => {
                feed.classList.remove('ring-2', 'ring-[#2D1D5C]/20', 'rounded-3xl');
            }, 1800);
        }

        currentUrl.searchParams.delete('editor_feed_refresh');
        const search = currentUrl.searchParams.toString();
        const cleanUrl = `${currentUrl.pathname}${search ? `?${search}` : ''}${currentUrl.hash}`;
        window.history.replaceState({}, document.title, cleanUrl);
    })();
</script>
@endpush
