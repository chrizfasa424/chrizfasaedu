<form id="facilities-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="facilities">
    <input type="hidden" name="facilities_label" value="{{ old('facilities_label', $publicPage['facilities_label'] ?? 'Facilities') }}">
    <input type="hidden" name="facilities_intro" value="{{ old('facilities_intro', strip_tags((string) ($publicPage['facilities_intro'] ?? ''))) }}">
    <input type="hidden" name="facilities" value="{{ old('facilities', $facilitiesText) }}">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
</form>

<section id="facilities-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'facilities',
        '_sectionLabel' => trim((string) ($publicPage['facilities_label'] ?? 'Facilities')),
        '_items'        => $publicPage['facilities'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>
