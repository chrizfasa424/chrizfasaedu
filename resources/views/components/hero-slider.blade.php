@props(['school' => null, 'publicPage' => []])

@php
    use App\Models\HeroSlide;
    use App\Support\ThemePalette;
    use Illuminate\Support\Str;

    $schoolLogoPath = trim((string) ($school?->logo ?? ''));
    $schoolLogoUrl = $schoolLogoPath !== '' ? asset('storage/' . ltrim($schoolLogoPath, '/')) : null;
    $schoolInitials = Str::upper(
        collect(preg_split('/\s+/', trim((string) ($school?->name ?? 'ChrizFasa Academy'))) ?: [])
            ->filter()
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('')
    ) ?: 'CA';

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
            ->map(function (HeroSlide $slide) {
                $imagePath = trim((string) $slide->image_path);

                return [
                    'title' => (string) $slide->title,
                    'subtitle' => (string) $slide->subtitle,
                    'badge_text' => (string) $slide->badge_text,
                    'button_1_text' => (string) $slide->button_1_text,
                    'button_1_link' => (string) $slide->button_1_link,
                    'button_2_text' => (string) $slide->button_2_text,
                    'button_2_link' => (string) $slide->button_2_link,
                    'right_card_title' => (string) $slide->right_card_title,
                    'right_card_text' => (string) $slide->right_card_text,
                    'school_name' => (string) $slide->school_name,
                    'image_url' => $imagePath !== '' ? asset('storage/' . ltrim($imagePath, '/')) : null,
                    'is_placeholder_visual' => $imagePath === '' || Str::startsWith($imagePath, 'schools/logos/'),
                ];
            })
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

    if ($slides->isEmpty()) {
        $legacyImages = collect(data_get($publicPage, 'hero_slides', []))
            ->map(function ($slide) {
                $path = trim((string) data_get($slide, 'path', ''));

                return [
                    'path' => $path,
                    'caption' => trim((string) data_get($slide, 'caption', '')),
                ];
            })
            ->filter(fn (array $slide) => $slide['path'] !== '')
            ->values();

        $heroTitle = trim((string) data_get($publicPage, 'hero_title', ''));
        $heroSubtitle = trim((string) data_get($publicPage, 'hero_subtitle', ''));
        $heroBadgeText = trim((string) data_get($publicPage, 'hero_badge_text', ''));
        $ctaPrimaryText = trim((string) data_get($publicPage, 'cta_primary_text', 'Start Admission'));
        $ctaSecondaryText = trim((string) data_get($publicPage, 'cta_secondary_text', 'Explore Programs'));
        $schoolName = $school?->name ?? 'ChrizFasa Academy';
        $heroPlaceholder = trim((string) data_get($publicPage, 'hero_slider_placeholder_text', 'Upload hero slider images from Admin Settings to personalize this section.'));
        $programsLabel = trim((string) data_get($publicPage, 'programs_label', 'Programs'));
        $academicsLabel = trim((string) data_get($publicPage, 'academics_label', 'Academic Excellence'));
        $studentLifeLabel = trim((string) data_get($publicPage, 'student_life_label', 'Student Life'));
        $aboutLabel = trim((string) data_get($publicPage, 'about_label', 'About Us'));

        $legacyImageUrlAt = static function (int $index) use ($legacyImages): ?string {
            if ($legacyImages->isEmpty()) {
                return null;
            }

            $image = $legacyImages->get($index % $legacyImages->count());

            return $image ? asset('storage/' . ltrim((string) $image['path'], '/')) : null;
        };

        $fallbackSlides = collect([
            [
                'title' => $heroTitle !== '' ? $heroTitle : 'A modern learning environment for KG, Primary, and Secondary students',
                'subtitle' => $heroSubtitle !== '' ? $heroSubtitle : $schoolName . ' combines academic excellence, character development, and modern technology to prepare learners for global opportunities.',
                'badge_text' => $heroBadgeText !== '' ? $heroBadgeText : 'Standard and Industry Professional School',
                'button_1_text' => $ctaPrimaryText,
                'button_1_link' => route('admission.apply'),
                'button_2_text' => $ctaSecondaryText,
                'button_2_link' => '#programs',
                'right_card_title' => 'KG to Secondary Pathway',
                'right_card_text' => 'A well-structured journey from early foundation to confident senior-secondary readiness.',
                'school_name' => $schoolName,
                'image_url' => $legacyImageUrlAt(0),
                'is_placeholder_visual' => !filled($legacyImageUrlAt(0)),
            ],
            [
                'title' => 'Strong Primary Learning That Builds Confidence Early',
                'subtitle' => 'We support literacy, numeracy, creativity, and discipline through a warm and structured primary-school experience.',
                'badge_text' => $programsLabel,
                'button_1_text' => 'View Programs',
                'button_1_link' => '#programs',
                'button_2_text' => 'Contact School',
                'button_2_link' => route('public.contact'),
                'right_card_title' => 'Primary Years Focus',
                'right_card_text' => 'Reading, numeracy, communication, and personal development delivered with care and consistency.',
                'school_name' => $schoolName,
                'image_url' => $legacyImageUrlAt(1),
                'is_placeholder_visual' => !filled($legacyImageUrlAt(1)),
            ],
            [
                'title' => 'Academic Excellence With Mentorship At The Center',
                'subtitle' => 'Our secondary programme combines subject mastery, accountability, and teacher-student mentorship for lasting outcomes.',
                'badge_text' => $academicsLabel,
                'button_1_text' => 'Explore Academics',
                'button_1_link' => '#academics',
                'button_2_text' => 'Meet the School',
                'button_2_link' => '#about',
                'right_card_title' => 'Secondary School Direction',
                'right_card_text' => 'Exam preparation, leadership development, and future-ready learning for confident progression.',
                'school_name' => $schoolName,
                'image_url' => $legacyImageUrlAt(2),
                'is_placeholder_visual' => !filled($legacyImageUrlAt(2)),
            ],
            [
                'title' => 'Character, Leadership, And Student Life Beyond The Classroom',
                'subtitle' => 'Students grow through clubs, guided routines, values education, and a balanced campus culture that prepares them for life.',
                'badge_text' => $studentLifeLabel !== '' ? $studentLifeLabel : $aboutLabel,
                'button_1_text' => 'Discover Student Life',
                'button_1_link' => '#student-life',
                'button_2_text' => 'About the School',
                'button_2_link' => '#about',
                'right_card_title' => 'Whole-Child Development',
                'right_card_text' => 'A supportive environment that develops discipline, creativity, service, and leadership.',
                'school_name' => $schoolName,
                'image_url' => $legacyImageUrlAt(3),
                'is_placeholder_visual' => !filled($legacyImageUrlAt(3)),
            ],
        ]);

        $slides = $fallbackSlides
            ->map(function (array $slide) use ($heroPlaceholder) {
                if (!filled($slide['image_url'])) {
                    $slide['right_card_text'] = filled($slide['right_card_text']) ? $slide['right_card_text'] : $heroPlaceholder;
                }

                return $slide;
            })
            ->values();
    }

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

