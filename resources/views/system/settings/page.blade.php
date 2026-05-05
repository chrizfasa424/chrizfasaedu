@extends('layouts.app')

@section('title', $pageMeta['title'])
@section('header', 'Settings')

@push('styles')
<style>
    .settings-premium-scope {
        --settings-border: #cfd8e3;
        --settings-card-border: #d8e1ee;
        --settings-text: #0f172a;
        --settings-muted: #475569;
        --settings-focus: rgba(59, 130, 246, 0.2);
    }

    .settings-premium-scope form {
        color: var(--settings-text);
    }

    .settings-premium-scope form > div.rounded-xl,
    .settings-premium-scope form > section.rounded-xl,
    .settings-premium-scope form > div.rounded-2xl,
    .settings-premium-scope form > section.rounded-2xl,
    .settings-premium-scope form > div.rounded-3xl,
    .settings-premium-scope form > section.rounded-3xl {
        position: relative;
        overflow: hidden;
        border-color: var(--settings-border) !important;
        background: linear-gradient(140deg, #f7fbff 0%, #eef5ff 48%, #f7fde7 100%) !important;
        box-shadow: 0 18px 45px -30px rgba(28, 44, 67, 0.3);
    }

    .settings-premium-scope form > div.rounded-xl::before,
    .settings-premium-scope form > section.rounded-xl::before,
    .settings-premium-scope form > div.rounded-2xl::before,
    .settings-premium-scope form > section.rounded-2xl::before,
    .settings-premium-scope form > div.rounded-3xl::before,
    .settings-premium-scope form > section.rounded-3xl::before {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        top: -140px;
        right: -70px;
        border-radius: 9999px;
        background: rgba(223, 231, 83, 0.28);
        filter: blur(48px);
        pointer-events: none;
    }

    .settings-premium-scope form > div.rounded-xl::after,
    .settings-premium-scope form > section.rounded-xl::after,
    .settings-premium-scope form > div.rounded-2xl::after,
    .settings-premium-scope form > section.rounded-2xl::after,
    .settings-premium-scope form > div.rounded-3xl::after,
    .settings-premium-scope form > section.rounded-3xl::after {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        left: -120px;
        bottom: -170px;
        border-radius: 9999px;
        background: rgba(59, 130, 246, 0.16);
        filter: blur(58px);
        pointer-events: none;
    }

    .settings-premium-scope form > div.rounded-xl > *,
    .settings-premium-scope form > section.rounded-xl > *,
    .settings-premium-scope form > div.rounded-2xl > *,
    .settings-premium-scope form > section.rounded-2xl > *,
    .settings-premium-scope form > div.rounded-3xl > *,
    .settings-premium-scope form > section.rounded-3xl > * {
        position: relative;
        z-index: 1;
    }

    .settings-premium-scope form .grid > div {
        border-color: var(--settings-card-border);
    }

    .settings-premium-scope form label {
        color: #334155 !important;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .settings-premium-scope form input[type="text"],
    .settings-premium-scope form input[type="email"],
    .settings-premium-scope form input[type="url"],
    .settings-premium-scope form input[type="number"],
    .settings-premium-scope form input[type="password"],
    .settings-premium-scope form select,
    .settings-premium-scope form textarea {
        min-height: 46px;
        border: 2px solid #c7d2e3 !important;
        border-radius: 0.95rem !important;
        background-color: rgba(255, 255, 255, 0.9) !important;
        color: #0f172a !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .settings-premium-scope form textarea {
        min-height: 118px;
        line-height: 1.65;
    }

    .settings-premium-scope form input:focus,
    .settings-premium-scope form select:focus,
    .settings-premium-scope form textarea:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px var(--settings-focus) !important;
        outline: none !important;
        background-color: #ffffff !important;
    }

    .settings-premium-scope form button[type="submit"] {
        border-radius: 1rem !important;
    }

    .settings-premium-scope form .rounded-lg.border,
    .settings-premium-scope form .rounded-xl.border,
    .settings-premium-scope form .rounded-2xl.border {
        border-color: var(--settings-card-border) !important;
    }

    .settings-rich-editor .ck.ck-editor__main > .ck-editor__editable {
        min-height: 220px;
        border-radius: 0 0 1rem 1rem;
        border-color: rgba(45, 29, 92, 0.18);
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.16), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #f8f7ff 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.72), 0 12px 28px -20px rgba(15, 23, 42, 0.28);
        color: #0f172a;
        font-size: 0.975rem;
        line-height: 1.75;
        padding: 1rem 1.1rem;
    }

    .settings-rich-editor .ck.ck-toolbar {
        border-color: rgba(45, 29, 92, 0.2);
        border-radius: 1rem 1rem 0 0;
        background:
            linear-gradient(135deg, rgba(45, 29, 92, 0.96), rgba(58, 38, 112, 0.96)),
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.3), transparent 42%);
        padding: 0.75rem;
        box-shadow: 0 12px 22px -18px rgba(15, 23, 42, 0.45);
    }

    .settings-rich-editor .ck.ck-toolbar .ck-button,
    .settings-rich-editor .ck.ck-toolbar .ck-button .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-button .ck-button__label,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button .ck-button__label {
        color: #f8fafc;
    }

    .settings-rich-editor .ck.ck-button:not(.ck-disabled):hover,
    .settings-rich-editor .ck.ck-button.ck-on,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button.ck-on,
    .settings-rich-editor .ck.ck-toolbar .ck-button:not(.ck-disabled):focus {
        background: rgba(223, 231, 83, 0.96);
        color: #25333E;
    }

    .settings-rich-editor .ck.ck-button:not(.ck-disabled):hover .ck-icon,
    .settings-rich-editor .ck.ck-button.ck-on .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button.ck-on .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-button:not(.ck-disabled):focus .ck-icon,
    .settings-rich-editor .ck.ck-button:not(.ck-disabled):hover .ck-button__label,
    .settings-rich-editor .ck.ck-button.ck-on .ck-button__label,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button.ck-on .ck-button__label,
    .settings-rich-editor .ck.ck-toolbar .ck-button:not(.ck-disabled):focus .ck-button__label {
        color: #25333E;
    }

    .settings-rich-editor .ck.ck-dropdown__panel,
    .settings-rich-editor .ck.ck-list {
        border-radius: 1rem;
        border-color: rgba(45, 29, 92, 0.18);
        box-shadow: 0 20px 40px -28px rgba(15, 23, 42, 0.4);
    }

    .settings-rich-editor .ck-content h2,
    .settings-rich-editor .ck-content h3,
    .settings-rich-editor .ck-content h4 {
        color: #0f172a;
        font-weight: 700;
    }

    .settings-rich-editor .ck-content blockquote {
        border-left: 4px solid #DFE753;
        padding-left: 1rem;
        color: #334155;
        font-style: italic;
    }

    .settings-rich-editor .ck.ck-editor__editable_inline .image {
        margin: 1.5rem auto;
    }

    .settings-rich-editor .ck.ck-editor__editable_inline .image img {
        border-radius: 1rem;
        box-shadow: 0 18px 38px -28px rgba(15, 23, 42, 0.5);
    }

    .settings-rich-editor .ck.ck-editor__editable_inline .image > figcaption {
        margin-top: 0.75rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .settings-premium-scope [data-settings-submenu-content] {
        border-color: #bcd0ea !important;
        background:
            radial-gradient(circle at top right, rgba(223, 231, 83, 0.2), transparent 32%),
            radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.14), transparent 36%),
            linear-gradient(150deg, #f7fbff 0%, #eef5ff 52%, #f6fae8 100%) !important;
        box-shadow: 0 20px 40px -30px rgba(28, 44, 67, 0.35);
    }

    .settings-premium-scope [data-settings-submenu-content] > div:first-child {
        background: linear-gradient(110deg, rgba(37, 51, 62, 0.96), rgba(45, 29, 92, 0.96));
        border-bottom-color: rgba(191, 219, 254, 0.28);
    }

    .settings-premium-scope [data-settings-submenu-content] > div:first-child h3 {
        color: #f8fafc;
    }

    .settings-premium-scope [data-settings-submenu-content] > div:first-child p {
        color: rgba(226, 232, 240, 0.9);
    }

    .settings-premium-scope [data-settings-submenu-item] {
        border-top: 1px solid rgba(148, 163, 184, 0.26);
    }

    .settings-premium-scope [data-settings-submenu-summary] {
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.88), rgba(241, 245, 249, 0.72));
    }

    .settings-premium-scope [data-settings-submenu-summary]:hover {
        background: linear-gradient(90deg, rgba(223, 231, 83, 0.26), rgba(255, 255, 255, 0.96));
    }

    .settings-premium-scope [data-settings-submenu-item][open] [data-settings-submenu-summary] {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.12), rgba(223, 231, 83, 0.14), rgba(255, 255, 255, 0.96));
        padding-left: 1.25rem;
    }

    .settings-premium-scope [data-settings-submenu-body] {
        background:
            linear-gradient(155deg, rgba(255, 255, 255, 0.92), rgba(240, 248, 255, 0.85) 58%, rgba(248, 251, 232, 0.82));
    }

    .settings-premium-scope [data-settings-submenu-body] > .rounded-2xl {
        border-color: rgba(148, 163, 184, 0.35) !important;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 252, 255, 0.94)) !important;
        box-shadow: 0 18px 34px -30px rgba(15, 23, 42, 0.28);
    }
