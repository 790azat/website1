{{--
    Every article card in the current language, newest first, with the text
    search matches against. The All Articles page fetches this on the static
    export, where the server can't run the search.
--}}
@php
    $data = app(\App\Content\SiteContent::class)->localized();
    $articles = collect($data['articles'])->sortByDesc('date')->values();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="robots" content="noindex, nofollow" />
        <title>{{ __('All Articles') }}</title>
    </head>
    <body>
        @foreach ($articles as $article)
            <div data-search-section="{{ $article['section'] }}" data-search-text="{{ \App\Content\SiteContent::searchText($article) }}">
                @include('partials.article-card')
            </div>
        @endforeach
    </body>
</html>
