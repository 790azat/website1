@php
    $siteName = config('app.name', 'Laravel');
    $data = app(\App\Content\SiteContent::class)->localized();

    $categories = collect($data['sections'])
        ->map(fn ($meta, $key) => ['id' => $key, 'title' => $meta['title']])
        ->sortBy(fn ($c) => $data['sections'][$c['id']]['order'])
        ->values()
        ->all();

    $selectedSection = request()->query('section');
    if ($selectedSection && ! isset($data['sections'][$selectedSection])) {
        $selectedSection = null;
    }

    // Search box in the site header submits ?q=; every word must appear in
    // the article's title or body. The static export can't run this, so the
    // script at the end of the page repeats the search in the browser there.
    $searchQuery = trim(Str::limit(is_string(request()->query('q')) ? request()->query('q') : '', 100, ''));
    $searchWords = preg_split('/\s+/u', mb_strtolower($searchQuery), -1, PREG_SPLIT_NO_EMPTY);

    $allArticles = collect($data['articles'])
        ->when($selectedSection, fn ($collection) => $collection->where('section', $selectedSection))
        ->when($searchWords, fn ($collection) => $collection->filter(function ($article) use ($searchWords) {
            $haystack = \App\Content\SiteContent::searchText($article);

            foreach ($searchWords as $word) {
                if (! str_contains($haystack, $word)) {
                    return false;
                }
            }

            return true;
        }))
        ->sortByDesc('date')
        ->values();

    $perPage = 12;
    $totalArticles = $allArticles->count();
    $lastPage = max(1, (int) ceil($totalArticles / $perPage));
    $page = (int) request()->query('page', 1);
    $page = max(1, min($page, $lastPage));

    $pagedArticles = $allArticles->forPage($page, $perPage)->values();

    $pageLink = fn ($p) => route('articles', array_filter([
        'section' => $selectedSection,
        'q' => $searchQuery !== '' ? $searchQuery : null,
        'page' => $p > 1 ? $p : null,
    ]));

    // Window of page numbers to display around the current page, plus first/last.
    $pageWindow = collect(range(max(1, $page - 2), min($lastPage, $page + 2)))
        ->when($page - 2 > 1, fn ($c) => $c->prepend('…')->prepend(1))
        ->when($page + 2 < $lastPage, fn ($c) => $c->push('…')->push($lastPage))
        ->values();

    $chipActive = 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900';
    $chipInactive = 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800';

    $title = $searchQuery !== '' ? __('Search results for “:query”', ['query' => $searchQuery]) : __('All Articles');

    $seo = [
        'description' => __('Browse every article published on :site.', ['site' => $siteName]),
        'canonical' => $searchQuery !== '' ? route('articles') : $pageLink($page),
        'noindex' => $searchQuery !== '',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.seo')
        <link rel="x-search-index" href="{{ route('articles.search-index') }}" />
    </head>
    <body
        x-data="{ mobileOpen: false }"
        class="bg-white text-zinc-900 antialiased selection:bg-zinc-900 selection:text-white dark:bg-zinc-950 dark:text-zinc-100 dark:selection:bg-white dark:selection:text-zinc-900"
    >
        @include('partials.site-header', ['categories' => $categories, 'siteName' => $siteName])

        <main
            id="article-search"
            data-query="{{ $searchQuery }}"
            data-site-name="{{ $siteName }}"
            data-title="{{ __('Search results for “:query”') }}"
            data-count="{{ __(':count article|:count articles') }}"
        >
            {{-- Page header --}}
            <section class="border-b border-zinc-200 dark:border-zinc-800">
                <div class="mx-auto max-w-5xl px-6 py-14 lg:px-8">
                    <div class="flex flex-wrap items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-500">
                        <a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white">{{ __('Home') }}</a>
                        <flux:icon name="chevron-right" class="size-3.5" />
                        <span class="text-zinc-400 dark:text-zinc-600">{{ __('All Articles') }}</span>
                    </div>
                    <h1 data-search-title class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                        @if ($searchQuery !== '')
                            {{ __('Search results for “:query”', ['query' => $searchQuery]) }}
                        @else
                            {{ __('All Articles') }}
                        @endif
                    </h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
                        <span data-search-count>{{ trans_choice(':count article|:count articles', $totalArticles) }}</span>
                        <span data-search-clear @if ($searchQuery === '') hidden @endif>
                            &middot; <a href="{{ route('articles', array_filter(['section' => $selectedSection])) }}" wire:navigate class="underline hover:text-zinc-900 dark:hover:text-white">{{ __('Clear search') }}</a>
                        </span>
                    </p>

                    {{-- Section filter chips --}}
                    <div class="mt-6 flex flex-wrap gap-2" data-chip-active="{{ $chipActive }}" data-chip-inactive="{{ $chipInactive }}">
                        <a
                            href="{{ route('articles', array_filter(['q' => $searchQuery])) }}"
                            wire:navigate
                            data-section=""
                            class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $selectedSection ? $chipActive : $chipInactive }}"
                        >
                            {{ __('All') }}
                        </a>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('articles', array_filter(['section' => $category['id'], 'q' => $searchQuery])) }}"
                                wire:navigate
                                data-section="{{ $category['id'] }}"
                                class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $selectedSection === $category['id'] ? $chipActive : $chipInactive }}"
                            >
                                {{ $category['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Article list --}}
            <section class="mx-auto max-w-5xl px-6 py-12 lg:px-8">
                <p data-search-empty @if ($pagedArticles->isNotEmpty()) hidden @endif class="rounded-2xl border border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/40 dark:text-zinc-400">
                    {{ __('No articles match your search. Try different words.') }}
                </p>

                <div data-search-results class="grid gap-8 sm:grid-cols-2">
                    @foreach ($pagedArticles as $article)
                        @include('partials.article-card')
                    @endforeach
                </div>

                @if ($lastPage > 1)
                    <nav data-search-pagination class="mt-12 flex flex-wrap items-center justify-center gap-2" aria-label="{{ __('Pagination') }}">
                        <a
                            href="{{ $page > 1 ? $pageLink($page - 1) : '#' }}"
                            wire:navigate
                            @class([
                                'rounded-full px-4 py-1.5 text-sm font-medium transition',
                                'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' => $page > 1,
                                'pointer-events-none bg-zinc-50 text-zinc-300 dark:bg-zinc-900/50 dark:text-zinc-700' => $page <= 1,
                            ])
                        >
                            {{ __('Previous') }}
                        </a>

                        @foreach ($pageWindow as $p)
                            @if ($p === '…')
                                <span class="px-2 text-sm text-zinc-400 dark:text-zinc-600">…</span>
                            @else
                                <a
                                    href="{{ $pageLink($p) }}"
                                    wire:navigate
                                    class="min-w-9 rounded-full px-3.5 py-1.5 text-center text-sm font-medium transition {{ $p === $page ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                                >
                                    {{ $p }}
                                </a>
                            @endif
                        @endforeach

                        <a
                            href="{{ $page < $lastPage ? $pageLink($page + 1) : '#' }}"
                            wire:navigate
                            @class([
                                'rounded-full px-4 py-1.5 text-sm font-medium transition',
                                'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' => $page < $lastPage,
                                'pointer-events-none bg-zinc-50 text-zinc-300 dark:bg-zinc-900/50 dark:text-zinc-700' => $page >= $lastPage,
                            ])
                        >
                            {{ __('Next') }}
                        </a>
                    </nav>
                @endif
            </section>
        </main>

        @include('partials.site-footer', ['siteName' => $siteName])

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        {{-- The static export serves this page without running the search
             above, so ?q= is ignored there. When the query in the address
             bar wasn't applied by the server, search the article index in
             the browser with the same rules and show the matches here. --}}
        <script>
            (() => {
                const run = () => {
                    const root = document.getElementById('article-search');
                    const index = document.querySelector('link[rel="x-search-index"]');
                    const params = new URLSearchParams(location.search);
                    const query = (params.get('q') || '').slice(0, 100).trim();

                    if (! root || ! index || root.dataset.query !== '' || query === '' || root.dataset.searchStarted) {
                        return;
                    }
                    root.dataset.searchStarted = '1';

                    const words = query.toLowerCase().split(/\s+/u).filter(Boolean);
                    const chips = root.querySelector('[data-chip-active]');
                    const sections = [...chips.querySelectorAll('[data-section]')].map((chip) => chip.dataset.section);
                    const section = sections.includes(params.get('section')) ? params.get('section') : '';
                    const base = document.querySelector('form[role="search"]')?.getAttribute('action') || location.pathname;

                    fetch(index.href)
                        .then((response) => {
                            if (! response.ok) {
                                throw new Error('Search index: HTTP ' + response.status);
                            }

                            return response.text();
                        })
                        .then((html) => {
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const matches = [...doc.querySelectorAll('[data-search-text]')].filter((item) =>
                                (section === '' || item.dataset.searchSection === section)
                                && words.every((word) => item.dataset.searchText.includes(word)));
                            const [one, many] = root.dataset.count.split('|');
                            const heading = root.dataset.title.replace(':query', query);

                            root.querySelector('[data-search-results]').replaceChildren(
                                ...matches.map((item) => document.importNode(item.firstElementChild, true)),
                            );
                            root.querySelector('[data-search-title]').textContent = heading;
                            root.querySelector('[data-search-count]').textContent = (matches.length === 1 ? one : many).replace(':count', matches.length);
                            root.querySelector('[data-search-clear]').hidden = false;
                            root.querySelector('[data-search-empty]').hidden = matches.length > 0;
                            root.querySelector('[data-search-pagination]')?.remove();
                            document.title = heading + ' — ' + root.dataset.siteName;

                            chips.querySelectorAll('[data-section]').forEach((chip) => {
                                const chipParams = new URLSearchParams(chip.dataset.section ? { section: chip.dataset.section } : {});
                                chipParams.set('q', query);
                                chip.href = base + '?' + chipParams;
                                chip.removeAttribute('wire:navigate');
                                chip.className = chip.className
                                    .replace(chips.dataset.chipActive, '')
                                    .replace(chips.dataset.chipInactive, '')
                                    .trim() + ' ' + (chip.dataset.section === section ? chips.dataset.chipActive : chips.dataset.chipInactive);
                            });

                            document.querySelectorAll('input[name="q"]').forEach((input) => {
                                input.value = query;
                            });
                        })
                        .catch((error) => console.error(error));
                };

                run();

                if (! window.articleSearchListening) {
                    window.articleSearchListening = true;
                    document.addEventListener('livewire:navigated', run);
                }
            })();
        </script>

        @fluxScripts
    </body>
</html>
