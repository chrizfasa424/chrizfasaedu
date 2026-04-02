@props(['school' => null, 'publicPage' => []])

@php
    use App\Models\HeroSlide;
    use App\Support\ThemePalette;
    use Illuminate\Support\Str;

    $slides = $school
        ? HeroSlide::query()
            ->select([
                'id',
                'school_id',
                'title',
                'subtitle',
                'badge_text',
                'button_1_text',
                'button_1_link',
                'button_2_text',
                'button_2_link',
                'right_card_title',
                'right_card_text',
                'school_name',
                'image_path',
                'order',
                'is_active',
            ])
            ->where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->take(4)
            ->get()
        : collect();

    $theme = ThemePalette::fromPublicPage($publicPage);
    $heroPrimary = $theme['primary']['500'];
    $heroPrimarySoft = $theme['primary']['100'];
    $heroPrimaryDeep = $theme['primary']['700'];
    $heroSecondary = $theme['secondary']['500'];
    $heroSecondarySoft = $theme['secondary']['100'];
    $heroHeading = $theme['ink'];
    $heroBody = $theme['muted'];
    $heroSurface = $theme['surface'];
    $heroSoftSurface = $theme['soft_surface'];
    $heroSiteBackground = $theme['site_background'];
    $heroStyle = $theme['theme_style'];
    $heroTextOnPrimary = $theme['primary_text_on_primary'];
    $heroTextOnSecondary = $theme['primary_text_on_secondary'];

    $metrics = collect(data_get($publicPage, 'metrics', []))
        ->filter(function ($metric) {
            return filled(data_get($metric, 'value')) || filled(data_get($metric, 'label'));
        })
        ->values();

    $resolveLink = static function (?string $link): string {
        $link = trim((string) $link);

        if ($link === '') {
            return '#';
        }

        return Str::startsWith($link, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])
            ? $link
            : url($link);
    };
@endphp

@if($slides->isNotEmpty())
<section
    class="relative overflow-hidden border-b border-slate-200"
    style="background-color: {{ $heroStyle === 'minimal-clean' ? $heroSurface : $heroSoftSurface }};"
    x-data="{
        active: 0,
        slides: {{ $slides->count() }},
        timer: null,
        autoplay() {
            if (this.timer) clearInterval(this.timer)
            if (this.slides <= 1) return
            this.timer = setInterval(() => {
                this.active = (this.active + 1) % this.slides
            }, 5000)
        }
    }"
    x-init="autoplay()"
