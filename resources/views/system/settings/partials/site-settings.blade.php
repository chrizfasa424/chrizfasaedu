@push('styles')
<style>
    .site-settings-input {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.45);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.14), transparent 40%),
            linear-gradient(180deg, #ffffff 0%, #f8f7ff 100%);
        padding: 0.78rem 0.92rem;
        color: #0f172a;
        font-size: 0.925rem;
        line-height: 1.45;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .site-settings-input::placeholder {
        color: #94a3b8;
        opacity: 1;
    }

    .site-settings-input:focus,
    .site-settings-input:focus-visible {
        outline: none;
        border-color: #2D1D5C;
        box-shadow:
            0 0 0 4px rgba(45, 29, 92, 0.14),
            0 14px 28px -20px rgba(15, 23, 42, 0.48);
    }

    .site-settings-label {
        margin-bottom: 0.48rem;
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }
</style>
@endpush

@php
    $publicDomain = trim((string) ($school->domain ?? ''));
    $publicLink = $publicDomain !== '' ? 'http://' . $publicDomain : null;
@endphp

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <p class="font-semibold">Please review the highlighted inputs and try again.</p>
        </div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-100 pb-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/70">Site Identity</p>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Core School Details</h2>
            <p class="text-sm text-slate-500">These values appear across the dashboard and public pages.</p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
                <label class="site-settings-label" for="site-name">School Name</label>
                <input id="site-name" type="text" name="name" value="{{ old('name', $school->name) }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="site-email">Email Address</label>
                <input id="site-email" type="email" name="email" value="{{ old('email', $school->email) }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="site-phone">Phone Number</label>
                <input id="site-phone" type="text" name="phone" value="{{ old('phone', $school->phone) }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="site-website">Website</label>
                <input id="site-website" type="url" name="website" value="{{ old('website', $school->website) }}" class="site-settings-input" placeholder="https://example.com">
            </div>
            <div>
                <label class="site-settings-label" for="site-domain">Main School Domain</label>
                <input id="site-domain" type="text" name="domain" value="{{ old('domain', $school->domain) }}" class="site-settings-input" placeholder="chrizfasaedu.test">
                <p class="mt-2 text-xs text-slate-500">Enter only the domain. Do not include protocol (`http://`) or any path.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Current Public Link</p>
                @if($publicLink)
                    <a href="{{ $publicLink }}" target="_blank" rel="noopener" class="mt-2 inline-block break-all text-sm font-semibold text-[#2D1D5C] hover:underline">{{ $publicLink }}</a>
                @else
                    <p class="mt-2 text-sm text-slate-500">No custom domain saved yet.</p>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-100 pb-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/70">Messaging</p>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Brand Voice</h2>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4">
            <div>
                <label class="site-settings-label" for="site-motto">Motto / Hero Line</label>
                <input id="site-motto" type="text" name="motto" value="{{ old('motto', $school->motto) }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="site-address">Address</label>
                <textarea id="site-address" name="address" rows="4" class="site-settings-input">{{ old('address', $school->address) }}</textarea>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-100 pb-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/70">Brand Assets</p>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Logo and Favicon</h2>
            <p class="text-sm text-slate-500">Upload fresh assets or remove the existing ones if you are rebranding.</p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="site-settings-label" for="school-logo">School Logo</label>
                <input id="school-logo" type="file" name="logo" accept="image/*" class="site-settings-input bg-white">
                <label class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-rose-700">
                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300">
                    Remove current logo
                </label>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="site-settings-label">Current Logo Preview</p>
                @if($school->logo)
                    <img src="{{ asset('storage/' . ltrim($school->logo, '/')) }}" alt="School Logo" class="h-24 w-24 rounded-2xl border border-slate-200 bg-white object-cover p-1">
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-xs font-semibold uppercase tracking-[0.08em] text-slate-400">No logo</div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <label class="site-settings-label" for="site-favicon">Site Favicon</label>
                <input id="site-favicon" type="file" name="favicon" accept=".ico,image/png,image/jpeg,image/webp" class="site-settings-input bg-white">
                <p class="mt-2 text-xs text-slate-500">Recommended: square `.png` or `.ico` for tabs and bookmarks.</p>
                <label class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-rose-700">
                    <input type="checkbox" name="remove_favicon" value="1" class="rounded border-slate-300">
                    Remove current favicon
                </label>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="site-settings-label">Current Favicon Preview</p>
                @if($faviconPath)
                    <img src="{{ asset('storage/' . ltrim($faviconPath, '/')) }}" alt="Site Favicon" class="h-16 w-16 rounded-2xl border border-slate-200 bg-white object-cover p-1">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-400">No icon</div>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 border-b border-slate-100 pb-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/70">Navigation Text</p>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Shared Header and Mobile Labels</h2>
            <p class="text-sm text-slate-500">These labels are saved via the dedicated public-page settings endpoint.</p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label class="site-settings-label" for="header-apply-text">Header Apply Text</label>
                <input id="header-apply-text" type="text" form="public-site-settings-form" name="header_apply_text" value="{{ old('header_apply_text', $publicPage['header_apply_text'] ?? 'Apply') }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="header-portal-text">Header Portal Text</label>
                <input id="header-portal-text" type="text" form="public-site-settings-form" name="header_portal_login_text" value="{{ old('header_portal_login_text', $publicPage['header_portal_login_text'] ?? 'Portal Login') }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="mobile-apply-text">Mobile Apply Text</label>
                <input id="mobile-apply-text" type="text" form="public-site-settings-form" name="mobile_apply_text" value="{{ old('mobile_apply_text', $publicPage['mobile_apply_text'] ?? 'Apply Now') }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="mobile-portal-text">Mobile Portal Text</label>
                <input id="mobile-portal-text" type="text" form="public-site-settings-form" name="mobile_portal_login_text" value="{{ old('mobile_portal_login_text', $publicPage['mobile_portal_login_text'] ?? 'Portal Login') }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="site-title-suffix">Site Title Suffix</label>
                <input id="site-title-suffix" type="text" form="public-site-settings-form" name="site_title_suffix" value="{{ old('site_title_suffix', $publicPage['site_title_suffix'] ?? 'KG, Primary and Secondary School') }}" class="site-settings-input">
            </div>
            <div>
                <label class="site-settings-label" for="mobile-menu-title">Mobile Menu Title</label>
                <input id="mobile-menu-title" type="text" form="public-site-settings-form" name="mobile_menu_title" value="{{ old('mobile_menu_title', $publicPage['mobile_menu_title'] ?? 'Menu') }}" class="site-settings-input">
            </div>
        </div>
    </section>

    <div class="sticky bottom-3 z-10 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Site Settings</button>
            <button type="submit" form="public-site-settings-form" class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">Save Shared Navigation Text</button>
        </div>
    </div>
</form>

<form id="public-site-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="hidden">
    @csrf
    @method('PUT')
</form>

