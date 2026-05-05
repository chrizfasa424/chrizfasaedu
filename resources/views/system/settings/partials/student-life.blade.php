<form id="student-life-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="student-life">
    <input type="hidden" name="student_life_label" value="{{ old('student_life_label', $publicPage['student_life_label'] ?? 'Student Life') }}">
    <input type="hidden" name="student_life_intro" value="{{ old('student_life_intro', strip_tags((string) ($publicPage['student_life_intro'] ?? ''))) }}">
    <input type="hidden" name="student_life_items_text" value="{{ old('student_life_items_text', $studentLifeItemsText) }}">

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
</form>

<section id="student-life-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'student-life',
        '_sectionLabel' => trim((string) ($publicPage['student_life_label'] ?? 'Student Life')),
        '_items'        => $publicPage['student_life'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>
