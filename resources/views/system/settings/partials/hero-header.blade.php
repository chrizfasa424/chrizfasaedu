<form action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Badge Text</label>
            <input type="text" name="hero_badge_text" value="{{ old('hero_badge_text', $publicPage['hero_badge_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Admission Session Text</label>
            <input type="text" name="admission_session_text" value="{{ old('admission_session_text', $publicPage['admission_session_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Title</label>
            <input type="text" name="hero_title" value="{{ old('hero_title', $publicPage['hero_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Subtitle</label>
            <textarea name="hero_subtitle" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('hero_subtitle', $publicPage['hero_subtitle'] ?? '') }}</textarea>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Primary Button Text</label>
            <input type="text" name="cta_primary_text" value="{{ old('cta_primary_text', $publicPage['cta_primary_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Secondary Button Text</label>
            <input type="text" name="cta_secondary_text" value="{{ old('cta_secondary_text', $publicPage['cta_secondary_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Slider Placeholder Text</label>
            <input type="text" name="hero_slider_placeholder_text" value="{{ old('hero_slider_placeholder_text', $publicPage['hero_slider_placeholder_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
            <p class="mt-2 text-xs text-slate-500">Hero slide images are managed separately under the Hero Slides menu.</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Hero Metrics</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @for($i = 0; $i < 4; $i++)
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Metric {{ $i + 1 }} Value</label>
                    <input type="text" name="metric_{{ $i + 1 }}_value" value="{{ old('metric_' . ($i + 1) . '_value', $publicPage['metrics'][$i]['value'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    <label class="mb-2 mt-4 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Metric {{ $i + 1 }} Label</label>
                    <input type="text" name="metric_{{ $i + 1 }}_label" value="{{ old('metric_' . ($i + 1) . '_label', $publicPage['metrics'][$i]['label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                </div>
            @endfor
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Hero Header</button>
</form>