@once
    <style>
        [x-cloak] {
            display: none !important;
        }

        .hero-fullbleed {
            position: relative;
            left: 50%;
            right: 50%;
            width: 100vw;
            transform: translateX(-50%);
        }

        .hero-fullbleed,
        .hero-fullbleed * {
            text-align: left;
            text-justify: auto;
            letter-spacing: 0;
        }

        .hero-glass-panel {
            border: 1px solid rgba(255, 255, 255, 0.28);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.08)),
                linear-gradient(180deg, rgba(15, 23, 42, 0.26), rgba(15, 23, 42, 0.14));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.34),
                0 32px 90px -42px rgba(2, 6, 23, 0.82);
            backdrop-filter: blur(16px) saturate(130%);
        }

        .hero-glass-panel::before,
        .hero-focus-glass::before,
        .hero-metric-glass::before,
        .hero-gloss-btn::before {
            content: "";
            pointer-events: none;
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.48), rgba(255, 255, 255, 0.08) 34%, transparent 58%);
            opacity: 0.74;
        }

        .hero-focus-glass,
        .hero-metric-glass {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: linear-gradient(150deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.09));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.32),
                0 24px 60px -34px rgba(2, 6, 23, 0.72);
            backdrop-filter: blur(14px) saturate(125%);
        }

        .hero-gloss-btn {
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .hero-gloss-btn::after {
            content: "";
            pointer-events: none;
            position: absolute;
            left: -45%;
            top: -115%;
            width: 190%;
            height: 62%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0));
            transform: rotate(8deg);
            transition: top 0.5s ease;
            z-index: 1;
        }

        .hero-gloss-btn > span,
        .hero-focus-glass > *,
        .hero-metric-glass > *,
        .hero-glass-panel > * {
            position: relative;
            z-index: 2;
        }

        .hero-gloss-btn:hover::after,
        .hero-gloss-btn:focus-visible::after {
            top: 120%;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.34);
            background: rgba(255, 255, 255, 0.14);
            padding: 0.45rem 0.9rem;
            font-family: "Space Grotesk", "Outfit", sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(7px);
        }

        .hero-badge::before {
            content: "";
            width: 0.48rem;
            height: 0.48rem;
            border-radius: 9999px;
            background: var(--hero-secondary, #DFE753);
            box-shadow: 0 0 0 4px rgba(223, 231, 83, 0.18);
        }

        .hero-title-type {
            font-family: "Fraunces", "Outfit", serif;
            font-variation-settings: "SOFT" 60, "WONK" 0;
            letter-spacing: -0.02em;
            line-height: 0.95;
        }

        .hero-copy-type {
            max-width: 46ch;
            font-family: "Space Grotesk", "Manrope", sans-serif;
            letter-spacing: 0.01em;
        }

        .hero-metric-item {
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(150deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.08));
            padding: 0.82rem 0.9rem;
            backdrop-filter: blur(8px);
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-gloss-btn::after {
                transition: none;
            }
        }
    </style>
