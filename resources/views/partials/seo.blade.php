{{--
    Search and social meta tags for public pages. Include inside <head>
    after partials.head.
    Uses the page's $title (without the site name) and a $seo array:
      description  required
      image        path under public/images, for the share preview
      type         'website' (default) or 'article'
      canonical    the English URL; defaults to the current URL without its
                   query string. Translated pages add "lang=es" or
                   "lang=fr" to it.
      alternates   which translations the page has: true (the default)
                   for every language in SiteContent::TRANSLATION_LOCALES,
                   false for none (hreflang links are then left out), or a
                   list of locales
      noindex      true to keep the page out of search results
      schema       list of JSON-LD objects
--}}
@php
    $siteName = config('app.name', 'Laravel');
    $seoTitle = filled($title ?? null) ? $title : $siteName;
    $seoEnglishUrl = $seo['canonical'] ?? url()->current();
    $seoTranslations = ($seo['noindex'] ?? false) ? false : ($seo['alternates'] ?? true);
    $seoTranslations = match ($seoTranslations) {
        true => \App\Content\SiteContent::TRANSLATION_LOCALES,
        false => [],
        default => $seoTranslations,
    };
    $seoVersions = ['en' => $seoEnglishUrl];

    foreach ($seoTranslations as $seoTranslation) {
        $seoVersions[$seoTranslation] = $seoEnglishUrl.(str_contains($seoEnglishUrl, '?') ? '&' : '?').'lang='.$seoTranslation;
    }

    $seoCanonical = $seoVersions[app()->getLocale()] ?? $seoEnglishUrl;
    $seoImage = asset('images/'.($seo['image'] ?? 'branding/logo-full.webp'));
    $seoLocale = ['es' => 'es_ES', 'fr' => 'fr_FR'][app()->getLocale()] ?? 'en_US';

    // Built here rather than in the markup below, where Blade would read
    // "@context" as one of its own directives.
    $seoJsonLd = array_map(
        fn (array $ld) => json_encode(['@context' => 'https://schema.org'] + $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
        $seo['schema'] ?? [],
    );
@endphp
<meta name="description" content="{{ $seo['description'] }}" />
@if ($seo['noindex'] ?? false)
    <meta name="robots" content="noindex, follow" />
@endif
<link rel="canonical" href="{{ $seoCanonical }}" />
@if (count($seoVersions) > 1)
    @foreach ($seoVersions as $seoHreflang => $seoUrl)
        <link rel="alternate" hreflang="{{ $seoHreflang }}" href="{{ $seoUrl }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $seoEnglishUrl }}" />
@endif

<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seo['description'] }}" />
<meta property="og:url" content="{{ $seoCanonical }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:locale" content="{{ $seoLocale }}" />
<meta name="twitter:card" content="{{ isset($seo['image']) ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seo['description'] }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />

@foreach ($seoJsonLd as $json)
    <script type="application/ld+json">{!! $json !!}</script>
@endforeach
