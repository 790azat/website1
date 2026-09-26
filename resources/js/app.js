/**
 * Live search for the header search boxes: as you type, shows the first
 * matching articles under the box. Articles come from the search index page
 * (<link rel="x-search-index"> in the head), fetched once on first focus. It
 * holds the article links for the current language, so they are right on the
 * static export too. Matching follows the All Articles search: every word
 * must appear in the article's title or text.
 */
const indexes = {};

function loadIndex(url) {
    indexes[url] ??= fetch(url)
        .then((response) => {
            if (! response.ok) {
                throw new Error('Search index: HTTP ' + response.status);
            }

            return response.text();
        })
        .then((html) => [...new DOMParser().parseFromString(html, 'text/html').querySelectorAll('[data-search-text]')].map((item) => ({
            href: item.querySelector('a').getAttribute('href'),
            title: item.querySelector('[data-card-title]').textContent.trim(),
            section: item.querySelector('[data-card-section]').textContent.trim(),
            text: item.dataset.searchText,
        })))
        .catch((error) => {
            delete indexes[url];
            throw error;
        });

    return indexes[url];
}

window.liveSearch = (initial = '') => ({
    query: initial,
    open: false,
    articles: null,
    failed: false,
    active: -1,

    load() {
        const link = document.querySelector('link[rel="x-search-index"]');

        if (this.articles || ! link) {
            return;
        }

        this.failed = false;
        loadIndex(link.href)
            .then((articles) => (this.articles = articles))
            .catch((error) => {
                this.failed = true;
                console.error(error);
            });
    },

    get words() {
        return this.query.slice(0, 100).trim().toLowerCase().split(/\s+/u).filter(Boolean);
    },

    get matches() {
        const words = this.words;

        if (! this.articles || ! words.length) {
            return [];
        }

        // Articles whose title has every word come first, newest first within each group.
        const matches = this.articles.filter((article) => words.every((word) => article.text.includes(word)));
        const inTitle = (article) => words.every((word) => article.title.toLowerCase().includes(word));

        return [...matches.filter(inTitle), ...matches.filter((article) => ! inTitle(article))];
    },

    get results() {
        return this.matches.slice(0, 6);
    },

    get showing() {
        return this.open && this.words.length > 0;
    },

    type() {
        this.open = true;
        this.active = -1;
    },

    move(step) {
        if (! this.results.length) {
            return;
        }

        this.open = true;
        // Cycles through the results and back to the search box (-1).
        this.active += step;
        if (this.active >= this.results.length) {
            this.active = -1;
        } else if (this.active < -1) {
            this.active = this.results.length - 1;
        }
    },

    // Enter on a highlighted result opens it; otherwise the form goes to the
    // full results page as before.
    submit(event) {
        if (this.showing && this.results[this.active]) {
            event.preventDefault();
            window.location.href = this.results[this.active].href;
        }
    },
});
