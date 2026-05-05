<form id="academics-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="academics">
    <input type="hidden" name="academics_label" value="{{ old('academics_label', $publicPage['academics_label'] ?? 'Academic Excellence') }}">
    <input type="hidden" name="academics_intro" value="{{ old('academics_intro', strip_tags((string) ($publicPage['academics_intro'] ?? ''))) }}">
    <input type="hidden" name="academics_support_text" value="{{ old('academics_support_text', strip_tags((string) ($publicPage['academics_support_text'] ?? ''))) }}">
    <input type="hidden" name="academic_highlight_1_title" value="{{ old('academic_highlight_1_title', $publicPage['academic_highlights'][0]['title'] ?? 'STEM-First Curriculum') }}">
    <input type="hidden" name="academic_highlight_1_description" value="{{ old('academic_highlight_1_description', strip_tags((string) ($publicPage['academic_highlights'][0]['description'] ?? ''))) }}">
    <input type="hidden" name="academic_highlight_2_title" value="{{ old('academic_highlight_2_title', $publicPage['academic_highlights'][1]['title'] ?? 'Student Leadership') }}">
    <input type="hidden" name="academic_highlight_2_description" value="{{ old('academic_highlight_2_description', strip_tags((string) ($publicPage['academic_highlights'][1]['description'] ?? ''))) }}">
    <input type="hidden" name="academics_items_text" value="{{ old('academics_items_text', $academicsItemsText) }}">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
</form>

<section id="academics-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'academics',
        '_sectionLabel' => trim((string) ($publicPage['academics_label'] ?? 'Academic Excellence')),
        '_items'        => $publicPage['academics'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>
