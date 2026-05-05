<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="contact-settings-form space-y-8">
    @csrf
    @method('PUT')

    <section class="content-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="content-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Contact Page Content</h2>
                <p class="mt-1 text-sm text-slate-600">Refine hero copy and message framing for the public Contact page.</p>
            </div>
            <span class="content-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Public Hero</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="content-admin-card content-admin-card--blue rounded-2xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Identity Copy</h3>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <div class="content-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Browser Title</label>
                        <input type="text" name="contact_page_browser_title" value="{{ old('contact_page_browser_title', $publicPage['contact_page_browser_title'] ?? 'Contact Us') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="content-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Badge Text</label>
                        <input type="text" name="contact_page_badge_text" value="{{ old('contact_page_badge_text', $publicPage['contact_page_badge_text'] ?? 'Contact Us') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="content-admin-card content-admin-card--amber rounded-2xl border p-5 shadow-sm xl:col-span-2">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Hero Messaging</h3>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <div class="content-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Page Heading</label>
                        <input type="text" name="contact_page_heading" value="{{ old('contact_page_heading', $publicPage['contact_page_heading'] ?? 'We are here to help you') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="content-admin-card content-admin-card--green rounded-2xl border p-5 shadow-sm xl:col-span-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Support Message</h3>
                <div class="mt-4 content-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Page Subheading</label>
                    <textarea name="contact_page_subheading" rows="4" class="w-full rounded-2xl border-slate-300 bg-white text-slate-700 leading-relaxed">{{ old('contact_page_subheading', strip_tags((string) ($publicPage['contact_page_subheading'] ?? ''))) }}</textarea>
                </div>
            </div>

            @php
                $contactHeroImage = trim((string) ($publicPage['contact_hero_image'] ?? ''));
            @endphp
            <div class="content-admin-card content-admin-card--purple rounded-2xl border p-5 shadow-sm xl:col-span-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Contact Hero Image</h3>
                <p class="mt-1 text-sm text-slate-600">Upload the full-width hero image displayed at the top of the public Contact page.</p>

                <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                        @if($contactHeroImage !== '')
                            <img src="{{ asset('storage/' . ltrim($contactHeroImage, '/')) }}" alt="Current contact hero image" class="h-48 w-full object-cover">
                        @else
                            <div class="flex h-48 w-full items-center justify-center text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">No image uploaded</div>
                        @endif
                    </div>

                    <div class="content-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Upload Image</label>
                        <input type="file" name="contact_hero_image" accept="image/*" class="w-full rounded-2xl border-slate-300 bg-white file:mr-3 file:rounded-xl file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                        <p class="mt-2 text-xs text-slate-600">Recommended: at least 1600 x 700px. JPG or PNG. Max 6MB.</p>

                        @if($contactHeroImage !== '')
                            <label class="mt-4 inline-flex items-center text-sm font-medium text-rose-700">
                                <input type="hidden" name="remove_contact_hero_image" value="0">
                                <input type="checkbox" name="remove_contact_hero_image" value="1" class="mr-2 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                Remove current hero image
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="contact-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Contact Details and Labels</h2>
                <p class="mt-1 text-sm text-slate-600">Control the contact information language and fallback copy shown on the public Contact page.</p>
            </div>
            <span class="contact-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Frontend Sync</span>
        </div>

        <input type="hidden" name="contact_info_title" value="{{ old('contact_info_title', $publicPage['contact_info_title'] ?? '') }}">
        <input type="hidden" name="contact_more_details_title" value="{{ old('contact_more_details_title', $publicPage['contact_more_details_title'] ?? '') }}">
        <input type="hidden" name="contact_not_provided_text" value="{{ old('contact_not_provided_text', $publicPage['contact_not_provided_text'] ?? '') }}">
        <input type="hidden" name="menu_overview_suffix" value="{{ old('menu_overview_suffix', $publicPage['menu_overview_suffix'] ?? 'Overview') }}">

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="contact-admin-card contact-admin-card--labels rounded-2xl border p-5 shadow-sm xl:col-span-2">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Contact Labels</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="contact-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Phone Label</label>
                        <input type="text" name="contact_phone_label" value="{{ old('contact_phone_label', $publicPage['contact_phone_label'] ?? 'Phone') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="contact-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">WhatsApp Label</label>
                        <input type="text" name="contact_whatsapp_label" value="{{ old('contact_whatsapp_label', $publicPage['contact_whatsapp_label'] ?? 'WhatsApp') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="contact-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Email Label</label>
                        <input type="text" name="contact_email_label" value="{{ old('contact_email_label', $publicPage['contact_email_label'] ?? 'Email') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="contact-admin-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Address Label</label>
                        <input type="text" name="contact_address_label" value="{{ old('contact_address_label', $publicPage['contact_address_label'] ?? 'Address') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="contact-admin-card contact-admin-card--live rounded-2xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Live Contact Value</h3>
                <div class="mt-4 contact-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">WhatsApp Number</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $publicPage['whatsapp'] ?? '') }}" class="w-full rounded-2xl border-slate-300" placeholder="+234...">
                </div>
            </div>

            <div class="contact-admin-card contact-admin-card--map rounded-2xl border p-5 shadow-sm xl:col-span-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Map Integration</h3>
                <div class="mt-4 contact-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Map Embed URL</label>
                    <input type="text" name="map_embed_url" value="{{ old('map_embed_url', $publicPage['map_embed_url'] ?? '') }}" class="w-full rounded-2xl border-slate-300" placeholder="Paste Google Maps URL or full iframe embed code">
                    <p class="mt-2 text-xs text-slate-600">Supports both direct map URL and full iframe embed code. The system will auto-extract the map link.</p>
                </div>
            </div>
        </div>
    </section>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">Save Contact Page</button>
