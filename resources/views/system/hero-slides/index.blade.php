@extends('layouts.app')

@section('title', 'Hero Slides')
@section('header', 'Hero Slider Studio')

@section('content')
<div class="space-y-8">
    <section class="overflow-hidden rounded-[2rem] border border-[#D9D4EA] bg-gradient-to-br from-[#24164A] via-[#2D1D5C] to-[#43316F] text-white shadow-[0_28px_80px_-42px_rgba(17,24,39,0.65)]">
        <div class="grid gap-8 px-6 py-7 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:py-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-white/60">Homepage Content Studio</p>
                <h2 class="mt-4 max-w-2xl text-3xl font-black leading-tight sm:text-[2.3rem]">Design a stronger first impression with four polished homepage hero slides.</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200/90 sm:text-base">Each slide controls its own heading, supporting text, calls to action, right-side message card, order, and image. Use this space to create a cleaner landing-page story for KG, Primary, and Secondary families.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @if($slides->count() < $maxSlides)
                        <a href="{{ route('system.hero-slides.create') }}" class="inline-flex items-center rounded-full bg-[#DFE753] px-5 py-3 text-sm font-bold text-[#2D1D5C] transition duration-200 hover:-translate-y-0.5 hover:bg-white">
                            Add New Slide
                        </a>
                    @endif
                    <a href="{{ route('public.home') }}" target="_blank" class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:border-[#DFE753] hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                        Preview Homepage
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                <div class="rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/55">Configured</p>
                    <p class="mt-3 text-4xl font-black text-white">{{ $slides->count() }}</p>
                    <p class="mt-2 text-sm text-slate-200/80">Slides created out of {{ $maxSlides }} maximum.</p>
                </div>
                <div class="rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/55">Active</p>
                    <p class="mt-3 text-4xl font-black text-[#DFE753]">{{ $slides->where('is_active', true)->count() }}</p>
                    <p class="mt-2 text-sm text-slate-200/80">Only active slides rotate on the homepage.</p>
                </div>
                <div class="rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/55">Images Pending</p>
                    <p class="mt-3 text-4xl font-black text-white">{{ $slides->filter(fn ($slide) => \Illuminate\Support\Str::startsWith((string) $slide->image_path, 'schools/logos/'))->count() }}</p>
                    <p class="mt-2 text-sm text-slate-200/80">Slides still waiting for real wide school photography.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.6rem] border border-[#DDD8ED] bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#2D1D5C]/60">Best Practice</p>
            <h3 class="mt-3 text-lg font-bold text-slate-900">Use one clear message per slide</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Let each slide focus on a different school promise: admissions, academics, student life, or parental confidence.</p>
        </article>
        <article class="rounded-[1.6rem] border border-[#DDD8ED] bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#2D1D5C]/60">Visual Direction</p>
            <h3 class="mt-3 text-lg font-bold text-slate-900">Choose real school-wide images</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Landscape photos of learners, classrooms, labs, campus spaces, and school activities will make the homepage feel much more premium.</p>
        </article>
        <article class="rounded-[1.6rem] border border-[#DDD8ED] bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#2D1D5C]/60">Action Flow</p>
            <h3 class="mt-3 text-lg font-bold text-slate-900">Guide families to the right page</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Use buttons for admissions, programs, academics, and contact so the hero becomes a real navigation tool, not only a banner.</p>
        </article>
    </section>

    @if($slides->isEmpty())
        <section class="rounded-[2rem] border border-dashed border-[#C8C0DF] bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto max-w-2xl">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#2D1D5C] text-[#DFE753] shadow-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-8 w-8"><rect x="3.75" y="5.25" width="16.5" height="13.5" rx="2.25"/><circle cx="8.25" cy="10.125" r="1.125"/><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 15-4.5-4.5L6 20.25"/></svg>
                </div>
                <h3 class="mt-6 text-2xl font-black text-slate-900">No hero slides yet</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">Create your first slide to start building a modern homepage hero with premium visuals, stronger messaging, and clear call-to-action buttons.</p>
                <a href="{{ route('system.hero-slides.create') }}" class="mt-6 inline-flex items-center rounded-full bg-[#2D1D5C] px-6 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-[#DFE753] hover:text-[#2D1D5C]">
                    Create First Slide
                </a>
            </div>
        </section>
    @else
        <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2D1D5C]/55">Visual Ordering</p>
                    <h3 class="mt-3 text-xl font-black text-slate-900">Drag and drop to reorder the homepage slider</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">Move the cards below into the sequence you want. The homepage will follow this order automatically after you drop a card.</p>
                </div>
                <div id="hero-order-status" class="inline-flex items-center rounded-full border border-[#DFE753]/60 bg-[#F8F9D9] px-4 py-2 text-sm font-semibold text-[#2D1D5C]">
                    Drag the handle and release to save order automatically
                </div>
            </div>

            <div id="hero-slide-sortable" data-reorder-url="{{ route('system.hero-slides.reorder') }}" class="mt-6 grid gap-4 xl:grid-cols-4">
                @foreach($slides as $slide)
                    <article draggable="true" data-slide-id="{{ $slide->id }}" class="hero-sort-card group cursor-move rounded-[1.6rem] border border-[#DDD8ED] bg-[#FBFAFE] p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#DFE753] hover:shadow-md">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="hero-sort-order text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">Slide {{ $slide->order }}</p>
                                <h4 class="mt-2 text-base font-black leading-snug text-slate-900">{{ \Illuminate\Support\Str::limit($slide->title, 60) }}</h4>
                            </div>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#2D1D5C]/10 bg-white text-[#2D1D5C] shadow-sm transition group-hover:border-[#DFE753] group-hover:bg-[#DFE753]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h.008v.008H8.25V6.75Zm0 5.25h.008v.008H8.25V12Zm0 5.25h.008v.008H8.25v-.008Zm7.5-10.5h.008v.008h-.008V6.75Zm0 5.25h.008v.008h-.008V12Zm0 5.25h.008v.008h-.008v-.008Z"/></svg>
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-medium text-slate-600">{{ $slide->badge_text }}</p>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if(\Illuminate\Support\Str::startsWith((string) $slide->image_path, 'schools/logos/'))
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Real image needed</span>
                                @endif
                            </div>
                            <a href="{{ route('system.hero-slides.edit', $slide->id) }}" class="text-sm font-semibold text-[#2D1D5C] transition hover:text-[#5A4792]">Edit</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach($slides as $slide)
                @php
                    $usesLogoPlaceholder = \Illuminate\Support\Str::startsWith((string) $slide->image_path, 'schools/logos/');
                @endphp
                <article class="overflow-hidden rounded-[2rem] border border-[#DDD8ED] bg-white shadow-[0_24px_70px_-42px_rgba(15,23,42,0.35)]">
                    <div class="grid gap-0 lg:grid-cols-[0.95fr_1.05fr]">
                        <div class="relative min-h-[23rem] overflow-hidden border-b border-[#EEEAF8] lg:border-b-0 lg:border-r">
                            @if(!$usesLogoPlaceholder)
                                <img src="{{ asset('storage/' . ltrim($slide->image_path, '/')) }}" alt="{{ $slide->title }}" class="absolute inset-0 h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-slate-900/5 to-transparent"></div>
                            @else
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_28%),linear-gradient(135deg,#201244_0%,#2D1D5C_45%,#4D3A87_100%)]"></div>
                                <div class="absolute -right-10 top-8 h-44 w-44 rounded-full border border-white/10 bg-white/5 blur-sm"></div>
                                <div class="absolute -left-12 bottom-8 h-36 w-36 rounded-full border border-[#DFE753]/35 bg-[#DFE753]/12 blur-sm"></div>
                                <div class="absolute inset-0 p-6 text-white">
                                    <div class="flex h-full flex-col justify-between">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85">Placeholder Visual</p>
                                                <h3 class="mt-4 max-w-xs text-2xl font-black leading-tight">Upload a real school-wide hero image for this slide</h3>
                                            </div>
                                            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border border-white/15 bg-white/10 shadow-xl">
                                                <img src="{{ asset('storage/' . ltrim($slide->image_path, '/')) }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                            </div>
                                        </div>
                                        <div class="rounded-[1.4rem] border border-white/12 bg-white/10 p-4 backdrop-blur">
                                            <p class="text-sm leading-6 text-white/88">Replace this with a wide campus, classroom, laboratory, or student-life photograph to make the homepage look more like a professional school landing page.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 lg:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#2D1D5C]/65">{{ $slide->badge_text }}</p>
                                    <h3 class="mt-3 text-2xl font-black leading-tight text-slate-900">{{ $slide->title }}</h3>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Slide {{ $slide->order }}</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($slide->subtitle, 210) }}</p>

                            <div class="mt-5 rounded-[1.5rem] border border-[#E7E2F4] bg-[#F8F7FD] p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#2D1D5C]/55">{{ $slide->school_name }}</p>
                                <h4 class="mt-2 text-lg font-bold text-slate-900">{{ $slide->right_card_title }}</h4>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($slide->right_card_text, 145) }}</p>
                            </div>

                            <div class="mt-5 grid gap-3 text-sm text-slate-600">
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                    <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Primary CTA</span>
                                    <span class="mt-1 block font-semibold text-slate-900">{{ $slide->button_1_text }}</span>
                                    <span class="mt-1 block break-all text-xs text-slate-500">{{ $slide->button_1_link }}</span>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                    <span class="block text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Secondary CTA</span>
                                    <span class="mt-1 block font-semibold text-slate-900">{{ $slide->button_2_text }}</span>
                                    <span class="mt-1 block break-all text-xs text-slate-500">{{ $slide->button_2_link }}</span>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-2">
                                <a href="{{ route('system.hero-slides.edit', $slide->id) }}" class="inline-flex items-center rounded-full border border-[#2D1D5C]/15 bg-white px-4 py-2.5 text-sm font-semibold text-[#2D1D5C] transition duration-200 hover:-translate-y-0.5 hover:border-[#DFE753] hover:bg-[#DFE753]">
                                    Edit Slide
                                </a>

                                <form action="{{ route('system.hero-slides.toggle', $slide->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $slide->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center rounded-full px-4 py-2.5 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 {{ $slide->is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                        {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('system.hero-slides.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this hero slide permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-full bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const container = document.getElementById('hero-slide-sortable');
        const status = document.getElementById('hero-order-status');

        if (!container) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        let draggedCard = null;

        const getCards = () => Array.from(container.querySelectorAll('[data-slide-id]'));

        const setStatus = (message, classes) => {
            if (!status) {
                return;
            }

            status.textContent = message;
            status.className = classes;
        };

        const defaultStatusClass = 'inline-flex items-center rounded-full border border-[#DFE753]/60 bg-[#F8F9D9] px-4 py-2 text-sm font-semibold text-[#2D1D5C]';
        const savingStatusClass = 'inline-flex items-center rounded-full border border-[#2D1D5C]/15 bg-[#EEF2FF] px-4 py-2 text-sm font-semibold text-[#2D1D5C]';
        const successStatusClass = 'inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700';
        const errorStatusClass = 'inline-flex items-center rounded-full border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700';

        const persistOrder = async () => {
            const order = getCards().map((card) => Number(card.dataset.slideId));
            setStatus('Saving new slide order...', savingStatusClass);

            try {
                const response = await fetch(container.dataset.reorderUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                    body: JSON.stringify({ order }),
                });

                if (!response.ok) {
                    throw new Error('Unable to save order');
                }

                getCards().forEach((card, index) => {
                    const label = card.querySelector('.hero-sort-order');
                    if (label) {
                        label.textContent = `Slide ${index + 1}`;
                    }
                });

                setStatus('Slide order saved successfully.', successStatusClass);
                window.setTimeout(() => setStatus('Drag the handle and release to save order automatically', defaultStatusClass), 2200);
            } catch (error) {
                setStatus('Unable to save order. Refreshing the page...', errorStatusClass);
                window.setTimeout(() => window.location.reload(), 900);
            }
        };

        getCards().forEach((card) => {
            card.addEventListener('dragstart', () => {
                draggedCard = card;
                card.classList.add('opacity-60', 'scale-[0.98]');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('opacity-60', 'scale-[0.98]');
                draggedCard = null;
            });

            card.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!draggedCard || draggedCard === card) {
                    return;
                }

                const bounds = card.getBoundingClientRect();
                const insertAfter = event.clientY > bounds.top + bounds.height / 2;

                if (insertAfter) {
                    card.after(draggedCard);
                } else {
                    card.before(draggedCard);
                }
            });

            card.addEventListener('drop', (event) => {
                event.preventDefault();
                persistOrder();
            });
        });
    })();
</script>
@endpush

