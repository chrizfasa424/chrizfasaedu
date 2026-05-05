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
        border-color: #25333E;
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
    <input type="hidden" name="admissions_process_label" value="{{ old('admissions_process_label', $publicPage['admissions_process_label'] ?? 'Admissions Process') }}">
    <input type="hidden" name="admissions_intro" value="{{ old('admissions_intro', strip_tags((string) ($publicPage['admissions_intro'] ?? ''))) }}">
    <input type="hidden" name="admissions_items_text" value="{{ old('admissions_items_text', $admissionsItemsText) }}">
    <input type="hidden" name="admission_steps" value="{{ old('admission_steps', $admissionStepsText) }}">
    <input type="hidden" name="visit_booking_url" value="{{ old('visit_booking_url', $publicPage['visit_booking_url'] ?? '') }}">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

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
            feed.classList.add('ring-2', 'ring-[#25333E]/20', 'rounded-3xl');
            setTimeout(() => {
                feed.classList.remove('ring-2', 'ring-[#25333E]/20', 'rounded-3xl');
            }, 1800);
        }

        currentUrl.searchParams.delete('editor_feed_refresh');
        const search = currentUrl.searchParams.toString();
        const cleanUrl = `${currentUrl.pathname}${search ? `?${search}` : ''}${currentUrl.hash}`;
        window.history.replaceState({}, document.title, cleanUrl);
    })();
</script>
@endpush
