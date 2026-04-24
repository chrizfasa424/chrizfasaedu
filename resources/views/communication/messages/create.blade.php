@extends('layouts.app')
@section('title', 'Compose Message')
@section('header', 'Compose Message')

@section('content')
<div class="w-full max-w-none">
    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#2D1D5C]/8 text-[#2D1D5C]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                </span>
                <div>
                    <h3 class="font-semibold text-slate-800">New Message</h3>
                    <p class="text-xs text-slate-400">Send a message to students, parents or a class</p>
                </div>
            </div>
        </div>

        <form action="{{ route('messages.store') }}" method="POST" class="divide-y divide-slate-100">
            @csrf

            <div class="space-y-5 px-6 py-5">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Send To</label>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4" id="audienceGrid">
                        @foreach([
                            ['all_students', 'All Students', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                            ['all_parents',  'All Parents',  'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0z'],
                            ['all_portal',   'Everyone',     'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0z'],
                            ['class',        'Specific Class','M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5zM4 13a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6zM16 13a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-6z'],
                        ] as [$val, $lbl, $icon])
                            <label class="audience-card cursor-pointer" data-audience="{{ $val }}">
                                <input type="radio" name="audience" value="{{ $val }}" class="sr-only" {{ old('audience', 'all_students') === $val ? 'checked' : '' }}>
                                <div class="audience-option flex flex-col items-center gap-2 rounded-xl border-2 px-3 py-4 text-center transition {{ old('audience', 'all_students') === $val ? 'border-[#2D1D5C] bg-[#2D1D5C] text-white shadow-md' : 'border-slate-200 bg-white text-slate-600 hover:border-[#2D1D5C]/40' }}">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                    </svg>
                                    <span class="text-xs font-semibold leading-tight">{{ $lbl }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('audience')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div id="classRow" style="display:{{ old('audience') === 'class' ? 'block' : 'none' }}">
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="class_id">Select Class</label>
                    <select id="class_id" name="class_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C]">
                        <option value="">-- choose a class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Enter message subject..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C] @error('subject') border-red-400 @enderror">
                    @error('subject')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="body">Message</label>
                    <textarea id="body" name="body" rows="10" placeholder="Type your message here..." class="js-ck-editor w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C] resize-none @error('body') border-red-400 @enderror">{{ old('body') }}</textarea>
                    <div class="mt-1 flex justify-between">
                        @error('body')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                        <span id="charCount" class="ml-auto text-xs text-slate-400">0 characters</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 bg-slate-50/70 px-6 py-4">
                <a href="{{ route('messages.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">&larr; Back</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2D1D5C] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3A2872] active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/>
                    </svg>
                    Send Message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
(() => {
    document.querySelectorAll('.audience-card').forEach((card) => {
        card.addEventListener('click', () => {
            const val = card.dataset.audience;
            card.querySelector('input[type=radio]').checked = true;

            document.querySelectorAll('.audience-option').forEach((opt) => {
                opt.classList.remove('border-[#2D1D5C]', 'bg-[#2D1D5C]', 'text-white', 'shadow-md');
                opt.classList.add('border-slate-200', 'bg-white', 'text-slate-600');
            });

            const selected = card.querySelector('.audience-option');
            selected.classList.add('border-[#2D1D5C]', 'bg-[#2D1D5C]', 'text-white', 'shadow-md');
            selected.classList.remove('border-slate-200', 'bg-white', 'text-slate-600');

            const classRow = document.getElementById('classRow');
            if (classRow) {
                classRow.style.display = val === 'class' ? 'block' : 'none';
            }
        });
    });

    const bodyEl = document.getElementById('body');
    const counter = document.getElementById('charCount');
    const form = bodyEl?.closest('form');

    const updateCount = (html) => {
        if (!counter) {
            return;
        }
        const ghost = document.createElement('div');
        ghost.innerHTML = html ?? '';
        const text = (ghost.textContent || ghost.innerText || '').trim();
        const n = text.length;
        counter.textContent = n.toLocaleString() + ' character' + (n !== 1 ? 's' : '');
    };

    if (!bodyEl) {
        return;
    }

    if (typeof ClassicEditor === 'undefined') {
        updateCount(bodyEl.value);
        bodyEl.addEventListener('input', () => updateCount(bodyEl.value));
        return;
    }

    ClassicEditor.create(bodyEl, {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', '|',
            'bulletedList', 'numberedList', 'blockQuote', '|',
            'undo', 'redo',
        ],
    }).then((editor) => {
        updateCount(editor.getData());

        editor.model.document.on('change:data', () => {
            updateCount(editor.getData());
        });

        form?.addEventListener('submit', () => {
            editor.updateSourceElement();
        });
    }).catch((error) => {
        console.error('CKEditor failed to initialize', error);
        updateCount(bodyEl.value);
        bodyEl.addEventListener('input', () => updateCount(bodyEl.value));
    });
})();
</script>
@endpush
