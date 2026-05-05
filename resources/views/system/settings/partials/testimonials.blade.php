<form action="{{ route('settings.public-page') }}" method="POST" class="testimonials-settings-form space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="testimonials">

    <section class="testimonials-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="testimonials-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Testimonials Section Content</h2>
                <p class="mt-1 text-sm text-slate-600">Manage the public heading, intro copy, and slider labels shown on the homepage.</p>
            </div>
            <span class="testimonials-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Public Section</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-12">
            <div class="testimonials-admin-card testimonials-admin-card--blue rounded-2xl border p-5 shadow-sm xl:col-span-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Badge</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Testimonials Badge</label>
                    <input type="text" name="testimonials_badge_text" value="{{ old('testimonials_badge_text', $publicPage['testimonials_badge_text'] ?? 'Testimonials') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--amber rounded-2xl border p-5 shadow-sm xl:col-span-8">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Headline</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Testimonials Heading</label>
                    <input type="text" name="testimonials_heading" value="{{ old('testimonials_heading', $publicPage['testimonials_heading'] ?? 'What Parents and Student Say') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--green rounded-2xl border p-5 shadow-sm xl:col-span-12">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Intro Copy</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Testimonials Intro</label>
                    <textarea name="testimonials_subheading" rows="3" class="w-full rounded-2xl border-slate-300">{{ old('testimonials_subheading', $publicPage['testimonials_subheading'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--purple rounded-2xl border p-5 shadow-sm xl:col-span-6">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Slider</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Slider Title</label>
                    <input type="text" name="testimonials_slider_title" value="{{ old('testimonials_slider_title', $publicPage['testimonials_slider_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--teal rounded-2xl border p-5 shadow-sm xl:col-span-6">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Fallback Copy</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Empty Text</label>
                    <input type="text" name="testimonials_empty_text" value="{{ old('testimonials_empty_text', $publicPage['testimonials_empty_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="testimonials-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Submission Form Copy</h2>
                <p class="mt-1 text-sm text-slate-600">Edit labels and placeholders used by the testimonial form on the public homepage.</p>
            </div>
            <span class="testimonials-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Form UX</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-12">
            <div class="testimonials-admin-card testimonials-admin-card--amber rounded-2xl border p-5 shadow-sm xl:col-span-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Form Header</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Form Title</label>
                    <input type="text" name="testimonials_form_title" value="{{ old('testimonials_form_title', $publicPage['testimonials_form_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--blue rounded-2xl border p-5 shadow-sm xl:col-span-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Name Field</h3>
                <div class="mt-4 space-y-4">
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Name Label</label>
                        <input type="text" name="testimonials_form_name_label" value="{{ old('testimonials_form_name_label', $publicPage['testimonials_form_name_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Name Placeholder</label>
                        <input type="text" name="testimonials_form_name_placeholder" value="{{ old('testimonials_form_name_placeholder', $publicPage['testimonials_form_name_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--green rounded-2xl border p-5 shadow-sm xl:col-span-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Role Field</h3>
                <div class="mt-4 space-y-4">
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Role Label</label>
                        <input type="text" name="testimonials_form_role_label" value="{{ old('testimonials_form_role_label', $publicPage['testimonials_form_role_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Role Placeholder</label>
                        <input type="text" name="testimonials_form_role_placeholder" value="{{ old('testimonials_form_role_placeholder', $publicPage['testimonials_form_role_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--purple rounded-2xl border p-5 shadow-sm xl:col-span-6">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Form Controls</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Rating Label</label>
                        <input type="text" name="testimonials_form_rating_label" value="{{ old('testimonials_form_rating_label', $publicPage['testimonials_form_rating_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Submit Button Text</label>
                        <input type="text" name="testimonials_form_submit_text" value="{{ old('testimonials_form_submit_text', $publicPage['testimonials_form_submit_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="testimonials-admin-card testimonials-admin-card--teal rounded-2xl border p-5 shadow-sm xl:col-span-6">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Message Field</h3>
                <div class="mt-4 space-y-4">
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Message Label</label>
                        <input type="text" name="testimonials_form_message_label" value="{{ old('testimonials_form_message_label', $publicPage['testimonials_form_message_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="testimonials-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Message Placeholder</label>
                        <textarea name="testimonials_form_message_placeholder" rows="3" class="w-full rounded-2xl border-slate-300">{{ old('testimonials_form_message_placeholder', $publicPage['testimonials_form_message_placeholder'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="testimonials-admin-heading border-b pb-4">
            <h2 class="text-lg font-bold text-slate-900">Status Messages</h2>
            <p class="mt-1 text-sm text-slate-600">Configure confirmation and error feedback shown after testimonial submission.</p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">
            <div class="testimonials-admin-card testimonials-admin-card--green rounded-2xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Success Response</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Success Text</label>
                    <input type="text" name="testimonials_success_text" value="{{ old('testimonials_success_text', $publicPage['testimonials_success_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>
            <div class="testimonials-admin-card testimonials-admin-card--amber rounded-2xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Error Response</h3>
                <div class="mt-4 testimonials-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Error Text</label>
                    <input type="text" name="testimonials_error_text" value="{{ old('testimonials_error_text', $publicPage['testimonials_error_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            </div>
        </div>
    </section>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">Save Testimonials Page</button>
</form>

<style>
    .testimonials-admin-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(145deg, #f7fbff 0%, #eef5ff 48%, #f7fde7 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.34);
    }

    .testimonials-admin-panel::before {
        content: "";
        position: absolute;
        width: 360px;
        height: 360px;
        top: -190px;
        right: -120px;
        border-radius: 9999px;
        background: rgba(223, 231, 83, 0.3);
        filter: blur(58px);
        pointer-events: none;
    }

    .testimonials-admin-panel::after {
        content: "";
        position: absolute;
        width: 380px;
        height: 380px;
        bottom: -210px;
        left: -120px;
        border-radius: 9999px;
        background: rgba(59, 130, 246, 0.16);
        filter: blur(60px);
        pointer-events: none;
    }

    .testimonials-admin-panel > * {
        position: relative;
        z-index: 1;
    }

    .testimonials-admin-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .testimonials-admin-chip {
        border: 1px solid rgba(37, 51, 62, 0.2);
        background: linear-gradient(135deg, #ffffff 0%, #edf3ff 100%);
        color: #51657f;
    }

    .testimonials-admin-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.48);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .testimonials-admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.45);
    }

    .testimonials-admin-card--blue {
        border-top: 4px solid #3b82f6;
        background: linear-gradient(155deg, #ffffff 0%, #eff6ff 100%);
    }

    .testimonials-admin-card--amber {
        border-top: 4px solid #f59e0b;
        background: linear-gradient(155deg, #ffffff 0%, #fffbeb 100%);
    }

    .testimonials-admin-card--green {
        border-top: 4px solid #10b981;
        background: linear-gradient(155deg, #ffffff 0%, #ecfdf5 100%);
    }

    .testimonials-admin-card--purple {
        border-top: 4px solid #8b5cf6;
        background: linear-gradient(155deg, #ffffff 0%, #f5f3ff 100%);
    }

    .testimonials-admin-card--teal {
        border-top: 4px solid #06b6d4;
        background: linear-gradient(155deg, #ffffff 0%, #ecfeff 100%);
    }

    .testimonials-admin-field {
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.86);
        padding: 0.75rem 0.85rem;
    }

    .testimonials-settings-form input[type="text"] {
        min-height: 48px;
        padding: 11px 14px;
        font-size: 1rem;
        line-height: 1.5;
        color: #0f172a;
        background-color: #ffffff;
        border: 2px solid #c7d2e3;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .testimonials-settings-form textarea {
        min-height: 120px;
        padding: 12px 14px;
        font-size: 1rem;
        line-height: 1.65;
        color: #0f172a;
        background-color: #fff;
        border: 2px solid #c7d2e3;
    }

    .testimonials-admin-panel input:focus,
    .testimonials-admin-panel textarea:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        outline: none;
    }
</style>
