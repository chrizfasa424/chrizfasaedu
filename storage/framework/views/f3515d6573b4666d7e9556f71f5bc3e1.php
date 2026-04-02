<?php
    $theme = \App\Support\ThemePalette::fromPublicPage($publicPage);
    $schoolName = $school?->name ?? 'ChrizFasa Academy';
    $footerLogoPath = $publicPage['footer_logo'] ?? null;
    $fallbackLogoPath = $school?->logo ?? null;
    $footerDescription = trim((string) ($publicPage['footer_description'] ?? ''));
    $footerDescription = $footerDescription !== '' ? $footerDescription : ($publicPage['hero_subtitle'] ?? '');
    $footerNote = trim((string) ($publicPage['footer_note'] ?? 'All rights reserved.'));
    $footerBgColor = $theme['footer'];
    $footerSeparatorColor = $theme['divider'];
    $footerHoverColor = $theme['secondary']['300'];
    $footerMutedColor = $theme['secondary']['100'];
    $footerContactAddress = trim((string) ($publicPage['footer_contact_address'] ?? ($school?->address ?? '')));
    $footerContactPhone = trim((string) ($publicPage['footer_contact_phone'] ?? ($school?->phone ?? '')));
    $footerContactEmail = trim((string) ($publicPage['footer_contact_email'] ?? ($school?->email ?? '')));
    $footerQuickLinksTitle = trim((string) ($publicPage['footer_quick_links_title'] ?? 'Quick Links'));
    $footerResourcesTitle = trim((string) ($publicPage['footer_resources_title'] ?? 'Resources'));
    $footerContactTitle = trim((string) ($publicPage['footer_contact_title'] ?? 'Contact'));
    $footerQuickLinks = collect($publicPage['footer_quick_links'] ?? [])->filter(fn ($item) => !empty($item['title']))->values();
    $footerResources = collect($publicPage['footer_resources'] ?? [])->filter(fn ($item) => !empty($item['title']))->values();
    $footerSocialLinks = collect($publicPage['footer_social_links'] ?? [])->filter(fn ($item) => !empty($item['title']))->values();
?>

<footer class="border-t border-slate-200 text-slate-200" style="background-color: <?php echo e($footerBgColor); ?>; color: <?php echo e($footerMutedColor); ?>;">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <a href="<?php echo e(route('public.home')); ?>" class="inline-flex items-center gap-3">
                    <?php if($footerLogoPath || $fallbackLogoPath): ?>
                        <img src="<?php echo e(asset('storage/' . ltrim($footerLogoPath ?: $fallbackLogoPath, '/'))); ?>" alt="<?php echo e($schoolName); ?> Footer Logo" class="h-12 w-12 rounded-xl border border-white/10 bg-white/5 object-cover">
                    <?php endif; ?>
                    <span class="font-display text-lg font-semibold text-white"><?php echo e($schoolName); ?></span>
                </a>
                <?php if($footerDescription !== ''): ?>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300"><?php echo e($footerDescription); ?></p>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: <?php echo e($footerMutedColor); ?>;"><?php echo e($footerQuickLinksTitle !== '' ? $footerQuickLinksTitle : 'Quick Links'); ?></h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <?php $__currentLoopData = $footerQuickLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $url = trim((string) ($link['description'] ?? ''));
                            $isExternal = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                        ?>
                        <li>
                            <?php if($url !== ''): ?>
                                <a href="<?php echo e($url); ?>" <?php if($isExternal): ?> target="_blank" rel="noopener" <?php endif; ?> class="transition" style="--footer-hover: <?php echo e($footerHoverColor); ?>;" onmouseover="this.style.color='<?php echo e($footerHoverColor); ?>'" onmouseout="this.style.color=''"><?php echo e($link['title']); ?></a>
                            <?php else: ?>
                                <span><?php echo e($link['title']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: <?php echo e($footerMutedColor); ?>;"><?php echo e($footerResourcesTitle !== '' ? $footerResourcesTitle : 'Resources'); ?></h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <?php $__currentLoopData = $footerResources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $url = trim((string) ($resource['description'] ?? ''));
                            $isExternal = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                        ?>
                        <li>
                            <?php if($url !== ''): ?>
                                <a href="<?php echo e($url); ?>" <?php if($isExternal): ?> target="_blank" rel="noopener" <?php endif; ?> class="transition" style="--footer-hover: <?php echo e($footerHoverColor); ?>;" onmouseover="this.style.color='<?php echo e($footerHoverColor); ?>'" onmouseout="this.style.color=''"><?php echo e($resource['title']); ?></a>
                            <?php else: ?>
                                <span><?php echo e($resource['title']); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: <?php echo e($footerMutedColor); ?>;"><?php echo e($footerContactTitle !== '' ? $footerContactTitle : 'Contact'); ?></h3>
                <div class="mt-4 space-y-2 text-sm" style="color: <?php echo e($footerMutedColor); ?>;">
                    <?php if($footerContactAddress !== ''): ?>
                        <p><?php echo e($footerContactAddress); ?></p>
                    <?php endif; ?>
                    <?php if($footerContactPhone !== ''): ?>
                        <p><?php echo e($footerContactPhone); ?></p>
                    <?php endif; ?>
                    <?php if($footerContactEmail !== ''): ?>
                        <p><?php echo e($footerContactEmail); ?></p>
                    <?php endif; ?>
                </div>

                <?php if($footerSocialLinks->isNotEmpty()): ?>
                    <div class="mt-5 flex flex-wrap gap-3 text-sm">
                        <?php $__currentLoopData = $footerSocialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $url = trim((string) ($social['description'] ?? '')); ?>
                            <?php if($url !== ''): ?>
                                <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="inline-flex items-center rounded-full border px-3 py-1.5 transition" style="border-color: rgba(255,255,255,0.2); color: #FFFFFF;" onmouseover="this.style.borderColor='<?php echo e($footerHoverColor); ?>'; this.style.color='<?php echo e($footerHoverColor); ?>'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='#FFFFFF'"><?php echo e($social['title']); ?></a>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-full border px-3 py-1.5" style="border-color: rgba(255,255,255,0.1); color: <?php echo e($footerMutedColor); ?>;"><?php echo e($social['title']); ?></span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-10 border-t pt-6 text-xs" style="border-top-color: <?php echo e($footerSeparatorColor); ?>; color: <?php echo e($footerMutedColor); ?>;">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($schoolName); ?>. <?php echo e($footerNote); ?></p>
        </div>
    </div>
</footer>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views\public\partials\footer.blade.php ENDPATH**/ ?>