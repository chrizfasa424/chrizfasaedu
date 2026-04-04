<form action="{{ route('settings.public-page') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Description</label>
            <textarea name="footer_description" rows="4" class="js-ck-editor w-full rounded-2xl border-slate-300">{{ old('footer_description', $publicPage['footer_description'] ?? '') }}</textarea>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Logo</label>
            <input type="file" name="footer_logo" accept="image/*" class="w-full rounded-2xl border-slate-300 bg-white">
            <label class="mt-4 inline-flex items-center text-sm font-medium text-red-600">
                <input type="checkbox" name="remove_footer_logo" value="1" class="mr-2 rounded border-slate-300">
                Remove current footer logo
            </label>
            <div class="mt-4">
                @if(!empty($publicPage['footer_logo']))
                    <img src="{{ asset('storage/' . ltrim($publicPage['footer_logo'], '/')) }}" alt="Footer logo" class="h-20 w-20 rounded-2xl border border-slate-200 bg-white object-cover p-1">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-xs text-slate-400">No logo</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Quick Links Title</label><input type="text" name="footer_quick_links_title" value="{{ old('footer_quick_links_title', $publicPage['footer_quick_links_title'] ?? 'Quick Links') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Resources Title</label><input type="text" name="footer_resources_title" value="{{ old('footer_resources_title', $publicPage['footer_resources_title'] ?? 'Resources') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Contact Title</label><input type="text" name="footer_contact_title" value="{{ old('footer_contact_title', $publicPage['footer_contact_title'] ?? 'Contact') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Footer Note</label><input type="text" name="footer_note" value="{{ old('footer_note', $publicPage['footer_note'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Footer Contact Address</label><input type="text" name="footer_contact_address" value="{{ old('footer_contact_address', $publicPage['footer_contact_address'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Footer Contact Phone</label><input type="text" name="footer_contact_phone" value="{{ old('footer_contact_phone', $publicPage['footer_contact_phone'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
        <div><label class="mb-2 block text-sm font-semibold text-slate-700">Footer Contact Email</label><input type="email" name="footer_contact_email" value="{{ old('footer_contact_email', $publicPage['footer_contact_email'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Quick Links</label>
            <textarea name="footer_quick_links_text" rows="8" class="w-full rounded-2xl border-slate-300">{{ old('footer_quick_links_text', $footerQuickLinksText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Link</p>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Resources</label>
            <textarea name="footer_resources_text" rows="8" class="w-full rounded-2xl border-slate-300">{{ old('footer_resources_text', $footerResourcesText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per item in the format: Title | Link</p>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Footer Social Links</label>
            <textarea name="footer_social_links_text" rows="8" class="w-full rounded-2xl border-slate-300" placeholder="Facebook | https://facebook.com/your-school&#10;Instagram | https://instagram.com/your-school&#10;YouTube | https://youtube.com/@your-school">{{ old('footer_social_links_text', $footerSocialLinksText) }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Use one line per social platform in the format: Platform | Full Link. Supported icons: Facebook, Instagram, YouTube, X, TikTok, LinkedIn, WhatsApp.</p>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save Footer Settings</button>
</form>
