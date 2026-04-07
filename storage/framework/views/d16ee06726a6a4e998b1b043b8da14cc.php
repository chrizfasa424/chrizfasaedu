<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['school' => null, 'publicPage' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['school' => null, 'publicPage' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
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
?>

<?php if($slides->isNotEmpty()): ?>
<section
    class="relative overflow-hidden border-b border-slate-200"
    style="background-color: <?php echo e($heroStyle === 'minimal-clean' ? $heroSurface : $heroSoftSurface); ?>;"
    x-data="{
        active: 0,
        slides: <?php echo e($slides->count()); ?>,
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
    <?php if($heroStyle === 'modern-grid'): ?>
        <div class="pointer-events-none absolute inset-0 opacity-60" style="background-image: linear-gradient(<?php echo e($heroPrimary); ?>22 1px, transparent 1px), linear-gradient(90deg, <?php echo e($heroPrimary); ?>22 1px, transparent 1px); background-size: 34px 34px;"></div>
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at top left, rgba(255,255,255,0.78), transparent 38%);"></div>
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at bottom right, <?php echo e($heroSecondary); ?>22, transparent 34%);"></div>
    <?php elseif($heroStyle === 'soft-gradient'): ?>
        <div class="pointer-events-none absolute inset-0" style="background: radial-gradient(circle at top left, <?php echo e($heroPrimarySoft); ?>, transparent 42%), radial-gradient(circle at bottom right, <?php echo e($heroSecondarySoft); ?>, transparent 36%); opacity: 0.9;"></div>
    <?php else: ?>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-24" style="background: linear-gradient(180deg, <?php echo e($heroPrimarySoft); ?>, transparent); opacity: 0.65;"></div>
    <?php endif; ?>

    <div class="relative mx-auto max-w-7xl px-6 pb-16 pt-14 lg:px-8 lg:pb-20 lg:pt-16">
        <div class="relative min-h-[42rem] lg:min-h-[40rem]" @mouseenter="if (timer) clearInterval(timer)" @mouseleave="autoplay()">
            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div x-cloak x-show="active === <?php echo e($index); ?>" class="absolute inset-0 grid items-center gap-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-12">
                    <div
                        x-show="active === <?php echo e($index); ?>"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-y-6"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-6"
                        class="max-w-3xl"
                    >
                        <?php if(filled(data_get($slide, 'badge_text'))): ?>
                            <p class="inline-flex items-center rounded-full border px-4 py-1.5 text-sm font-semibold tracking-tight shadow-sm backdrop-blur" style="border-color: <?php echo e($heroSecondary); ?>; background-color: <?php echo e($heroSurface); ?>CC; color: <?php echo e($heroPrimary); ?>;">
                                <?php echo e(data_get($slide, 'badge_text')); ?>

                            </p>
                        <?php endif; ?>

                        <h1 class="mt-7 max-w-4xl font-[Georgia,serif] text-5xl font-semibold leading-[0.94] tracking-[-0.04em] sm:text-6xl lg:text-[4.8rem]" style="color: <?php echo e($heroHeading); ?>;">
                            <?php echo e(data_get($slide, 'title')); ?>

                        </h1>

                        <p class="mt-7 max-w-2xl text-lg leading-relaxed sm:text-[1.35rem]" style="color: <?php echo e($heroBody); ?>;">
                            <?php echo e(data_get($slide, 'subtitle')); ?>

                        </p>

                        <div class="mt-8 flex flex-wrap gap-4">
                            <?php if(filled(data_get($slide, 'button_1_text')) && filled(data_get($slide, 'button_1_link'))): ?>
                                <a href="<?php echo e($resolveLink(data_get($slide, 'button_1_link'))); ?>" class="inline-flex items-center rounded-full px-7 py-3.5 text-sm font-bold shadow-[0_18px_35px_-16px_rgba(15,23,42,0.35)] transition duration-200 hover:-translate-y-0.5" style="background-color: <?php echo e($heroPrimary); ?>; color: <?php echo e($heroTextOnPrimary); ?>;" onmouseover="this.style.backgroundColor='<?php echo e($heroSecondary); ?>'; this.style.color='<?php echo e($heroTextOnSecondary); ?>'" onmouseout="this.style.backgroundColor='<?php echo e($heroPrimary); ?>'; this.style.color='<?php echo e($heroTextOnPrimary); ?>'">
                                    <?php echo e(data_get($slide, 'button_1_text')); ?>

                                </a>
                            <?php endif; ?>

                            <?php if(filled(data_get($slide, 'button_2_text')) && filled(data_get($slide, 'button_2_link'))): ?>
                                <a href="<?php echo e($resolveLink(data_get($slide, 'button_2_link'))); ?>" class="inline-flex items-center rounded-full border px-7 py-3.5 text-sm font-bold shadow-sm backdrop-blur transition duration-200 hover:-translate-y-0.5" style="border-color: <?php echo e($heroPrimary); ?>55; background-color: <?php echo e($heroSurface); ?>D9; color: <?php echo e($heroPrimary); ?>;" onmouseover="this.style.borderColor='<?php echo e($heroSecondary); ?>'; this.style.backgroundColor='<?php echo e($heroSecondary); ?>'; this.style.color='<?php echo e($heroTextOnSecondary); ?>'" onmouseout="this.style.borderColor='<?php echo e($heroPrimary); ?>55'; this.style.backgroundColor='<?php echo e($heroSurface); ?>D9'; this.style.color='<?php echo e($heroPrimary); ?>'">
                                    <?php echo e(data_get($slide, 'button_2_text')); ?>

                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if($metrics->isNotEmpty()): ?>
                            <div class="mt-9 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <?php $__currentLoopData = $metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-[1.6rem] border p-4 shadow-[0_18px_40px_-24px_rgba(15,23,42,0.35)] backdrop-blur" style="border-color: <?php echo e($heroPrimary); ?>22; background-color: <?php echo e($heroSurface); ?>E6;">
                                        <p class="text-4xl font-black tracking-[-0.04em]" style="color: <?php echo e($heroHeading); ?>;"><?php echo e(data_get($metric, 'value')); ?></p>
                                        <p class="mt-1 text-sm font-medium" style="color: <?php echo e($heroBody); ?>;"><?php echo e(data_get($metric, 'label')); ?></p>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="relative lg:pl-6">
                        <div class="relative overflow-hidden rounded-[2rem] border shadow-[0_30px_80px_-35px_rgba(15,23,42,0.6)]" style="border-color: <?php echo e($heroPrimary); ?>18; background-color: <?php echo e($heroSurface); ?>;">
                            <div class="relative aspect-[4/4.15] min-h-[24rem] overflow-hidden sm:min-h-[30rem]">
                                <?php if(filled(data_get($slide, 'image_url')) && !data_get($slide, 'is_placeholder_visual')): ?>
                                    <img src="<?php echo e(data_get($slide, 'image_url')); ?>" alt="<?php echo e(data_get($slide, 'title')); ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_28%),linear-gradient(135deg,#201244_0%,#2D1D5C_45%,#4D3A87_100%)]"></div>
                                    <div class="absolute -right-10 top-8 h-44 w-44 rounded-full border border-white/10 bg-white/5 blur-sm"></div>
                                    <div class="absolute -left-12 bottom-8 h-36 w-36 rounded-full border border-[#DFE753]/35 bg-[#DFE753]/12 blur-sm"></div>
                                    <div class="absolute inset-0 p-7 sm:p-8">
                                        <div class="flex h-full flex-col justify-between">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85">
                                                        Upload-ready hero visual
                                                    </p>
                                                    <h3 class="mt-4 max-w-sm text-2xl font-black leading-tight text-white sm:text-[2rem]">
                                                        <?php echo e(data_get($slide, 'right_card_title') ?: 'Premium school landing page banner'); ?>

                                                    </h3>
                                                </div>
                                                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border border-white/15 bg-white/10 shadow-xl">
                                                    <?php if($schoolLogoUrl): ?>
                                                        <img src="<?php echo e($schoolLogoUrl); ?>" alt="<?php echo e(data_get($slide, 'school_name') ?: ($school?->name ?? 'School')); ?>" class="h-full w-full object-cover">
                                                    <?php else: ?>
                                                        <span class="text-lg font-black tracking-[0.24em] text-[#DFE753]"><?php echo e($schoolInitials); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                                                <div class="rounded-[1.5rem] border border-white/12 bg-white/10 p-4 backdrop-blur">
                                                    <p class="text-sm leading-relaxed text-white/88">
                                                        <?php echo e(trim((string) data_get($publicPage, 'hero_slider_placeholder_text', 'Upload hero slider images from Admin Settings to personalize this section.'))); ?>

                                                    </p>
                                                </div>
                                                <div class="rounded-[1.5rem] border border-[#DFE753]/35 bg-[#DFE753]/12 px-4 py-3 text-sm font-semibold text-[#F7F4A0] backdrop-blur">
                                                    Replace with a wide campus, classroom, or student-life photo from Hero Slides
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/30 via-slate-900/5 to-transparent"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>
                            </div>
                        </div>

                        <div
                            x-show="active === <?php echo e($index); ?>"
                            x-transition:enter="transition ease-out duration-700 delay-300"
                            x-transition:enter-start="opacity-0 translate-x-8"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 translate-x-8"
                            class="absolute -bottom-6 left-4 max-w-[18rem] rounded-[1.5rem] border p-4 shadow-[0_24px_55px_-24px_rgba(15,23,42,0.4)] backdrop-blur sm:left-[-1.25rem] sm:p-5" style="border-color: <?php echo e($heroPrimary); ?>22; background-color: <?php echo e($heroSurface); ?>F2;"
                        >
                            <?php if(filled(data_get($slide, 'school_name'))): ?>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" style="color: <?php echo e($heroPrimary); ?>99;"><?php echo e(data_get($slide, 'school_name')); ?></p>
                            <?php endif; ?>
                            <h2 class="mt-2 text-lg font-bold leading-tight" style="color: <?php echo e($heroHeading); ?>;"><?php echo e(data_get($slide, 'right_card_title')); ?></h2>
                            <p class="mt-2 text-sm leading-relaxed" style="color: <?php echo e($heroBody); ?>;"><?php echo e(data_get($slide, 'right_card_text')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($slides->count() > 1): ?>
            <div class="mt-8 flex justify-center gap-2 lg:justify-end">
                <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        type="button"
                        class="h-2.5 w-2.5 rounded-full transition duration-200"
                        :style="active === <?php echo e($index); ?> ? 'background-color: <?php echo e($heroPrimary); ?>; transform: scale(1.1);' : 'background-color: <?php echo e($heroSecondarySoft); ?>;'"
                        @click="active = <?php echo e($index); ?>; autoplay()"
                        aria-label="Go to slide <?php echo e($index + 1); ?>"
                    ></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/components/hero-slider.blade.php ENDPATH**/ ?>