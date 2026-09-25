@php
    $siteName = config('app.name', 'Laravel');
    $data = app(\App\Content\SiteContent::class)->localized();

    $sectionStyles = [
        'data-intelligence' => ['badge' => 'bg-sky-600'],
        'business-strategy' => ['badge' => 'bg-amber-600'],
        'digital-horizons' => ['badge' => 'bg-violet-600'],
        'people-impact' => ['badge' => 'bg-rose-600'],
    ];

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
    // the article's title or body.
    $searchQuery = trim(Str::limit(is_string(request()->query('q')) ? request()->query('q') : '', 100, ''));
    $searchWords = preg_split('/\s+/u', mb_strtolower($searchQuery), -1, PREG_SPLIT_NO_EMPTY);

    $allArticles = collect($data['articles'])
        ->when($selectedSection, fn ($collection) => $collection->where('section', $selectedSection))
        ->when($searchWords, fn ($collection) => $collection->filter(function ($article) use ($searchWords) {
            $haystack = mb_strtolower($article['title'].' '.$article['body']);

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
    </head>
    <body
        x-data="{ mobileOpen: false }"
        class="bg-white text-zinc-900 antialiased selection:bg-zinc-900 selection:text-white dark:bg-zinc-950 dark:text-zinc-100 dark:selection:bg-white dark:selection:text-zinc-900"
    >
        @include('partials.site-header', ['categories' => $categories, 'siteName' => $siteName])

        <main>
            {{-- Page header --}}
            <section class="border-b border-zinc-200 dark:border-zinc-800">
                <div class="mx-auto max-w-5xl px-6 py-14 lg:px-8">
                    <div class="flex flex-wrap items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-500">
                        <a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-900 dark:hover:text-white">{{ __('Home') }}</a>
                        <flux:icon name="chevron-right" class="size-3.5" />
                        <span class="text-zinc-400 dark:text-zinc-600">{{ __('All Articles') }}</span>
                    </div>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                        @if ($searchQuery !== '')
                            {{ __('Search results for “:query”', ['query' => $searchQuery]) }}
                        @else
                            {{ __('All Articles') }}
                        @endif
                    </h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">
                        {{ trans_choice(':count article|:count articles', $totalArticles) }}
                        @if ($searchQuery !== '')
                            &middot; <a href="{{ route('articles', array_filter(['section' => $selectedSection])) }}" wire:navigate class="underline hover:text-zinc-900 dark:hover:text-white">{{ __('Clear search') }}</a>
                        @endif
                    </p>

                    {{-- Section filter chips --}}
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a
                            href="{{ route('articles', array_filter(['q' => $searchQuery])) }}"
                            wire:navigate
                            class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $selectedSection ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            {{ __('All') }}
                        </a>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('articles', array_filter(['section' => $category['id'], 'q' => $searchQuery])) }}"
                                wire:navigate
                                class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $selectedSection === $category['id'] ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                            >
                                {{ $category['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Article list --}}
            <section class="mx-auto max-w-5xl px-6 py-12 lg:px-8">
                @if ($pagedArticles->isEmpty())
                    <p class="rounded-2xl border border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/40 dark:text-zinc-400">
                        {{ __('No articles match your search. Try different words.') }}
                    </p>
                @endif

                <div class="grid gap-8 sm:grid-cols-2">
                    @foreach ($pagedArticles as $article)
                        @php
                            $author = $data['authors'][$article['author']];
                            $sectionTitle = $data['sections'][$article['section']]['title'];
                            $badge = $sectionStyles[$article['section']]['badge'] ?? 'bg-zinc-600';
                        @endphp
                        <a href="{{ route('article', $article['slug']) }}" wire:navigate class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:hover:border-zinc-700">
                            @if ($article['image'] ?? null)
                                <div class="aspect-video w-full overflow-hidden">
                                    <img
                                        loading="lazy"
                                        decoding="async"
                                        src="{{ asset('images/'.$article['image']) }}"
                                        alt="{{ $article['title'] }}"
                                        class="size-full object-cover transition duration-300 group-hover:scale-105"
                                    />
                                </div>
                            @endif
                            <div class="flex flex-1 flex-col p-6">
                                <span class="inline-flex w-fit items-center gap-1.5 rounded-full {{ $badge }} px-2.5 py-1 text-xs font-medium text-white">
                                    {{ $sectionTitle }}
                                </span>
                                <h2 class="mt-3 font-semibold text-zinc-900 group-hover:underline dark:text-white">
                                    {{ $article['title'] }}
                                </h2>
                                <div class="mt-4 flex items-center gap-2.5">
                                    <img
                                        loading="lazy"
                                        decoding="async"
                                        src="{{ asset('images/team/'.$author['photo']) }}"
                                        alt="{{ $author['name'] }}"
                                        class="size-8 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-800"
                                    />
                                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $author['name'] }}</span>
                                        <div>{{ \Carbon\Carbon::parse($article['date'])->translatedFormat(__('F j, Y')) }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($lastPage > 1)
                    <nav class="mt-12 flex flex-wrap items-center justify-center gap-2" aria-label="{{ __('Pagination') }}">
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

        @fluxScripts
    </body>
</html>
