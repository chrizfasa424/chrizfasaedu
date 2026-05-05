<form action="{{ route('settings.public-page') }}" method="POST" class="hero-settings-form space-y-8">
    @csrf
    @method('PUT')

    <section class="hero-content-panel rounded-3xl border p-6 shadow-sm">
        <div class="hero-content-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Hero Header Content</h2>
                <p class="mt-1 text-sm text-slate-600">Control the homepage hero headline, message, CTA labels, and slider fallback copy.</p>
            </div>
            <span class="hero-content-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Homepage Hero</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="hero-content-card hero-content-card--blue rounded-2xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Top Labels</h3>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Hero Badge Text</label>
                        <input type="text" name="hero_badge_text" value="{{ old('hero_badge_text', $publicPage['hero_badge_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Admission Session Text</label>
                        <input type="text" name="admission_session_text" value="{{ old('admission_session_text', $publicPage['admission_session_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="hero-content-card hero-content-card--amber rounded-2xl border p-5 shadow-sm xl:col-span-2">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Hero Messaging</h3>
                <div class="mt-4 grid grid-cols-1 gap-4">
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Hero Title</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $publicPage['hero_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Hero Subtitle</label>
                        <textarea name="hero_subtitle" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('hero_subtitle', $publicPage['hero_subtitle'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="hero-content-card hero-content-card--green rounded-2xl border p-5 shadow-sm xl:col-span-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Call To Action Buttons</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Primary Button Text</label>
                        <input type="text" name="cta_primary_text" value="{{ old('cta_primary_text', $publicPage['cta_primary_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                    <div class="hero-content-field">
                        <label class="mb-2 block text-sm font-semibold text-slate-800">Secondary Button Text</label>
                        <input type="text" name="cta_secondary_text" value="{{ old('cta_secondary_text', $publicPage['cta_secondary_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    </div>
                </div>
            </div>

            <div class="hero-content-card hero-content-card--purple rounded-2xl border p-5 shadow-sm xl:col-span-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Slider Fallback Copy</h3>
                <div class="mt-4 hero-content-field">
                    <label class="mb-2 block text-sm font-semibold text-slate-800">Hero Slider Placeholder Text</label>
                    <input type="text" name="hero_slider_placeholder_text" value="{{ old('hero_slider_placeholder_text', $publicPage['hero_slider_placeholder_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                    <p class="mt-2 text-xs text-slate-600">Hero slide images are managed separately under the Hero Slides menu.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="hero-metrics-panel rounded-3xl border p-6 shadow-sm">
        <div class="hero-metrics-heading flex flex-wrap items-start justify-between gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Hero Metrics</h2>
                <p class="mt-1 text-sm text-slate-600">Manage the four headline metric chips shown in the homepage hero section.</p>
            </div>
            <span class="hero-metrics-chip inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">Statistics</span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @for($i = 0; $i < 4; $i++)
                <div class="hero-metric-card rounded-2xl border p-4 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-[0.1em] text-slate-700">Metric {{ $i + 1 }}</h3>
                    <div class="mt-3 space-y-3">
                        <div class="hero-content-field">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-600">Value</label>
                            <input type="text" name="metric_{{ $i + 1 }}_value" value="{{ old('metric_' . ($i + 1) . '_value', $publicPage['metrics'][$i]['value'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                        </div>
                        <div class="hero-content-field">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-slate-600">Label</label>
                            <input type="text" name="metric_{{ $i + 1 }}_label" value="{{ old('metric_' . ($i + 1) . '_label', $publicPage['metrics'][$i]['label'] ?? '') }}" class="w-full rounded-2xl border-slate-300">
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#25333E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#25333E]">Save Hero Header</button>
</form>

<style>
    .hero-content-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(140deg, #f7fbff 0%, #f8fbff 45%, #fdfce8 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.3);
    }

    .hero-content-panel::before {
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

    .hero-content-panel::after {
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

    .hero-content-panel > * {
        position: relative;
        z-index: 1;
    }

    .hero-content-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .hero-content-chip {
        border: 1px solid rgba(59, 130, 246, 0.28);
        background: linear-gradient(135deg, #ffffff 0%, #e9f2ff 100%);
        color: #375274;
    }

    .hero-content-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.52);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hero-content-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.42);
    }

    .hero-content-card--blue {
        border-top: 4px solid #3b82f6;
        background: linear-gradient(155deg, #ffffff 0%, #eff6ff 100%);
    }

    .hero-content-card--amber {
        border-top: 4px solid #f59e0b;
        background: linear-gradient(155deg, #ffffff 0%, #fffbeb 100%);
    }

    .hero-content-card--green {
        border-top: 4px solid #10b981;
        background: linear-gradient(155deg, #ffffff 0%, #ecfdf5 100%);
    }

    .hero-content-card--purple {
        border-top: 4px solid #8b5cf6;
        background: linear-gradient(155deg, #ffffff 0%, #f5f3ff 100%);
    }

    .hero-metrics-panel {
        position: relative;
        overflow: hidden;
        border-color: #cfd8e3;
        background: linear-gradient(140deg, #f7fbff 0%, #eef5ff 48%, #f7fde7 100%);
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.35);
    }

    .hero-metrics-panel::before {
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

    .hero-metrics-panel::after {
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

    .hero-metrics-panel > * {
        position: relative;
        z-index: 1;
    }

    .hero-metrics-heading {
        border-color: rgba(37, 51, 62, 0.12);
    }

    .hero-metrics-chip {
        border: 1px solid rgba(37, 51, 62, 0.18);
        background: linear-gradient(135deg, #ffffff 0%, #edf3ff 100%);
        color: #50627c;
    }

    .hero-metric-card {
        border-color: #d8e1ee;
        background: linear-gradient(160deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 14px 34px -28px rgba(31, 47, 74, 0.55);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .hero-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 36px -24px rgba(31, 47, 74, 0.45);
    }

    .hero-content-field {
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.85);
        padding: 0.75rem 0.85rem;
    }

    .hero-settings-form input[type="text"],
    .hero-settings-form input[type="url"] {
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

    .hero-settings-form textarea {
        min-height: 120px;
        padding: 12px 14px;
        font-size: 1rem;
        line-height: 1.65;
        color: #0f172a;
        background-color: #fff;
        border: 2px solid #c7d2e3;
    }

    .hero-settings-form input:focus,
    .hero-settings-form textarea:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        outline: none;
    }
</style>
