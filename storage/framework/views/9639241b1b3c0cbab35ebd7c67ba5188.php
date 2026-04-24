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
    $footerSecondaryLineColor = $theme['secondary']['500'] ?? '#DFE753';
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
    $cookieBannerTitle = trim((string) ($publicPage['cookie_banner_title'] ?? 'Cookie Notice')) ?: 'Cookie Notice';
    $cookieBannerText = trim((string) ($publicPage['cookie_banner_text'] ?? 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.')) ?: 'We use necessary cookies to keep this website secure and functional. You can accept or reject optional cookies. Rejecting optional cookies will not block access to the website.';
    $cookieBannerAcceptText = trim((string) ($publicPage['cookie_banner_accept_text'] ?? 'Accept Cookies')) ?: 'Accept Cookies';
    $cookieBannerRejectText = trim((string) ($publicPage['cookie_banner_reject_text'] ?? 'Reject Optional')) ?: 'Reject Optional';
    $legalLinks = [
        ['label' => 'Privacy Policy', 'url' => route('public.privacy')],
        ['label' => 'Cookies Policy', 'url' => route('public.cookies')],
    ];

    $socialIcon = static function (string $title): string {
        return match (strtolower(trim($title))) {
            'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.6 1.6-1.6H16V4.8c-.3 0-.9-.1-1.8-.1-2.8 0-4.7 1.7-4.7 4.8V11H7v3h2.5v7h4z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"></rect><circle cx="12" cy="12" r="4.25"></circle><circle cx="17.3" cy="6.7" r="1"></circle></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.6 7.2a2.9 2.9 0 0 0-2-2C17.8 4.7 12 4.7 12 4.7s-5.8 0-7.6.5a2.9 2.9 0 0 0-2 2A30 30 0 0 0 2 12a30 30 0 0 0 .4 4.8 2.9 2.9 0 0 0 2 2c1.8.5 7.6.5 7.6.5s5.8 0 7.6-.5a2.9 2.9 0 0 0 2-2A30 30 0 0 0 22 12a30 30 0 0 0-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg>',
            'x', 'twitter', 'twitter/x' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 3H21l-6.8 7.8L22 21h-6.1l-4.8-6.2L5.7 21H3.6l7.2-8.3L2 3h6.2l4.3 5.7L18.9 3Zm-1.1 16h1.2L7.5 4.9H6.2L17.8 19Z"/></svg>',
            'linkedin', 'linkedin-in' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3A1.96 1.96 0 1 0 5.3 6.9 1.96 1.96 0 0 0 5.25 3ZM20.44 12.9c0-3.47-1.85-5.08-4.32-5.08a3.73 3.73 0 0 0-3.37 1.86V8.5H9.38c.04.79 0 11.5 0 11.5h3.37v-6.42c0-.34.02-.68.13-.92.27-.68.89-1.39 1.93-1.39 1.36 0 1.9 1.05 1.9 2.58V20h3.37v-7.1Z"/></svg>',
            'tiktok' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.7 3c.3 1.7 1.3 3.1 2.8 4 .8.5 1.7.8 2.5.9v3.2a8.1 8.1 0 0 1-4.9-1.7v6.4a5.8 5.8 0 1 1-5.1-5.8v3.2a2.6 2.6 0 1 0 1.9 2.5V3h2.8Z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 11.8A8 8 0 0 0 6.3 6.1a7.8 7.8 0 0 0-2.2 5.5c0 1.4.4 2.8 1.1 4L4 20l4.5-1.2a8 8 0 0 0 3.5.8h.1A8 8 0 0 0 20 11.8Zm-8 6.5a6.7 6.7 0 0 1-3.4-.9l-.2-.1-2.7.7.7-2.6-.2-.2a6.6 6.6 0 1 1 5.8 3.1Zm3.7-5c-.2-.1-1.1-.6-1.3-.7-.2-.1-.3-.1-.4.1l-.6.7c-.1.1-.2.2-.4.1-.2-.1-.8-.3-1.5-1-.6-.5-1-1.2-1.1-1.4-.1-.2 0-.3.1-.4l.3-.4.2-.3c.1-.1.1-.2 0-.4l-.6-1.5c-.1-.2-.2-.2-.4-.2h-.3a.6.6 0 0 0-.4.2 1.8 1.8 0 0 0-.6 1.4c0 .8.6 1.7.7 1.8.1.1 1.2 1.9 2.9 2.6 1.7.8 1.7.5 2 .5.3 0 1.1-.4 1.2-.8.2-.4.2-.8.2-.9 0-.1-.1-.2-.3-.2Z"/></svg>',
            default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 13.5 21 3m0 0h-6m6 0v6"></path><path stroke-linecap="round" stroke-linejoin="round" d="M20 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h4"></path></svg>',
        };
    };
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
                    <div class="footer-rich-text mt-4 text-sm leading-relaxed text-slate-300"><?php echo \App\Support\RichText::render($footerDescription); ?></div>
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
                    <div class="mt-5 flex flex-wrap gap-3">
                        <?php $__currentLoopData = $footerSocialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $url = trim((string) ($social['description'] ?? ''));
                                $label = trim((string) ($social['title'] ?? 'Social link'));
                            ?>
                            <?php if($url !== ''): ?>
                                <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($label); ?>" title="<?php echo e($label); ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-full border transition" style="border-color: rgba(255,255,255,0.2); color: #FFFFFF;" onmouseover="this.style.borderColor='<?php echo e($footerHoverColor); ?>'; this.style.color='<?php echo e($footerHoverColor); ?>'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='#FFFFFF'">
                                    <span class="h-5 w-5"><?php echo $socialIcon($label); ?></span>
                                    <span class="sr-only"><?php echo e($label); ?></span>
                                </a>
                            <?php else: ?>
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border" style="border-color: rgba(255,255,255,0.1); color: <?php echo e($footerMutedColor); ?>;" title="<?php echo e($label); ?>">
                                    <span class="h-5 w-5"><?php echo $socialIcon($label); ?></span>
                                    <span class="sr-only"><?php echo e($label); ?></span>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-10 h-px w-full" style="background-color: <?php echo e($footerSecondaryLineColor); ?>;"></div>

        <div class="pt-6 text-xs" style="color: <?php echo e($footerMutedColor); ?>;">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($schoolName); ?>. <?php echo e($footerNote); ?></p>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1">
                <?php $__currentLoopData = $legalLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legalLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($legalLink['url']); ?>" class="transition hover:text-white"><?php echo e($legalLink['label']); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <style>
        .footer-rich-text p + p,
        .footer-rich-text ul + p,
        .footer-rich-text ol + p,
        .footer-rich-text p + ul,
        .footer-rich-text p + ol,
        .footer-rich-text figure,
        .footer-rich-text blockquote {
            margin-top: 0.75rem;
        }

        .footer-rich-text ul,
        .footer-rich-text ol {
            margin-left: 1.25rem;
            list-style-position: outside;
        }

        .footer-rich-text ul {
            list-style-type: disc;
        }

        .footer-rich-text ol {
            list-style-type: decimal;
        }

        .footer-rich-text blockquote {
            border-left: 3px solid rgba(223, 231, 83, 0.75);
            padding-left: 0.9rem;
            margin-top: 0.75rem;
            font-style: italic;
        }

        .footer-rich-text a {
            color: #ffffff;
            text-decoration: underline;
            text-underline-offset: 0.2em;
        }

        .footer-rich-text img {
            display: block;
            max-width: 100%;
            border-radius: 0.85rem;
        }

        .footer-rich-text figcaption {
            margin-top: 0.6rem;
            color: <?php echo e($footerMutedColor); ?>;
            font-size: 0.875rem;
        }
    </style>
</footer>

<div id="cookie-consent-banner" class="pointer-events-none fixed inset-x-0 bottom-4 z-[90] hidden px-4 sm:px-6">
    <div class="pointer-events-auto mx-auto w-full max-w-5xl rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-2xl backdrop-blur sm:p-5"
         style="border-color: var(--submenu-primary, #2D1D5C);">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-slate-700"><?php echo e($cookieBannerTitle); ?></p>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    <?php echo e($cookieBannerText); ?>

                    See our <a href="<?php echo e(route('public.cookies')); ?>" class="font-semibold text-brand-700 underline underline-offset-2">Cookies Policy</a> for details.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 sm:justify-end">
                <button type="button" id="cookie-consent-reject" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                    <?php echo e($cookieBannerRejectText); ?>

                </button>
                <button type="button" id="cookie-consent-accept" class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold text-white transition hover:opacity-95"
                        style="background-color: var(--submenu-primary, #2D1D5C);">
                    <?php echo e($cookieBannerAcceptText); ?>

                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const banner = document.getElementById('cookie-consent-banner');
        const acceptButton = document.getElementById('cookie-consent-accept');
        const rejectButton = document.getElementById('cookie-consent-reject');
        const storageKey = 'site_cookie_consent_status';
        const cookieName = 'site_cookie_consent';

        if (!banner || !acceptButton || !rejectButton) {
            return;
        }

        const readCookie = (name) => {
            const escaped = name.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
            const match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'));
            return match ? decodeURIComponent(match[1]) : '';
        };

        const writeCookie = (name, value) => {
            document.cookie = `${name}=${encodeURIComponent(value)}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
        };

        const getStoredChoice = () => {
            let localChoice = '';
            try {
                localChoice = window.localStorage.getItem(storageKey) || '';
            } catch (error) {
                localChoice = '';
            }

            return localChoice || readCookie(cookieName);
        };

        const persistChoice = (choice) => {
            try {
                window.localStorage.setItem(storageKey, choice);
            } catch (error) {
                // Local storage can be blocked by browser privacy mode.
            }

            writeCookie(cookieName, choice);
            banner.classList.add('hidden');
            banner.classList.add('pointer-events-none');
            document.dispatchEvent(new CustomEvent('site:cookie-consent', { detail: { status: choice } }));
        };

        const existingChoice = getStoredChoice();

        if (existingChoice === 'accepted' || existingChoice === 'rejected') {
            banner.classList.add('hidden');
            banner.classList.add('pointer-events-none');
            return;
        }

        banner.classList.remove('hidden');
        banner.classList.remove('pointer-events-none');

        acceptButton.addEventListener('click', function () {
            persistChoice('accepted');
        });

        rejectButton.addEventListener('click', function () {
            persistChoice('rejected');
        });
    })();
</script>
<?php /**PATH C:\wamp64\www\chrizfasaedu\resources\views/public/partials/footer.blade.php ENDPATH**/ ?>