@endonce

@if($slides->isNotEmpty())
<section
    class="hero-fullbleed relative isolate overflow-hidden bg-teal-950"
    style="--hero-primary: {{ $heroPrimary }}; --hero-secondary: {{ $heroSecondary }}; --hero-text-on-primary: {{ $heroTextOnPrimary }}; --hero-text-on-secondary: {{ $heroTextOnSecondary }}; --hero-surface: {{ $heroSurface }}; --hero-heading: {{ $heroHeading }}; --hero-body: {{ $heroBody }};"
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
    @mouseenter="if (timer) clearInterval(timer)"
    @mouseleave="autoplay()"
>
    <div class="relative min-h-[680px] w-full overflow-hidden sm:min-h-[720px] lg:min-h-[760px]">
        @foreach ($slides as $index => $slide)
            <template x-if="active === {{ $index }}">
                <div
                    x-cloak
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-[1.01]"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-[1.01]"
                    class="absolute inset-0"
                >
                    @if(filled(data_get($slide, 'image_url')) && !data_get($slide, 'is_placeholder_visual'))
                        <img src="{{ data_get($slide, 'image_url') }}" alt="{{ data_get($slide, 'title') }}" class="h-full w-full object-cover object-center">
                    @else
                        <div class="absolute inset-0 bg-[linear-gradient(135deg,#0f172a_0%,#0f766e_48%,#0ea5e9_100%)]"></div>
                        <div class="absolute inset-0 flex items-center justify-center px-6">
                            <div class="hero-glass-panel relative max-w-xl rounded-3xl p-6 text-white">
                                <p class="text-sm font-semibold uppercase text-white/80">Hero Slides</p>
                                <p class="mt-3 text-2xl font-bold leading-tight">{{ trim((string) data_get($publicPage, 'hero_slider_placeholder_text', 'Upload hero slider images from Admin Settings to personalize this section.')) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </template>
        @endforeach

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-slate-950/82 via-slate-950/42 to-slate-950/10"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/66 via-transparent to-slate-950/14"></div>
        <div class="pointer-events-none absolute inset-0 opacity-28" style="background-image: linear-gradient(rgba(255,255,255,0.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 46px 46px;"></div>

        <div class="relative z-10 mx-auto flex min-h-[680px] w-full max-w-7xl items-end px-4 py-14 sm:min-h-[720px] sm:px-6 sm:py-16 lg:min-h-[760px] lg:px-8 lg:py-20">
            @foreach ($slides as $index => $slide)
                <template x-if="active === {{ $index }}">
                    <div
                        x-cloak
                        x-transition:enter="transition ease-out duration-500 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="w-full max-w-5xl text-white"
                >
                        @if(filled(data_get($slide, 'badge_text')))
                            <p class="hero-badge">
                                {{ data_get($slide, 'badge_text') }}
                            </p>
                        @endif

                        <h1 class="hero-title-type mt-4 max-w-4xl text-4xl font-semibold text-white drop-shadow-[0_5px_22px_rgba(2,6,23,0.76)] sm:text-5xl lg:text-7xl">
                            {{ data_get($slide, 'title') }}
                        </h1>

                        <p class="hero-copy-type mt-5 text-base leading-relaxed text-white drop-shadow-[0_2px_12px_rgba(2,6,23,0.82)] sm:text-lg lg:text-xl">
                            {{ data_get($slide, 'subtitle') }}
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            @if(filled(data_get($slide, 'button_1_text')) && filled(data_get($slide, 'button_1_link')))
                                <a href="{{ $resolveLink(data_get($slide, 'button_1_link')) }}" class="hero-gloss-btn inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-bold shadow-[0_18px_35px_-16px_rgba(2,6,23,0.8)] transition duration-200 hover:-translate-y-0.5" style="background-color: {{ $heroSecondary }}; color: {{ $heroTextOnSecondary }};">
                                    <span>{{ data_get($slide, 'button_1_text') }}</span>
                                </a>
                            @endif

                            @if(filled(data_get($slide, 'button_2_text')) && filled(data_get($slide, 'button_2_link')))
                                <a href="{{ $resolveLink(data_get($slide, 'button_2_link')) }}" class="hero-gloss-btn inline-flex items-center justify-center rounded-full border border-white/35 bg-white/15 px-6 py-3 text-sm font-bold text-white shadow-sm backdrop-blur transition duration-200 hover:-translate-y-0.5">
                                    <span>{{ data_get($slide, 'button_2_text') }}</span>
                                </a>
                            @endif
                        </div>

                        @if($metrics->isNotEmpty())
                            <div class="mt-8 grid max-w-3xl gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                @foreach($metrics as $metric)
                                    <div class="hero-metric-item">
                                        <p class="text-3xl font-black leading-none text-white drop-shadow-[0_3px_14px_rgba(2,6,23,0.85)] sm:text-4xl">{{ data_get($metric, 'value') }}</p>
                                        <p class="mt-1 text-sm font-medium text-white/86 drop-shadow-[0_2px_10px_rgba(2,6,23,0.82)]">{{ data_get($metric, 'label') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </template>
            @endforeach
        </div>

        @if($slides->count() > 1)
            <div class="absolute bottom-5 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2 rounded-full border border-white/25 bg-white/12 px-3 py-2 shadow-lg backdrop-blur-xl">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        class="h-2.5 rounded-full transition-all duration-300"
                        :class="active === {{ $index }} ? 'w-8 bg-white' : 'w-2.5 bg-white/45 hover:bg-white/70'"
                        @click="active = {{ $index }}; autoplay()"
                        aria-label="Go to slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif
