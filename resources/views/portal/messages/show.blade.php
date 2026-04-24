@extends('layouts.app')
@section('title', $message->subject)
@section('header', 'Message')

@section('content')
<div class="w-full max-w-none space-y-5">
    <a href="{{ route('portal.messages.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-[#2D1D5C]">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        Back to Inbox
    </a>

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-[#2D1D5C] to-[#3A2872] px-6 py-5">
            <p class="mb-1 text-[11px] font-semibold uppercase tracking-widest text-white/60">Message</p>
            <h2 class="text-xl font-bold leading-snug text-white">{{ $message->subject }}</h2>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-white/70">
                <span class="flex items-center gap-1">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/>
                    </svg>
                    {{ $message->sender->full_name }}
                </span>
                <span>&middot;</span>
                <span>{{ $message->created_at->format('d M Y, g:i A') }}</span>
                @if($recipient->read_at)
                    <span>&middot;</span>
                    <span class="flex items-center gap-1 text-emerald-300">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                        Read {{ $recipient->read_at->diffForHumans() }}
                    </span>
                @endif
            </div>
        </div>

        <div class="prose prose-sm max-w-none px-6 py-6 text-slate-700 leading-relaxed">
            {!! \App\Support\RichTextSanitizer::sanitize((string) $message->body) !!}
        </div>
    </div>

    @if($replies->isNotEmpty())
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-3.5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                    Your Replies
                    <span class="ml-1.5 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ $replies->count() }}</span>
                </p>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($replies as $reply)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-end gap-3">
                            <div class="max-w-lg rounded-2xl rounded-tr-md bg-[#2D1D5C] px-4 py-3">
                                <div class="prose prose-sm max-w-none text-white prose-headings:text-white prose-strong:text-white prose-a:text-white">
                                    {!! \App\Support\RichTextSanitizer::sanitize((string) $reply->body) !!}
                                </div>
                                <p class="mt-1.5 text-right text-[10px] text-white/50">{{ $reply->created_at->format('d M Y, g:i A') }}</p>
                            </div>
                            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#2D1D5C] text-xs font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" id="replyForm">
        <div class="border-b border-slate-100 px-5 py-3.5">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Write a Reply</p>
        </div>
        <form action="{{ route('portal.messages.reply', $message) }}" method="POST" class="space-y-4 p-5">
            @csrf
            <textarea id="replyBody" name="body" rows="5" placeholder="Type your reply here..." class="js-ck-editor w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-[#2D1D5C] focus:outline-none focus:ring-1 focus:ring-[#2D1D5C] resize-none @error('body') border-red-400 @enderror">{{ old('body') }}</textarea>
            @error('body')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-400">Your reply will be seen by the administration.</p>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#2D1D5C] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#3A2872] active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/>
                    </svg>
                    Send Reply
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
    const replyBody = document.getElementById('replyBody');
    const form = replyBody?.closest('form');

    if (replyBody && typeof ClassicEditor !== 'undefined') {
        ClassicEditor.create(replyBody, {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', 'blockQuote', '|',
                'undo', 'redo'
            ],
        }).then((editor) => {
            form?.addEventListener('submit', () => {
                editor.updateSourceElement();
            });
        }).catch((error) => {
            console.error('CKEditor failed to initialize', error);
        });
    }

    @if(session('success'))
        setTimeout(() => {
            document.getElementById('replyForm')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 200);
    @endif
})();
</script>
@endpush
