@php
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
@endphp

<footer class="border-t border-slate-200 text-slate-200" style="background-color: {{ $footerBgColor }}; color: {{ $footerMutedColor }};">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <a href="{{ route('public.home') }}" class="inline-flex items-center gap-3">
                    @if($footerLogoPath || $fallbackLogoPath)
                        <img src="{{ asset('storage/' . ltrim($footerLogoPath ?: $fallbackLogoPath, '/')) }}" alt="{{ $schoolName }} Footer Logo" class="h-12 w-12 rounded-xl border border-white/10 bg-white/5 object-cover">
                    @endif
                    <span class="font-display text-lg font-semibold text-white">{{ $schoolName }}</span>
                </a>
                @if($footerDescription !== '')
                    <p class="mt-4 text-sm leading-relaxed text-slate-300">{{ $footerDescription }}</p>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: {{ $footerMutedColor }};">{{ $footerQuickLinksTitle !== '' ? $footerQuickLinksTitle : 'Quick Links' }}</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach($footerQuickLinks as $link)
                        @php
                            $url = trim((string) ($link['description'] ?? ''));
                            $isExternal = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                        @endphp
                        <li>
                            @if($url !== '')
                                <a href="{{ $url }}" @if($isExternal) target="_blank" rel="noopener" @endif class="transition" style="--footer-hover: {{ $footerHoverColor }};" onmouseover="this.style.color='{{ $footerHoverColor }}'" onmouseout="this.style.color=''">{{ $link['title'] }}</a>
                            @else
                                <span>{{ $link['title'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: {{ $footerMutedColor }};">{{ $footerResourcesTitle !== '' ? $footerResourcesTitle : 'Resources' }}</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach($footerResources as $resource)
                        @php
                            $url = trim((string) ($resource['description'] ?? ''));
                            $isExternal = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                        @endphp
                        <li>
                            @if($url !== '')
                                <a href="{{ $url }}" @if($isExternal) target="_blank" rel="noopener" @endif class="transition" style="--footer-hover: {{ $footerHoverColor }};" onmouseover="this.style.color='{{ $footerHoverColor }}'" onmouseout="this.style.color=''">{{ $resource['title'] }}</a>
                            @else
                                <span>{{ $resource['title'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em]" style="color: {{ $footerMutedColor }};">{{ $footerContactTitle !== '' ? $footerContactTitle : 'Contact' }}</h3>
                <div class="mt-4 space-y-2 text-sm" style="color: {{ $footerMutedColor }};">
                    @if($footerContactAddress !== '')
                        <p>{{ $footerContactAddress }}</p>
                    @endif
                    @if($footerContactPhone !== '')
                        <p>{{ $footerContactPhone }}</p>
                    @endif
                    @if($footerContactEmail !== '')
                        <p>{{ $footerContactEmail }}</p>
                    @endif
                </div>

                @if($footerSocialLinks->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-3 text-sm">
                        @foreach($footerSocialLinks as $social)
                            @php $url = trim((string) ($social['description'] ?? '')); @endphp
                            @if($url !== '')
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full border px-3 py-1.5 transition" style="border-color: rgba(255,255,255,0.2); color: #FFFFFF;" onmouseover="this.style.borderColor='{{ $footerHoverColor }}'; this.style.color='{{ $footerHoverColor }}'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='#FFFFFF'">{{ $social['title'] }}</a>
                            @else
                                <span class="inline-flex items-center rounded-full border px-3 py-1.5" style="border-color: rgba(255,255,255,0.1); color: {{ $footerMutedColor }};">{{ $social['title'] }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-10 border-t pt-6 text-xs" style="border-top-color: {{ $footerSeparatorColor }}; color: {{ $footerMutedColor }};">
            <p>&copy; {{ date('Y') }} {{ $schoolName }}. {{ $footerNote }}</p>
        </div>
    </div>
</footer>
