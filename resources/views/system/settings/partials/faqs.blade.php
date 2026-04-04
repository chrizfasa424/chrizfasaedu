@php
    $existingFaqs = $publicPage['faqs'] ?? [];
@endphp

<form action="{{ route('settings.faqs.save') }}" method="POST" id="faq-admin-form" class="space-y-8">
    @csrf
    @method('POST')

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">FAQ Categories & Questions</h2>
                <p class="mt-1 text-sm text-slate-500">Manage all FAQ categories and their questions. Changes are published live to the public FAQ page.</p>
            </div>
            <button type="button" id="add-faq-category"
                    class="inline-flex items-center gap-2 rounded-2xl border border-dashed border-[#2D1D5C] bg-[#2D1D5C]/5 px-4 py-2 text-sm font-semibold text-[#2D1D5C] transition hover:bg-[#2D1D5C] hover:text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Category
            </button>
        </div>

        <div id="faq-categories-container" class="mt-5 space-y-4">
            @forelse($existingFaqs as $catIndex => $cat)
            <div class="faq-category-block rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden"
                 data-category-index="{{ $catIndex }}">
                {{-- Category header --}}
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-3">
                    <div class="flex flex-1 flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Category ID</label>
                            <input type="text"
                                   name="categories[{{ $catIndex }}][id]"
                                   value="{{ $cat['id'] ?? '' }}"
                                   placeholder="e.g. admissions"
                                   class="w-40 rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-mono text-slate-700 focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Label</label>
                            <input type="text"
                                   name="categories[{{ $catIndex }}][label]"
                                   value="{{ $cat['label'] ?? '' }}"
                                   placeholder="Category label"
                                   class="w-48 rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-900 focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 faq-item-count">
                            {{ count($cat['items'] ?? []) }} question{{ count($cat['items'] ?? []) !== 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="faq-toggle-category inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 faq-toggle-icon"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                            Toggle
                        </button>
                        <button type="button" class="faq-remove-category inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            Remove
                        </button>
                    </div>
                </div>

                {{-- FAQ items --}}
                <div class="faq-items-container px-5 py-4 space-y-3">
                    @forelse($cat['items'] ?? [] as $itemIndex => $faqItem)
                    <div class="faq-item-block rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Question</label>
                                    <input type="text"
                                           name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][q]"
                                           value="{{ $faqItem['q'] ?? '' }}"
                                           placeholder="Enter the question..."
                                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Answer</label>
                                    <textarea name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][a]"
                                              rows="3"
                                              placeholder="Enter the answer. You may use basic HTML like &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;..."
                                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">{{ $faqItem['a'] ?? '' }}</textarea>
                                    <p class="mt-1 text-xs text-slate-400">Supports basic HTML: &lt;strong&gt;, &lt;ul&gt;&lt;li&gt;, &lt;a&gt;, line breaks.</p>
                                </div>
                            </div>
                            <button type="button" class="faq-remove-item mt-6 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-red-200 text-red-400 transition hover:bg-red-500 hover:text-white">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 italic">No questions in this category yet.</p>
                    @endforelse

                    <button type="button"
                            class="faq-add-item mt-1 inline-flex items-center gap-2 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Question
                    </button>
                </div>
            </div>
            @empty
            <div id="faq-empty-state" class="rounded-2xl border border-dashed border-slate-300 bg-white py-10 text-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 h-8 w-8 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                <p class="text-sm font-semibold text-slate-400">No FAQ categories yet.</p>
                <p class="mt-1 text-xs text-slate-400">Click "Add Category" to get started.</p>
            </div>
            @endforelse
        </div>
    </div>

    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-[#2D1D5C] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        Save All FAQs
    </button>
</form>

{{-- ── Templates (hidden, cloned by JS) ── --}}
<template id="faq-category-template">
    <div class="faq-category-block rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" data-category-index="__CAT__">
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50 px-5 py-3">
            <div class="flex flex-1 flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Category ID</label>
                    <input type="text" name="categories[__CAT__][id]" placeholder="e.g. my-category"
                           class="w-40 rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-mono text-slate-700 focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Label</label>
                    <input type="text" name="categories[__CAT__][label]" placeholder="Category label"
                           class="w-48 rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-900 focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500 faq-item-count">0 questions</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="faq-toggle-category inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5 faq-toggle-icon"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    Toggle
                </button>
                <button type="button" class="faq-remove-category inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    Remove
                </button>
            </div>
        </div>
        <div class="faq-items-container px-5 py-4 space-y-3">
            <p class="text-sm text-slate-400 italic faq-empty-notice">No questions in this category yet.</p>
            <button type="button" class="faq-add-item mt-1 inline-flex items-center gap-2 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Question
            </button>
        </div>
    </div>
</template>

<template id="faq-item-template">
    <div class="faq-item-block rounded-2xl border border-slate-100 bg-slate-50 p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 space-y-3">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Question</label>
                    <input type="text" name="" placeholder="Enter the question..."
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-400">Answer</label>
                    <textarea name="" rows="3" placeholder="Enter the answer..."
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#2D1D5C]/10"></textarea>
                    <p class="mt-1 text-xs text-slate-400">Supports basic HTML: &lt;strong&gt;, &lt;ul&gt;&lt;li&gt;, &lt;a&gt;, line breaks.</p>
                </div>
            </div>
            <button type="button" class="faq-remove-item mt-6 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-red-200 text-red-400 transition hover:bg-red-500 hover:text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</template>

<script>
(function () {
    const container   = document.getElementById('faq-categories-container');
    const catTemplate = document.getElementById('faq-category-template');
    const itemTemplate = document.getElementById('faq-item-template');
    const addCatBtn   = document.getElementById('add-faq-category');
    const emptyState  = document.getElementById('faq-empty-state');

    function reindexAll() {
        container.querySelectorAll('.faq-category-block').forEach(function (catBlock, catIdx) {
            catBlock.setAttribute('data-category-index', catIdx);
            catBlock.querySelector('input[name*="[id]"]').name = 'categories[' + catIdx + '][id]';
            catBlock.querySelector('input[name*="[label]"]').name = 'categories[' + catIdx + '][label]';
            catBlock.querySelectorAll('.faq-item-block').forEach(function (itemBlock, itemIdx) {
                itemBlock.querySelector('input').name = 'categories[' + catIdx + '][items][' + itemIdx + '][q]';
                itemBlock.querySelector('textarea').name = 'categories[' + catIdx + '][items][' + itemIdx + '][a]';
            });
            updateItemCount(catBlock);
        });
        if (emptyState) {
            emptyState.style.display = container.querySelectorAll('.faq-category-block').length === 0 ? '' : 'none';
        }
    }

    function updateItemCount(catBlock) {
        const count = catBlock.querySelectorAll('.faq-item-block').length;
        const badge = catBlock.querySelector('.faq-item-count');
        if (badge) badge.textContent = count + ' question' + (count !== 1 ? 's' : '');
    }

    function hideEmptyNotice(catBlock) {
        const notice = catBlock.querySelector('.faq-empty-notice');
        if (notice) notice.style.display = 'none';
    }

    function bindCategoryEvents(catBlock) {
        // Toggle collapse
        const toggleBtn = catBlock.querySelector('.faq-toggle-category');
        const itemsContainer = catBlock.querySelector('.faq-items-container');
        if (toggleBtn && itemsContainer) {
            toggleBtn.addEventListener('click', function () {
                itemsContainer.style.display = itemsContainer.style.display === 'none' ? '' : 'none';
            });
        }

        // Remove category
        const removeBtn = catBlock.querySelector('.faq-remove-category');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                if (confirm('Remove this entire category and all its questions?')) {
                    catBlock.remove();
                    reindexAll();
                }
            });
        }

        // Add question
        const addItemBtn = catBlock.querySelector('.faq-add-item');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', function () {
                addItemToCategory(catBlock);
            });
        }

        // Remove existing items
        catBlock.querySelectorAll('.faq-remove-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                btn.closest('.faq-item-block').remove();
                reindexAll();
            });
        });
    }

    function addItemToCategory(catBlock) {
        hideEmptyNotice(catBlock);
        const itemsContainer = catBlock.querySelector('.faq-items-container');
        const addBtn = itemsContainer.querySelector('.faq-add-item');
        const clone = itemTemplate.content.cloneNode(true);
        const newItem = clone.querySelector('.faq-item-block');
        newItem.querySelector('.faq-remove-item').addEventListener('click', function () {
            newItem.remove();
            reindexAll();
        });
        itemsContainer.insertBefore(newItem, addBtn);
        reindexAll();
        newItem.querySelector('input').focus();
    }

    // Add category
    addCatBtn.addEventListener('click', function () {
        if (emptyState) emptyState.style.display = 'none';
        const catIdx = container.querySelectorAll('.faq-category-block').length;
        const clone  = catTemplate.content.cloneNode(true);
        const catBlock = clone.querySelector('.faq-category-block');
        catBlock.setAttribute('data-category-index', catIdx);
        catBlock.querySelector('input[name*="[id]"]').name    = 'categories[' + catIdx + '][id]';
        catBlock.querySelector('input[name*="[label]"]').name = 'categories[' + catIdx + '][label]';
        bindCategoryEvents(catBlock);
        container.appendChild(catBlock);
        catBlock.querySelector('input[name*="[id]"]').focus();
    });

    // Bind all existing categories
    container.querySelectorAll('.faq-category-block').forEach(bindCategoryEvents);
})();
</script>
