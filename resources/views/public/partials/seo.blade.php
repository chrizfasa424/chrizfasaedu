@php
    $seoTitle = trim((string) ($title ?? ''));
    $seoDescriptionRaw = (string) ($description ?? '');
    $seoDescription = trim((string) preg_replace('/\s+/u', ' ', strip_tags($seoDescriptionRaw)));
    $seoDescription = $seoDescription !== '' ? \Illuminate\Support\Str::limit($seoDescription, 160, '') : '';
    $seoCanonical = trim((string) ($canonical ?? url()->current()));
    $seoType = trim((string) ($type ?? 'website')) ?: 'website';
    $seoRobots = trim((string) ($robots ?? 'index,follow')) ?: 'index,follow';
    $seoLocale = str_replace('-', '_', app()->getLocale() ?: 'en');
    $seoSiteName = trim((string) ($siteName ?? config('app.name', 'School')));
    $seoImage = trim((string) ($image ?? ''));
    if ($seoImage !== '' && !\Illuminate\Support\Str::startsWith($seoImage, ['http://', 'https://'])) {
        $seoImage = asset('storage/' . ltrim($seoImage, '/'));
    }
    $schemaType = trim((string) ($schemaType ?? 'WebPage')) ?: 'WebPage';
    $schemaEntries = [];

    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => trim((string) (($school?->name ?? null) ?: $seoSiteName)),
        'url' => url('/'),
    ];
    if ($seoImage !== '') {
        $organizationSchema['logo'] = $seoImage;
    }
    if (!empty($school?->phone)) {
        $organizationSchema['telephone'] = (string) $school->phone;
    }
    if (!empty($school?->email)) {
        $organizationSchema['email'] = (string) $school->email;
    }
    if (!empty($school?->address)) {
        $organizationSchema['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => (string) $school->address,
            'addressLocality' => (string) ($school->city ?? ''),
            'addressRegion' => (string) ($school->state ?? ''),
            'addressCountry' => (string) ($school->country ?? 'NG'),
        ];
    }
    $schemaEntries[] = $organizationSchema;

    $webSchema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => $seoTitle !== '' ? $seoTitle : $seoSiteName,
        'url' => $seoCanonical !== '' ? $seoCanonical : url()->current(),
        'inLanguage' => str_replace('_', '-', $seoLocale),
    ];
    if ($seoDescription !== '') {
        $webSchema['description'] = $seoDescription;
    }
    if ($seoImage !== '') {
        $webSchema['image'] = $seoImage;
    }
    $schemaEntries[] = $webSchema;

    $extraSchema = $schema ?? null;
    if (is_array($extraSchema)) {
        if (array_is_list($extraSchema)) {
            foreach ($extraSchema as $entry) {
                if (is_array($entry) && !empty($entry)) {
                    $schemaEntries[] = $entry;
                }
            }
        } elseif (!empty($extraSchema)) {
            $schemaEntries[] = $extraSchema;
        }
    }
@endphp
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle !== '' ? $seoTitle : $seoSiteName }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:locale" content="{{ $seoLocale }}">
@if($seoImage !== '')
<meta property="og:image" content="{{ $seoImage }}">
@endif
<meta name="twitter:card" content="{{ $seoImage !== '' ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seoTitle !== '' ? $seoTitle : $seoSiteName }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if($seoImage !== '')
<meta name="twitter:image" content="{{ $seoImage }}">
@endif
@foreach($schemaEntries as $entry)
<script type="application/ld+json">{!! json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
