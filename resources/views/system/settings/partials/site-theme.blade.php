<form action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Theme Style</label>
                @php $themeStyle = old('theme_style', $publicPage['theme_style'] ?? 'modern-grid'); @endphp
                <select name="theme_style" class="w-full rounded-2xl border-slate-300">
                    <option value="modern-grid" {{ $themeStyle === 'modern-grid' ? 'selected' : '' }}>Modern Grid</option>
                    <option value="soft-gradient" {{ $themeStyle === 'soft-gradient' ? 'selected' : '' }}>Soft Gradient</option>
                    <option value="minimal-clean" {{ $themeStyle === 'minimal-clean' ? 'selected' : '' }}>Minimal Clean</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Primary Brand Color</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', $publicPage['primary_color'] ?? '#2D1D5C') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Secondary Brand Color</label>
                <input type="color" name="secondary_color" value="{{ old('secondary_color', $publicPage['secondary_color'] ?? '#DFE753') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Site Background Color</label>
                <input type="color" name="site_background_color" value="{{ old('site_background_color', $publicPage['site_background_color'] ?? '#F8FAFC') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Heading Text Color</label>
                <input type="color" name="heading_text_color" value="{{ old('heading_text_color', $publicPage['heading_text_color'] ?? '#0F172A') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Body Text Color</label>
                <input type="color" name="body_text_color" value="{{ old('body_text_color', $publicPage['body_text_color'] ?? '#475569') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Card Surface Color</label>
                <input type="color" name="surface_color" value="{{ old('surface_color', $publicPage['surface_color'] ?? '#FFFFFF') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Soft Surface Tint</label>
                <input type="color" name="soft_surface_color" value="{{ old('soft_surface_color', $publicPage['soft_surface_color'] ?? '#EEF6FF') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Header Color</label>
                <input type="color" name="header_bg_color" value="{{ old('header_bg_color', $publicPage['header_bg_color'] ?? '#2D1D5C') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Color</label>
                <input type="color" name="footer_bg_color" value="{{ old('footer_bg_color', $publicPage['footer_bg_color'] ?? '#2D1D5C') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Separator Color</label>
                <input type="color" name="footer_separator_color" value="{{ old('footer_separator_color', $publicPage['footer_separator_color'] ?? '#DFE753') }}" class="h-12 w-full rounded-2xl border border-slate-300 bg-white p-1">
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Theme Settings</button>
        <button type="submit" form="reset-theme-form" class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]" onclick="return confirm('Reset header and footer colors to the default school theme?');">Reset Theme To Default</button>
    </div>
</form>

<form id="reset-theme-form" action="{{ route('settings.public-page.reset-theme') }}" method="POST" class="hidden">
    @csrf
</form>

