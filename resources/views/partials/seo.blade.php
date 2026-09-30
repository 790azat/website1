{{--
    Search and social meta tags for public pages. Include inside <head>
    after partials.head.
    Uses the page's $title (without the site name) and a $seo array:
      description  required
      image        path under public/images, for the share preview
      type         'website' (default) or 'article'
      canonical    URL of the page (any language); defaults to the current
                   page without its query string. Canonical and hreflang
                   URLs are built from its path for each language
                   ("/p/x", "/es/p/x", "/fr/p/x"); see App\Support\Seo.
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
    $seoLocale = app()->getLocale();
    $seoLanguages = match ($seo['alternates'] ?? true) {
        true => \App\Http\Middleware\SetLocale::SUPPORTED,
        false => ['en'],
        default => ['en', ...$seo['alternates']],
    };
    $seoPath = \App\Support\Seo::unlocalizePath(isset($seo['canonical'])
        ? (string) (parse_url($seo['canonical'], PHP_URL_PATH) ?: '/')
        : \App\Support\Seo::currentPath());
    // Keeps a canonical query string such as "?page=2" on every version.
    $seoQuery = isset($seo['canonical']) && filled($seoRawQuery = parse_url($seo['canonical'], PHP_URL_QUERY)) ? '?'.$seoRawQuery : '';
    $seoCanonical = \App\Support\Seo::url($seoPath, in_array($seoLocale, $seoLanguages, true) ? $seoLocale : 'en').$seoQuery;
    $seoAlternates = ($seo['noindex'] ?? false) ? [] : array_map(
        fn (string $url) => $url.$seoQuery,
        \App\Support\Seo::alternates($seoLanguages, $seoPath),
    );
    $seoImage = \App\Support\Seo::origin().'/images/'.($seo['image'] ?? 'branding/logo-full.webp');

    // Built here rather than in the markup below, where Blade would read
    // "@context" as one of its own directives.
    $seoJsonLd = array_map(
        fn (array $ld) => json_encode(['@context' => 'https://schema.org'] + $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
        $seo['schema'] ?? [],
    );
@endphp
<meta name="description" content="{{ $seo['description'] }}" />
<meta name="robots" content="{{ ($seo['noindex'] ?? false) ? 'noindex, follow' : 'index, follow, max-image-preview:large' }}" />
<link rel="canonical" href="{{ $seoCanonical }}" />
@foreach ($seoAlternates as $seoHreflang => $seoUrl)
    <link rel="alternate" hreflang="{{ $seoHreflang }}" href="{{ $seoUrl }}" />
@endforeach

<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seo['description'] }}" />
<meta property="og:url" content="{{ $seoCanonical }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:locale" content="{{ \App\Support\Seo::ogLocale($seoLocale) }}" />
@foreach (array_keys($seoAlternates) as $seoHreflang)
    @if ($seoHreflang !== 'x-default' && $seoHreflang !== $seoLocale)
        <meta property="og:locale:alternate" content="{{ \App\Support\Seo::ogLocale($seoHreflang) }}" />
    @endif
@endforeach
<meta name="twitter:card" content="{{ isset($seo['image']) ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seo['description'] }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />

@foreach ($seoJsonLd as $json)
    <script type="application/ld+json">{!! $json !!}</script>
@endforeach
