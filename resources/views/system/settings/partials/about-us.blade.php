<form id="about-settings-form" action="{{ route('settings.public-page') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="settings_page" value="about-us">
    <input type="hidden" name="about_label" value="{{ old('about_label', $publicPage['about_label'] ?? 'About Us') }}">
    <input type="hidden" name="about_intro" value="{{ old('about_intro', strip_tags((string) ($publicPage['about_intro'] ?? ''))) }}">
    <input type="hidden" name="about_items_text" value="{{ old('about_items_text', $aboutItemsText ?? '') }}">
    <input type="hidden" name="why_choose_us_label" value="{{ old('why_choose_us_label', $publicPage['why_choose_us_label'] ?? 'Why Choose Us') }}">
    <input type="hidden" name="why_choose_us_intro" value="{{ old('why_choose_us_intro', strip_tags((string) ($publicPage['why_choose_us_intro'] ?? ''))) }}">
    <input type="hidden" name="why_choose_us" value="{{ old('why_choose_us', $whyChooseUsText ?? '') }}">
    <input type="hidden" name="teachers_marquee_label" value="{{ old('teachers_marquee_label', $publicPage['teachers_marquee_label'] ?? 'Our Teachers') }}">
    <input type="hidden" name="teachers_marquee_heading" value="{{ old('teachers_marquee_heading', $publicPage['teachers_marquee_heading'] ?? 'Meet Our Teaching Team') }}">
    <input type="hidden" name="teachers_marquee_intro" value="{{ old('teachers_marquee_intro', $publicPage['teachers_marquee_intro'] ?? 'Experienced teachers guiding learners with care, discipline, and excellence.') }}">
    <input type="hidden" name="legal_effective_date" value="{{ old('legal_effective_date', $publicPage['legal_effective_date'] ?? '') }}">
    <input type="hidden" name="privacy_policy_title" value="{{ old('privacy_policy_title', $publicPage['privacy_policy_title'] ?? 'Privacy Policy') }}">
    <input type="hidden" name="privacy_policy_intro" value="{{ old('privacy_policy_intro', strip_tags((string) ($publicPage['privacy_policy_intro'] ?? ''))) }}">
    <input type="hidden" name="privacy_policy_content" value="{{ old('privacy_policy_content', strip_tags((string) ($publicPage['privacy_policy_content'] ?? ''))) }}">
    <input type="hidden" name="cookies_policy_title" value="{{ old('cookies_policy_title', $publicPage['cookies_policy_title'] ?? 'Cookies Policy') }}">
    <input type="hidden" name="cookies_policy_intro" value="{{ old('cookies_policy_intro', strip_tags((string) ($publicPage['cookies_policy_intro'] ?? ''))) }}">
    <input type="hidden" name="cookies_policy_content" value="{{ old('cookies_policy_content', strip_tags((string) ($publicPage['cookies_policy_content'] ?? ''))) }}">
    <input type="hidden" name="cookie_banner_title" value="{{ old('cookie_banner_title', $publicPage['cookie_banner_title'] ?? 'Cookie Notice') }}">
    <input type="hidden" name="cookie_banner_text" value="{{ old('cookie_banner_text', $publicPage['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.') }}">
    <input type="hidden" name="cookie_banner_accept_text" value="{{ old('cookie_banner_accept_text', $publicPage['cookie_banner_accept_text'] ?? 'Accept Cookies') }}">
    <input type="hidden" name="cookie_banner_reject_text" value="{{ old('cookie_banner_reject_text', $publicPage['cookie_banner_reject_text'] ?? 'Reject Optional') }}">
    @php $aboutTeacherRows = old('teacher_marquee', $teacherMarqueeItems ?? []); @endphp
    @foreach($aboutTeacherRows as $index => $row)
        <input type="hidden" name="teacher_marquee[{{ $index }}][existing_image]" value="{{ trim((string) ($row['existing_image'] ?? ($row['image'] ?? ''))) }}">
        <input type="hidden" name="teacher_marquee[{{ $index }}][name]" value="{{ trim((string) ($row['name'] ?? '')) }}">
        <input type="hidden" name="teacher_marquee[{{ $index }}][role]" value="{{ trim((string) ($row['role'] ?? '')) }}">
        <input type="hidden" name="teacher_marquee[{{ $index }}][remove_image]" value="0">
        <input type="hidden" name="teacher_marquee[{{ $index }}][remove_row]" value="0">
    @endforeach

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
</form>

<section id="about-editor-feed" class="space-y-4">
    @include('system.settings.partials._submenu-item-editor', [
        '_section'      => 'about',
        '_sectionLabel' => trim((string) ($publicPage['about_label'] ?? 'About Us')),
        '_items'        => $publicPage['about'] ?? [],
        '_publicPage'   => $publicPage,
    ])
</section>