</form>

<style>
    .content-admin-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(140deg, #f7fbff 0%, #f8fbff 45%, #fdfce8 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.3);
    }

    .content-admin-panel::before {
        content: "";
        position: absolute;
        width: 340px;
        height: 340px;
        top: -170px;
        right: -90px;
        border-radius: 9999px;
        background: rgba(59, 130, 246, 0.2);
        filter: blur(52px);
        pointer-events: none;
    }

    .content-admin-panel::after {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        bottom: -220px;
        left: -120px;
        border-radius: 9999px;
        background: rgba(245, 158, 11, 0.15);
        filter: blur(68px);
        pointer-events: none;
    }

    .content-admin-panel > * {
        position: relative;
        z-index: 1;
    }

    .content-admin-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .content-admin-chip {
        border: 1px solid rgba(59, 130, 246, 0.28);
        background: linear-gradient(135deg, #ffffff 0%, #e9f2ff 100%);
        color: #375274;
    }

    .content-admin-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.52);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .content-admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.42);
    }

    .content-admin-card--blue {
        border-top: 4px solid #3b82f6;
        background: linear-gradient(155deg, #ffffff 0%, #eff6ff 100%);
    }

    .content-admin-card--amber {
        border-top: 4px solid #f59e0b;
        background: linear-gradient(155deg, #ffffff 0%, #fffbeb 100%);
    }

    .content-admin-card--green {
        border-top: 4px solid #10b981;
        background: linear-gradient(155deg, #ffffff 0%, #ecfdf5 100%);
    }

    .content-admin-card--purple {
        border-top: 4px solid #8b5cf6;
        background: linear-gradient(155deg, #ffffff 0%, #f5f3ff 100%);
    }

    .content-admin-field {
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.85);
        padding: 0.75rem 0.85rem;
    }

    .contact-admin-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(140deg, #f7fbff 0%, #eef5ff 48%, #f7fde7 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.35);
    }

    .contact-admin-panel::before {
        content: "";
        position: absolute;
        width: 360px;
        height: 360px;
        top: -180px;
        right: -120px;
        border-radius: 9999px;
        background: rgba(223, 231, 83, 0.35);
        filter: blur(55px);
        pointer-events: none;
    }

    .contact-admin-panel::after {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        bottom: -220px;
        left: -120px;
        border-radius: 9999px;
        background: rgba(37, 51, 62, 0.12);
        filter: blur(68px);
        pointer-events: none;
    }

    .contact-admin-panel > * {
        position: relative;
        z-index: 1;
    }

    .contact-admin-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .contact-admin-chip {
        border: 1px solid rgba(37, 51, 62, 0.18);
        background: linear-gradient(135deg, #ffffff 0%, #edf3ff 100%);
        color: #50627c;
    }

    .contact-admin-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.55);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .contact-admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.45);
    }

    .contact-admin-card--labels {
        border-top: 4px solid #3b82f6;
        background: linear-gradient(155deg, #ffffff 0%, #eff6ff 100%);
    }

    .contact-admin-card--live {
        border-top: 4px solid #f59e0b;
        background: linear-gradient(155deg, #ffffff 0%, #fffbeb 100%);
    }

    .contact-admin-card--map {
        border-top: 4px solid #10b981;
        background: linear-gradient(155deg, #ffffff 0%, #ecfdf5 100%);
    }

    .contact-settings-form input[type="text"],
    .contact-settings-form input[type="url"] {
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

    .contact-settings-form textarea {
        min-height: 120px;
        padding: 12px 14px;
        font-size: 1rem;
        line-height: 1.65;
        color: #0f172a;
        background-color: #fff;
        border: 2px solid #c7d2e3;
    }

    .contact-admin-panel input:focus,
    .contact-admin-panel textarea:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        outline: none;
    }
</style>
