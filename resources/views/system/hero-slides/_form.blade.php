@php
    $isEdit = $slide->exists;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.15fr_0.85fr]">
    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Slide Content</h3>
            <p class="mt-1 text-sm text-gray-500">Everything in the public hero section is controlled here.</p>

            <div class="mt-5 grid grid-cols-1 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Badge Text</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $slide->badge_text) }}" class="w-full rounded-xl border-gray-300" maxlength="120" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Main Heading</label>
                    <textarea name="title" rows="3" class="w-full rounded-xl border-gray-300" maxlength="255" required>{{ old('title', $slide->title) }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Subtitle</label>
                    <textarea name="subtitle" rows="4" class="w-full rounded-xl border-gray-300" maxlength="1500" required>{{ old('subtitle', $slide->subtitle) }}</textarea>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Buttons</h3>
            <p class="mt-1 text-sm text-gray-500">Use internal links like `/apply` or anchors like `#programs`.</p>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Button 1 Text</label>
                    <input type="text" name="button_1_text" value="{{ old('button_1_text', $slide->button_1_text) }}" class="w-full rounded-xl border-gray-300" maxlength="80" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Button 1 Link</label>
                    <input type="text" name="button_1_link" value="{{ old('button_1_link', $slide->button_1_link) }}" class="w-full rounded-xl border-gray-300" maxlength="255" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Button 2 Text</label>
                    <input type="text" name="button_2_text" value="{{ old('button_2_text', $slide->button_2_text) }}" class="w-full rounded-xl border-gray-300" maxlength="80" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Button 2 Link</label>
                    <input type="text" name="button_2_link" value="{{ old('button_2_link', $slide->button_2_link) }}" class="w-full rounded-xl border-gray-300" maxlength="255" required>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Floating Card</h3>
            <p class="mt-1 text-sm text-gray-500">This card sits on the image area and animates in after the text.</p>

            <div class="mt-5 grid grid-cols-1 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">School Name</label>
                    <input type="text" name="school_name" value="{{ old('school_name', $slide->school_name ?: auth()->user()->school?->name) }}" class="w-full rounded-xl border-gray-300" maxlength="160" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Right Card Title</label>
                    <input type="text" name="right_card_title" value="{{ old('right_card_title', $slide->right_card_title) }}" class="w-full rounded-xl border-gray-300" maxlength="160" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Right Card Text</label>
                    <textarea name="right_card_text" rows="4" class="w-full rounded-xl border-gray-300" maxlength="1000" required>{{ old('right_card_text', $slide->right_card_text) }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Slide Media</h3>
            <p class="mt-1 text-sm text-gray-500">Accepted formats: JPG, PNG, WEBP.</p>

            <div class="mt-5 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Hero Image</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full rounded-xl border-gray-300" {{ $isEdit ? '' : 'required' }}>
                </div>

                <div>
                    <p class="mb-2 text-sm font-medium text-gray-700">Preview</p>
                    @if($isEdit && $slide->image_path)
                        <img src="{{ asset('storage/' . ltrim($slide->image_path, '/')) }}" alt="{{ $slide->title }}" class="h-72 w-full rounded-2xl object-cover border border-gray-200 bg-gray-50">
                    @else
                        <div class="flex h-72 w-full items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 text-sm text-gray-400">
                            No image uploaded yet
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Publishing</h3>

            <div class="mt-5 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Order</label>
                    <select name="order" class="w-full rounded-xl border-gray-300" required>
                        @for($i = 1; $i <= $maxSlides; $i++)
                            <option value="{{ $i }}" @selected((int) old('order', $slide->order ?: 1) === $i)>Slide {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <input type="checkbox" name="is_active" value="1" class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" @checked((bool) old('is_active', $slide->is_active ?? true))>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">Active on website</span>
                        <span class="block text-sm text-gray-500">Only active slides are shown in the homepage slider. Maximum active slides: 4.</span>
                    </span>
                </label>
            </div>
        </section>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
                {{ $isEdit ? 'Update Slide' : 'Create Slide' }}
            </button>
            <a href="{{ route('system.hero-slides.index') }}" class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:text-gray-900">
                Cancel
            </a>
        </div>
    </div>
</div>