</style>
@endpush

@section('content')
<div class="space-y-6 settings-rich-editor">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#25333E]/60">Settings</p>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">{{ $pageMeta['title'] }}</h1>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $pageMeta['description'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span class="font-semibold text-slate-900">School:</span> {{ $school->name }}
            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex gap-3 overflow-x-auto pb-1">
            @foreach($settingsPages as $item)
                <a href="{{ $item['route'] }}" class="inline-flex shrink-0 items-center rounded-2xl border px-4 py-2.5 text-sm font-semibold transition {{ $settingsPage === $item['key'] ? 'border-[#DFE753] bg-[#DFE753] text-[#25333E]' : 'border-slate-200 bg-white text-slate-600 hover:border-[#25333E]/25 hover:text-[#25333E]' }}">
                    {{ $item['title'] }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="settings-premium-scope rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @include($pageMeta['partial'])
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    (() => {
        const fields = document.querySelectorAll('.js-ck-editor');
        if (!fields.length || typeof ClassicEditor === 'undefined') {
            return;
        }

        const uploadUrl = @json(route('settings.rich-text.upload'));
        const csrfToken = @json(csrf_token());
        const editors = [];

        function PremiumUploadAdapter(loader) {
            this.loader = loader;
        }

        PremiumUploadAdapter.prototype.upload = function () {
            return this.loader.file.then((file) => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('upload', file);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadUrl, true);
                xhr.responseType = 'json';
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.addEventListener('error', () => reject('Image upload failed. Please try again.'));
                xhr.addEventListener('abort', () => reject('Image upload was cancelled.'));
                xhr.addEventListener('load', () => {
                    const response = xhr.response || {};

                    if (xhr.status < 200 || xhr.status >= 300 || !response.url) {
                        reject(response.message || 'Unable to upload image.');
                        return;
                    }

                    resolve({ default: response.url });
                });

                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', (event) => {
                        if (event.lengthComputable) {
                            this.loader.uploadTotal = event.total;
                            this.loader.uploaded = event.loaded;
                        }
                    });
                }

                xhr.send(data);
            }));
        };

        PremiumUploadAdapter.prototype.abort = function () {};

        function PremiumUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new PremiumUploadAdapter(loader);
        }

        fields.forEach((field) => {
            ClassicEditor.create(field, {
                extraPlugins: [PremiumUploadAdapterPlugin],
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', 'blockQuote', '|',
                    'insertTable', 'imageUpload', '|',
                    'undo', 'redo'
                ],
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        '|',
                        'toggleImageCaption'
                    ]
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            }).then((editor) => {
                editors.push(editor);
            }).catch((error) => {
                console.error('CKEditor failed to initialize', error);
            });
        });

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', () => {
                editors.forEach((editor) => {
                    editor.updateSourceElement();
                });
            });
        });
    })();
</script>
@endpush
