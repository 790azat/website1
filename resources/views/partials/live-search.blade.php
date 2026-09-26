{{--
    Header search box with live results (window.liveSearch in resources/js/app.js).
    Typing shows the first matching articles under the box; Enter goes to the
    full results page on All Articles, or opens the result picked with the
    arrow keys.
    Expects: $id (unique per page, e.g. "desktop") and $mobile (bool).
    Optional: $searchQuery, shown in the box.
--}}
<form
    action="{{ route('articles') }}"
    method="get"
    role="search"
    aria-label="{{ __('Site search') }}"
    x-data="liveSearch(@js($searchQuery ?? ''))"
    @submit="submit($event)"
    @click.outside="open = false"
    @keydown.escape="open = false"
    @class(['relative', 'hidden sm:block' => ! $mobile])
>
    <input
        type="search"
        name="q"
        value="{{ $searchQuery ?? '' }}"
        x-model="query"
        @focus="load(); open = true"
        @input="type()"
        @keydown.arrow-down.prevent="move(1)"
        @keydown.arrow-up.prevent="move(-1)"
        autocomplete="off"
        role="combobox"
        aria-autocomplete="list"
        aria-controls="live-search-{{ $id }}"
        :aria-expanded="showing"
        :aria-activedescendant="active >= 0 ? 'live-search-{{ $id }}-' + active : null"
        placeholder="{{ __('Search') }}"
        aria-label="{{ __('Search articles') }}"
        @class([
            'w-full rounded-full border border-zinc-200 bg-zinc-50 py-2 pr-3 pl-9 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-400 focus:ring-0 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-white' => $mobile,
            'w-40 rounded-full border border-zinc-700 bg-zinc-800 py-1.5 pr-3 pl-9 text-sm text-white placeholder-zinc-400 focus:w-56 focus:border-zinc-500 focus:ring-0 focus:outline-none transition-[width]' => ! $mobile,
        ])
    />
    <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-zinc-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75">
        <circle cx="9" cy="9" r="6" />
        <path d="m17 17-4.35-4.35" stroke-linecap="round" />
    </svg>

    <div
        x-show="showing"
        style="display: none;"
        @class([
            'absolute top-full z-50 mt-2 overflow-hidden rounded-2xl border border-zinc-200 bg-white text-left shadow-xl shadow-zinc-900/10 dark:border-zinc-800 dark:bg-zinc-950',
            'inset-x-0' => $mobile,
            'right-0 w-80' => ! $mobile,
        ])
    >
        <div id="live-search-{{ $id }}" role="listbox" aria-label="{{ __('Search articles') }}">
            <template x-for="(article, i) in results" :key="article.href">
                <a
                    :id="'live-search-{{ $id }}-' + i"
                    :href="article.href"
                    role="option"
                    :aria-selected="active === i"
                    @mouseenter="active = i"
                    class="block border-b border-zinc-100 px-4 py-2.5 dark:border-zinc-800"
                    :class="active === i ? 'bg-zinc-100 dark:bg-zinc-900' : ''"
                >
                    <span class="block text-[11px] font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400" x-text="article.section"></span>
                    <span class="mt-0.5 line-clamp-2 block text-sm font-medium text-zinc-900 dark:text-white" x-text="article.title"></span>
                </a>
            </template>
        </div>

        <p x-show="! articles && ! failed" class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Searching…') }}</p>
        <p x-show="articles && matches.length === 0" class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No articles found.') }}</p>
        <a
            x-show="failed || matches.length > results.length"
            :href="$root.getAttribute('action') + '?q=' + encodeURIComponent(query.trim())"
            class="block px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-900"
        >
            {{ __('Show all results') }}<span x-show="! failed"> (<span x-text="matches.length"></span>)</span>
        </a>
    </div>
</form>
