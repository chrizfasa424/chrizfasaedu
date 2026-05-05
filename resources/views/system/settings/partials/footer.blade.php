<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="footer-settings-form space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="footer">

    @php
        $quickLinksRaw = old('footer_quick_links_text', $footerQuickLinksText);
        $resourceLinksRaw = old('footer_resources_text', $footerResourcesText);
        $socialLinksRaw = old('footer_social_links_text', $footerSocialLinksText);

        $parseLines = function (string $value) {
            return collect(preg_split('/\r\n|\r|\n/', $value))
                ->map(fn ($line) => trim((string) $line))
                ->filter()
                ->values();
        };

        $quickLines = $parseLines((string) $quickLinksRaw);
        $resourceLines = $parseLines((string) $resourceLinksRaw);
        $socialLines = $parseLines((string) $socialLinksRaw);
    @endphp

    <section class="footer-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="footer-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Footer Identity and Branding</h2>
                <p class="mt-1 text-sm text-slate-600">Manage how your footer introduces the school brand and contact base.</p>
            </div>
            <span class="footer-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Brand Layer</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-12">
            <div class="footer-admin-card footer-admin-card--blue rounded-2xl border p-5 shadow-sm xl:col-span-8">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Footer Description</h3>
                <div class="mt-4 footer-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Description Text</label>
                    <textarea name="footer_description" rows="4" class="w-full rounded-2xl border-slate-300">{{ old('footer_description', $publicPage['footer_description'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="footer-admin-card footer-admin-card--amber rounded-2xl border p-5 shadow-sm xl:col-span-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Footer Logo</h3>
                <div class="mt-4 footer-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Upload Logo</label>
                    <input type="file" name="footer_logo" accept="image/*" class="w-full rounded-2xl border-slate-300 bg-white file:mr-3 file:rounded-xl file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                    <p class="mt-2 text-xs text-slate-600">Recommended square logo (min 300 x 300px).</p>
                </div>

                <div class="mt-4">
                    @if(!empty($publicPage['footer_logo']))
                        <img src="{{ asset('storage/' . ltrim($publicPage['footer_logo'], '/')) }}" alt="Footer logo" class="h-20 w-20 rounded-2xl border border-slate-200 bg-white object-cover p-1">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">No Logo</div>
                    @endif
                </div>

                <label class="mt-4 inline-flex items-center text-sm font-medium text-rose-700">
                    <input type="checkbox" name="remove_footer_logo" value="1" class="mr-2 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    Remove current footer logo
                </label>
            </div>
        </div>
    </section>

    <section class="footer-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="footer-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Footer Titles and Contact Labels</h2>
                <p class="mt-1 text-sm text-slate-600">Set footer titles and contact metadata displayed across public pages.</p>
            </div>
            <span class="footer-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Content Map</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="footer-admin-card footer-admin-card--green rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Quick Links Title</label>
                <input type="text" name="footer_quick_links_title" value="{{ old('footer_quick_links_title', $publicPage['footer_quick_links_title'] ?? 'Quick Links') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card footer-admin-card--green rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Resources Title</label>
                <input type="text" name="footer_resources_title" value="{{ old('footer_resources_title', $publicPage['footer_resources_title'] ?? 'Resources') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card footer-admin-card--green rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Contact Title</label>
                <input type="text" name="footer_contact_title" value="{{ old('footer_contact_title', $publicPage['footer_contact_title'] ?? 'Contact') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card footer-admin-card--green rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Note</label>
                <input type="text" name="footer_note" value="{{ old('footer_note', $publicPage['footer_note'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card rounded-2xl border p-5 shadow-sm md:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Contact Address</label>
                <input type="text" name="footer_contact_address" value="{{ old('footer_contact_address', $publicPage['footer_contact_address'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Contact Phone</label>
                <input type="text" name="footer_contact_phone" value="{{ old('footer_contact_phone', $publicPage['footer_contact_phone'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="footer-admin-card rounded-2xl border p-5 shadow-sm">
                <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Contact Email</label>
                <input type="email" name="footer_contact_email" value="{{ old('footer_contact_email', $publicPage['footer_contact_email'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
            </div>
        </div>
    </section>

    <section class="footer-admin-panel rounded-3xl border p-6 shadow-sm">
        <div class="footer-admin-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Footer Link Libraries</h2>
                <p class="mt-1 text-sm text-slate-600">Use one line per item: <strong>Title | Link</strong>. This controls your public footer navigation.</p>
            </div>
            <span class="footer-admin-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Link Editor</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="footer-admin-card footer-admin-card--blue rounded-2xl border p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Quick Links</h3>
                    <span class="footer-admin-count">{{ $quickLines->count() }} Items</span>
                </div>
                <div class="footer-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Quick Links</label>
                    <textarea name="footer_quick_links_text" rows="9" class="w-full rounded-2xl border-slate-300" placeholder="Home | /&#10;Programs | /menu/programs&#10;Admissions | /admissions">{{ $quickLinksRaw }}</textarea>
                </div>
                <p class="mt-2 text-xs text-slate-600">Example: <code>Admissions | /admissions</code></p>
                @if($quickLines->isNotEmpty())
                    <div class="mt-3 rounded-xl border border-slate-200 bg-white/80 p-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-500">Preview</p>
                        <ul class="mt-2 space-y-1 text-sm text-slate-700">
                            @foreach($quickLines->take(4) as $line)
                                <li>- {{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="footer-admin-card footer-admin-card--amber rounded-2xl border p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Resources</h3>
                    <span class="footer-admin-count">{{ $resourceLines->count() }} Items</span>
                </div>
                <div class="footer-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Resources</label>
                    <textarea name="footer_resources_text" rows="9" class="w-full rounded-2xl border-slate-300" placeholder="School Calendar | /menu/student-life/school-calendar&#10;FAQs | /menu/admissions/faqs">{{ $resourceLinksRaw }}</textarea>
                </div>
                <p class="mt-2 text-xs text-slate-600">Example: <code>FAQs | /menu/admissions/faqs</code></p>
                @if($resourceLines->isNotEmpty())
                    <div class="mt-3 rounded-xl border border-slate-200 bg-white/80 p-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-500">Preview</p>
                        <ul class="mt-2 space-y-1 text-sm text-slate-700">
                            @foreach($resourceLines->take(4) as $line)
                                <li>- {{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="footer-admin-card footer-admin-card--teal rounded-2xl border p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Social Links</h3>
                    <span class="footer-admin-count">{{ $socialLines->count() }} Items</span>
                </div>
                <div class="footer-admin-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Footer Social Links</label>
                    <textarea name="footer_social_links_text" rows="9" class="w-full rounded-2xl border-slate-300" placeholder="Facebook | https://facebook.com/your-school&#10;Instagram | https://instagram.com/your-school&#10;YouTube | https://youtube.com/@your-school">{{ $socialLinksRaw }}</textarea>
                </div>
                <p class="mt-2 text-xs text-slate-600">Supported icons: Facebook, Instagram, YouTube, X, TikTok, LinkedIn, WhatsApp.</p>
                @if($socialLines->isNotEmpty())
                    <div class="mt-3 rounded-xl border border-slate-200 bg-white/80 p-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-500">Preview</p>
                        <ul class="mt-2 space-y-1 text-sm text-slate-700">
                            @foreach($socialLines->take(4) as $line)
                                <li>- {{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">Save Footer Settings</button>
</form>

<style>
    .footer-admin-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(145deg, #f7fbff 0%, #eef5ff 50%, #f7fde7 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.34);
    }

    .footer-admin-panel::before {
        content: "";
        position: absolute;
        width: 340px;
        height: 340px;
        top: -180px;
        right: -110px;
        border-radius: 9999px;
        background: rgba(223, 231, 83, 0.3);
        filter: blur(56px);
        pointer-events: none;
    }

    .footer-admin-panel::after {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        bottom: -220px;
        left: -130px;
        border-radius: 9999px;
        background: rgba(59, 130, 246, 0.14);
        filter: blur(70px);
        pointer-events: none;
    }

    .footer-admin-panel > * {
        position: relative;
        z-index: 1;
    }

    .footer-admin-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .footer-admin-chip {
        border: 1px solid rgba(37, 51, 62, 0.2);
        background: linear-gradient(135deg, #ffffff 0%, #edf3ff 100%);
        color: #50627c;
    }

    .footer-admin-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.52);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .footer-admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.42);
    }

    .footer-admin-card--blue {
        border-top: 4px solid #3b82f6;
        background: linear-gradient(155deg, #ffffff 0%, #eff6ff 100%);
    }

    .footer-admin-card--amber {
        border-top: 4px solid #f59e0b;
        background: linear-gradient(155deg, #ffffff 0%, #fffbeb 100%);
    }

    .footer-admin-card--green {
        border-top: 4px solid #10b981;
        background: linear-gradient(155deg, #ffffff 0%, #ecfdf5 100%);
    }

    .footer-admin-card--teal {
        border-top: 4px solid #06b6d4;
        background: linear-gradient(155deg, #ffffff 0%, #ecfeff 100%);
    }

    .footer-admin-field {
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.86);
        padding: 0.75rem 0.85rem;
    }

    .footer-admin-count {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        border: 1px solid rgba(37, 51, 62, 0.15);
        background: rgba(255, 255, 255, 0.9);
        padding: 0.28rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.11em;
        text-transform: uppercase;
        color: #4f637d;
    }

    .footer-settings-form input[type="text"],
    .footer-settings-form input[type="email"] {
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

    .footer-settings-form textarea {
        min-height: 120px;
        padding: 12px 14px;
        font-size: 1rem;
        line-height: 1.65;
        color: #0f172a;
        background-color: #fff;
        border: 2px solid #c7d2e3;
    }

    .footer-admin-panel input:focus,
    .footer-admin-panel textarea:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        outline: none;
    }
</style>
