{{--
    One article card in the All Articles grid (also used by the search index
    that the static export searches in the browser).
    Expects: $article and $data (SiteContent::localized()).
--}}
@php
    $sectionStyles = [
        'data-intelligence' => ['badge' => 'bg-sky-600'],
        'business-strategy' => ['badge' => 'bg-amber-600'],
        'digital-horizons' => ['badge' => 'bg-violet-600'],
        'people-impact' => ['badge' => 'bg-rose-600'],
    ];
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