>
    @if($heroStyle === 'modern-grid')
        <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient({{ $heroPrimary }}22 1px, transparent 1px), linear-gradient(90deg, {{ $heroPrimary }}22 1px, transparent 1px); background-size: 34px 34px;"></div>
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at top left, rgba(255,255,255,0.78), transparent 38%);"></div>
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at bottom right, {{ $heroSecondary }}22, transparent 34%);"></div>
    @elseif($heroStyle === 'soft-gradient')
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at top left, {{ $heroPrimarySoft }}, transparent 42%), radial-gradient(circle at bottom right, {{ $heroSecondarySoft }}, transparent 36%); opacity: 0.9;"></div>
    @else
        <div class="pointer-events-none absolute inset-x-0 top-0 h-24" style="background: linear-gradient(180deg, {{ $heroPrimarySoft }}, transparent); opacity: 0.65;"></div>
    @endif

    <div class="relative mx-auto max-w-7xl px-6 pb-16 pt-14 lg:px-8 lg:pb-20 lg:pt-16">
        <div class="relative min-h-[42rem] lg:min-h-[40rem]" @mouseenter="if (timer) clearInterval(timer)" @mouseleave="autoplay()">
            @foreach ($slides as $index => $slide)
                <div x-cloak x-show="active === {{ $index }}" class="absolute inset-0 grid items-center gap-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-12">
                    <div
                        x-show="active === {{ $index }}"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-y-6"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-6"
                        class="max-w-3xl"
                    >
                        @if(filled($slide->badge_text))
                            <p class="inline-flex items-center rounded-full border px-4 py-1.5 text-sm font-semibold tracking-tight shadow-sm backdrop-blur" style="border-color: {{ $heroSecondary }}; background-color: {{ $heroSurface }}CC; color: {{ $heroPrimary }};">
                                {{ $slide->badge_text }}
                            </p>
                        @endif

                        <h1 class="mt-7 max-w-4xl font-[Georgia,serif] text-5xl font-semibold leading-[0.94] tracking-[-0.04em] sm:text-6xl lg:text-[4.8rem]" style="color: {{ $heroHeading }};">
                            {{ $slide->title }}
                        </h1>

                        <p class="mt-7 max-w-2xl text-lg leading-relaxed sm:text-[1.35rem]" style="color: {{ $heroBody }};">
                            {{ $slide->subtitle }}
                        </p>

                        <div class="mt-8 flex flex-wrap gap-4">
                            @if(filled($slide->button_1_text) && filled($slide->button_1_link))
                                <a href="{{ $resolveLink($slide->button_1_link) }}" class="inline-flex items-center rounded-full px-7 py-3.5 text-sm font-bold shadow-[0_18px_35px_-16px_rgba(15,23,42,0.35)] transition duration-200 hover:-translate-y-0.5" style="background-color: {{ $heroPrimary }}; color: {{ $heroTextOnPrimary }};" onmouseover="this.style.backgroundColor='{{ $heroSecondary }}'; this.style.color='{{ $heroTextOnSecondary }}'" onmouseout="this.style.backgroundColor='{{ $heroPrimary }}'; this.style.color='{{ $heroTextOnPrimary }}'">
                                    {{ $slide->button_1_text }}
                                </a>
                            @endif

                            @if(filled($slide->button_2_text) && filled($slide->button_2_link))
                                <a href="{{ $resolveLink($slide->button_2_link) }}" class="inline-flex items-center rounded-full border px-7 py-3.5 text-sm font-bold shadow-sm backdrop-blur transition duration-200 hover:-translate-y-0.5" style="border-color: {{ $heroPrimary }}55; background-color: {{ $heroSurface }}D9; color: {{ $heroPrimary }};" onmouseover="this.style.borderColor='{{ $heroSecondary }}'; this.style.backgroundColor='{{ $heroSecondary }}'; this.style.color='{{ $heroTextOnSecondary }}'" onmouseout="this.style.borderColor='{{ $heroPrimary }}55'; this.style.backgroundColor='{{ $heroSurface }}D9'; this.style.color='{{ $heroPrimary }}'">
                                    {{ $slide->button_2_text }}
                                </a>
                            @endif
                        </div>

                        @if($metrics->isNotEmpty())
                            <div class="mt-9 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                @foreach($metrics as $metric)
                                    <div class="rounded-[1.6rem] border p-4 shadow-[0_18px_40px_-24px_rgba(15,23,42,0.35)] backdrop-blur" style="border-color: {{ $heroPrimary }}22; background-color: {{ $heroSurface }}E6;">
                                        <p class="text-4xl font-black tracking-[-0.04em]" style="color: {{ $heroHeading }};">{{ data_get($metric, 'value') }}</p>
                                        <p class="mt-1 text-sm font-medium" style="color: {{ $heroBody }};">{{ data_get($metric, 'label') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="relative lg:pl-6">
                        <div class="relative overflow-hidden rounded-[2rem] border shadow-[0_30px_80px_-35px_rgba(15,23,42,0.6)]" style="border-color: {{ $heroPrimary }}18; background-color: {{ $heroSurface }};">
                            <div class="relative aspect-[4/4.15] min-h-[24rem] overflow-hidden sm:min-h-[30rem]">
                                <img src="{{ asset('storage/' . ltrim($slide->image_path, '/')) }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/30 via-slate-900/5 to-transparent"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
                            </div>
                        </div>

                        <div
                            x-show="active === {{ $index }}"
                            x-transition:enter="transition ease-out duration-700 delay-300"
                            x-transition:enter-start="opacity-0 translate-x-8"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 translate-x-8"
                            class="absolute -bottom-6 left-4 max-w-[18rem] rounded-[1.5rem] border p-4 shadow-[0_24px_55px_-24px_rgba(15,23,42,0.4)] backdrop-blur sm:left-[-1.25rem] sm:p-5" style="border-color: {{ $heroPrimary }}22; background-color: {{ $heroSurface }}F2;"
                        >
                            @if(filled($slide->school_name))
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" style="color: {{ $heroPrimary }}99;">{{ $slide->school_name }}</p>
                            @endif
                            <h2 class="mt-2 text-lg font-bold leading-tight" style="color: {{ $heroHeading }};">{{ $slide->right_card_title }}</h2>
                            <p class="mt-2 text-sm leading-relaxed" style="color: {{ $heroBody }};">{{ $slide->right_card_text }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($slides->count() > 1)
            <div class="mt-8 flex justify-center gap-2 lg:justify-end">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        class="h-2.5 w-2.5 rounded-full transition duration-200"
                        :style="active === {{ $index }} ? 'background-color: {{ $heroPrimary }}; transform: scale(1.1);' : 'background-color: {{ $heroSecondarySoft }};'"
                        @click="active = {{ $index }}; autoplay()"
                        aria-label="Go to slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif
