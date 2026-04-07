<?php $__env->startSection('title', $pageMeta['title']); ?>
<?php $__env->startSection('header', 'Settings'); ?>

<?php $__env->startPush('styles'); ?>
<style>
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
        color: #2D1D5C;
    }

    .settings-rich-editor .ck.ck-button:not(.ck-disabled):hover .ck-icon,
    .settings-rich-editor .ck.ck-button.ck-on .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button.ck-on .ck-icon,
    .settings-rich-editor .ck.ck-toolbar .ck-button:not(.ck-disabled):focus .ck-icon,
    .settings-rich-editor .ck.ck-button:not(.ck-disabled):hover .ck-button__label,
    .settings-rich-editor .ck.ck-button.ck-on .ck-button__label,
    .settings-rich-editor .ck.ck-toolbar .ck-dropdown__button.ck-on .ck-button__label,
    .settings-rich-editor .ck.ck-toolbar .ck-button:not(.ck-disabled):focus .ck-button__label {
        color: #2D1D5C;
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 settings-rich-editor">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#2D1D5C]/60">Settings</p>
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900"><?php echo e($pageMeta['title']); ?></h1>
                <p class="mt-3 text-sm leading-7 text-slate-600"><?php echo e($pageMeta['description']); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span class="font-semibold text-slate-900">School:</span> <?php echo e($school->name); ?>

            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex gap-3 overflow-x-auto pb-1">
            <?php $__currentLoopData = $settingsPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['route']); ?>" class="inline-flex shrink-0 items-center rounded-2xl border px-4 py-2.5 text-sm font-semibold transition <?php echo e($settingsPage === $item['key'] ? 'border-[#DFE753] bg-[#DFE753] text-[#2D1D5C]' : 'border-slate-200 bg-white text-slate-600 hover:border-[#2D1D5C]/25 hover:text-[#2D1D5C]'); ?>">
                    <?php echo e($item['title']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <?php echo $__env->make($pageMeta['partial'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    (() => {
        const fields = document.querySelectorAll('.js-ck-editor');
        if (!fields.length || typeof ClassicEditor === 'undefined') {
            return;
        }

        const uploadUrl = <?php echo json_encode(route('settings.rich-text.upload'), 15, 512) ?>;
        const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/system/settings/page.blade.php ENDPATH**/ ?>