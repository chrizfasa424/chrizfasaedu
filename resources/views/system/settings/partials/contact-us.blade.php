<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Contact Menu Label</label>
            <input type="text" name="contact_label" value="{{ old('contact_label', $publicPage['contact_label'] ?? 'Contact') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Quick Contact Title</label>
            <input type="text" name="quick_contact_label" value="{{ old('quick_contact_label', $publicPage['quick_contact_label'] ?? 'Quick Contact') }}" class="w-full rounded-2xl border-slate-300">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Map Embed Title</label>
            <input type="text" name="map_embed_title_text" value="{{ old('map_embed_title_text', $publicPage['map_embed_title_text'] ?? 'School map') }}" class="w-full rounded-2xl border-slate-300">
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Contact Page Content</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Browser Title</label>
                <input type="text" name="contact_page_browser_title" value="{{ old('contact_page_browser_title', $publicPage['contact_page_browser_title'] ?? 'Contact Us') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Badge Text</label>
                <input type="text" name="contact_page_badge_text" value="{{ old('contact_page_badge_text', $publicPage['contact_page_badge_text'] ?? 'Contact Us') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="xl:col-span-2">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Page Heading</label>
                <input type="text" name="contact_page_heading" value="{{ old('contact_page_heading', $publicPage['contact_page_heading'] ?? 'We are here to help you') }}" class="w-full rounded-2xl border-slate-300">
            </div>
            <div class="xl:col-span-4">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Page Subheading</label>
                <textarea name="contact_page_subheading" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('contact_page_subheading', $publicPage['contact_page_subheading'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Contact Form Text</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Form Title</label><input type="text" name="contact_form_title" value="{{ old('contact_form_title', $publicPage['contact_form_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Full Name Label</label><input type="text" name="contact_form_full_name_label" value="{{ old('contact_form_full_name_label', $publicPage['contact_form_full_name_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Full Name Placeholder</label><input type="text" name="contact_form_full_name_placeholder" value="{{ old('contact_form_full_name_placeholder', $publicPage['contact_form_full_name_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Email Label</label><input type="text" name="contact_form_email_label" value="{{ old('contact_form_email_label', $publicPage['contact_form_email_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Email Placeholder</label><input type="text" name="contact_form_email_placeholder" value="{{ old('contact_form_email_placeholder', $publicPage['contact_form_email_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Phone Label</label><input type="text" name="contact_form_phone_label" value="{{ old('contact_form_phone_label', $publicPage['contact_form_phone_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Phone Placeholder</label><input type="text" name="contact_form_phone_placeholder" value="{{ old('contact_form_phone_placeholder', $publicPage['contact_form_phone_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Subject Label</label><input type="text" name="contact_form_subject_label" value="{{ old('contact_form_subject_label', $publicPage['contact_form_subject_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Subject Placeholder</label><input type="text" name="contact_form_subject_placeholder" value="{{ old('contact_form_subject_placeholder', $publicPage['contact_form_subject_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Message Label</label><input type="text" name="contact_form_message_label" value="{{ old('contact_form_message_label', $publicPage['contact_form_message_label'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div class="md:col-span-2 xl:col-span-3"><label class="mb-2 block text-sm font-semibold text-slate-700">Message Placeholder</label><input type="text" name="contact_form_message_placeholder" value="{{ old('contact_form_message_placeholder', $publicPage['contact_form_message_placeholder'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submit Button Text</label><input type="text" name="contact_form_submit_text" value="{{ old('contact_form_submit_text', $publicPage['contact_form_submit_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Contact Details and Labels</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Contact Info Title</label><input type="text" name="contact_info_title" value="{{ old('contact_info_title', $publicPage['contact_info_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Missing Value Text</label><input type="text" name="contact_not_provided_text" value="{{ old('contact_not_provided_text', $publicPage['contact_not_provided_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">More Details Title</label><input type="text" name="contact_more_details_title" value="{{ old('contact_more_details_title', $publicPage['contact_more_details_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Menu Overview Suffix</label><input type="text" name="menu_overview_suffix" value="{{ old('menu_overview_suffix', $publicPage['menu_overview_suffix'] ?? 'Overview') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Phone Label</label><input type="text" name="contact_phone_label" value="{{ old('contact_phone_label', $publicPage['contact_phone_label'] ?? 'Phone') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">WhatsApp Label</label><input type="text" name="contact_whatsapp_label" value="{{ old('contact_whatsapp_label', $publicPage['contact_whatsapp_label'] ?? 'WhatsApp') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Email Label</label><input type="text" name="contact_email_label" value="{{ old('contact_email_label', $publicPage['contact_email_label'] ?? 'Email') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Address Label</label><input type="text" name="contact_address_label" value="{{ old('contact_address_label', $publicPage['contact_address_label'] ?? 'Address') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">WhatsApp Number</label><input type="text" name="whatsapp" value="{{ old('whatsapp', $publicPage['whatsapp'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div class="xl:col-span-2"><label class="mb-2 block text-sm font-semibold text-slate-700">Map Embed URL</label><input type="url" name="map_embed_url" value="{{ old('map_embed_url', $publicPage['map_embed_url'] ?? '') }}" class="w-full rounded-2xl border-slate-300" placeholder="https://maps.google.com/..."/></div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Submenu and Status Microcopy</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Card One Title</label><input type="text" name="submenu_highlight_one_title" value="{{ old('submenu_highlight_one_title', $publicPage['submenu_highlight_one_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div class="md:col-span-2 xl:col-span-4"><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Card One Text</label><textarea name="submenu_highlight_one_text" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('submenu_highlight_one_text', $publicPage['submenu_highlight_one_text'] ?? '') }}</textarea></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Card Two Title</label><input type="text" name="submenu_highlight_two_title" value="{{ old('submenu_highlight_two_title', $publicPage['submenu_highlight_two_title'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div class="md:col-span-2 xl:col-span-4"><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Card Two Text</label><textarea name="submenu_highlight_two_text" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('submenu_highlight_two_text', $publicPage['submenu_highlight_two_text'] ?? '') }}</textarea></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Primary Button Text</label><input type="text" name="submenu_primary_button_text" value="{{ old('submenu_primary_button_text', $publicPage['submenu_primary_button_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Back Prefix</label><input type="text" name="submenu_back_button_prefix" value="{{ old('submenu_back_button_prefix', $publicPage['submenu_back_button_prefix'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Side Prefix</label><input type="text" name="submenu_more_in_prefix" value="{{ old('submenu_more_in_prefix', $publicPage['submenu_more_in_prefix'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div class="xl:col-span-4"><label class="mb-2 block text-sm font-semibold text-slate-700">Submenu Description Fallback Template</label><input type="text" name="submenu_description_fallback_template" value="{{ old('submenu_description_fallback_template', $publicPage['submenu_description_fallback_template'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Form Unavailable Text</label><input type="text" name="contact_status_unavailable_text" value="{{ old('contact_status_unavailable_text', $publicPage['contact_status_unavailable_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Recipient Missing Text</label><input type="text" name="contact_status_recipient_missing_text" value="{{ old('contact_status_recipient_missing_text', $publicPage['contact_status_recipient_missing_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Delivery Error Text</label><input type="text" name="contact_status_send_error_text" value="{{ old('contact_status_send_error_text', $publicPage['contact_status_send_error_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Success Text</label><input type="text" name="contact_status_success_text" value="{{ old('contact_status_success_text', $publicPage['contact_status_success_text'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Contact Items</label>
        <textarea name="contact_items_text" rows="10" class="w-full rounded-2xl border-slate-300">{{ old('contact_items_text', $contactItemsText) }}</textarea>
        <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Description</p>
    </div>

    {{-- Contact Page Hero Image --}}
    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">Contact Page Hero Image</h2>
        <p class="mt-1 text-sm text-slate-500">Displays as a full-width hero at the top of the Contact page. If not uploaded, a brand-color gradient is used.</p>
        @php $existingContactHero = trim((string) ($publicPage['contact_hero_image'] ?? '')); @endphp
        <div class="mt-4 flex flex-wrap items-start gap-6">
            <div class="flex-1 min-w-0">
                @if($existingContactHero !== '')
                    <img src="{{ asset('storage/' . ltrim($existingContactHero, '/')) }}" alt="Contact hero"
                        class="h-40 w-full max-w-md rounded-2xl border border-slate-200 object-cover shadow-sm">
                    <p class="mt-2 text-xs text-slate-400 font-mono">{{ $existingContactHero }}</p>
                @else
                    <div class="flex h-40 w-full max-w-md items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-sm text-slate-400">
                        No hero image — gradient will be used
                    </div>
                @endif
            </div>
            <div class="flex shrink-0 flex-col gap-3">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">Upload New Image</label>
                    <input type="file" name="contact_hero_image" accept="image/*"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#2D1D5C] file:px-3 file:py-1 file:text-xs file:font-semibold file:text-white">
                    <p class="mt-1 text-xs text-slate-400">Max 6MB · JPG, PNG, WebP</p>
                </div>
                @if($existingContactHero !== '')
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_contact_hero_image" value="1" class="rounded border-slate-300">
                        Remove current hero image
                    </label>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Contact Page</button>
</form>

@include('system.settings.partials._submenu-item-editor', [
    '_section'      => 'contact',
    '_sectionLabel' => trim((string) ($publicPage['contact_label'] ?? 'Contact')),
    '_items'        => $publicPage['contact_items'] ?? [],
    '_publicPage'   => $publicPage,
])
