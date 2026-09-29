<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' — '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

{{-- Article index for the header live search and the static export's search page. --}}
<link rel="x-search-index" href="{{ route('articles.search-index') }}" />

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
