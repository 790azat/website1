<?php

namespace App\Content;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Loads the site's content: sections, authors and programs from
 * resources/data/site.php, and articles from one Markdown file per article
 * in resources/content/articles/<slug>.md.
 *
 * Translations of an article live next to it in a folder named after the
 * locale, e.g. resources/content/articles/es/<slug>.md or fr/<slug>.md. They use the same
 * format; only their title and body are used, and an article without a
 * translation is shown in English.
 *
 * Each article file starts with a front matter block of "key: value" lines
 * between "---" markers, followed by the article body.
 *
 * Parsing every file on each request is avoided by `php artisan content:cache`
 * (run on deploy and by `php artisan optimize`), which writes the parsed
 * content to bootstrap/cache/content.php.
 */
class SiteContent
{
    /**
     * Front matter keys every article must define, in the order they
     * appear in the article array.
     */
    public const ARTICLE_FIELDS = ['title', 'section', 'author', 'date', 'image'];

    /**
     * Locales, other than English, that articles can be translated into.
     */
    public const TRANSLATION_LOCALES = ['es', 'fr'];

    /** @var array<string, mixed>|null */
    private ?array $content = null;

    public function __construct(
        private readonly string $dataPath,
        private readonly string $articlesPath,
        private readonly string $cachePath,
    ) {}

    /**
     * All site content, keyed by sections, authors, articles and programs.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->content !== null) {
            return $this->content;
        }

        if (is_file($this->cachePath)) {
            /** @var array<string, mixed> $cached */
            $cached = require $this->cachePath;

            return $this->content = $cached;
        }

        return $this->content = $this->build();
    }

    /**
     * All site content, with section titles and author roles translated into
     * the current locale, and each article's title and body replaced by its
     * translation when there is one. Every article gets a "locale" key naming
     * the language it will be shown in. Program copy is translated through
     * lang/{locale}.json.
     *
     * @return array<string, mixed>
     */
    public function localized(): array
    {
        $data = $this->all();
        $locale = app()->getLocale();

        /** @var array<string, array{title: string, body: string}> $translations */
        $translations = $data['translations'][$locale] ?? [];

        foreach ($data['articles'] as $key => $article) {
            $data['articles'][$key] = isset($translations[$article['slug']])
                ? ['locale' => $locale] + $translations[$article['slug']] + $article
                : ['locale' => 'en'] + $article;
        }

        foreach ($data['sections'] as $key => $section) {
            $data['sections'][$key]['title'] = __($section['title']);
        }

        foreach ($data['authors'] as $key => $author) {
            $data['authors'][$key]['role'] = __($author['role']);
        }

        if ($locale !== 'en') {
            foreach ($data['programs'] ?? [] as $key => $program) {
                $data['programs'][$key] = $this->localizeProgram($program);
            }
        }

        return $data;
    }

    /**
     * A program with its copy looked up in lang/{locale}.json. Text without a
     * translation stays in English; slugs, URLs, icons and images are kept.
     *
     * @param  array<string, mixed>  $program
     * @return array<string, mixed>
     */
    private function localizeProgram(array $program): array
    {
        $translate = fn (mixed $text) => is_string($text) && $text !== '' ? __($text) : $text;

        foreach (['title', 'intro', 'cta_label', 'hero_tagline', 'overview_heading', 'overview_intro'] as $key) {
            if (isset($program[$key])) {
                $program[$key] = $translate($program[$key]);
            }
        }

        foreach (['features', 'pros', 'extra_sections'] as $group) {
            foreach ($program[$group] ?? [] as $i => $item) {
                foreach ($item as $key => $value) {
                    if ($key === 'icon') {
                        continue;
                    }

                    $program[$group][$i][$key] = is_array($value) ? array_map($translate, $value) : $translate($value);
                }
            }
        }

        return $program;
    }

    /**
     * Parse the content from source files, ignoring any cache.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        /** @var array<string, mixed> $data */
        $data = require $this->dataPath;

        $data['articles'] = $this->loadArticles();
        $data['translations'] = [];

        foreach (self::TRANSLATION_LOCALES as $locale) {
            $data['translations'][$locale] = $this->loadTranslations($locale);
        }

        return $data;
    }

    /**
     * Whether an article has a translation into the given locale. English,
     * the language articles are written in, always counts as translated.
     */
    public function hasTranslation(string $slug, string $locale): bool
    {
        return $locale === 'en' || isset($this->all()['translations'][$locale][$slug]);
    }

    /**
     * The locales, other than English, that an article has been translated
     * into.
     *
     * @return list<string>
     */
    public function translatedLocales(string $slug): array
    {
        return array_values(array_filter(self::TRANSLATION_LOCALES, fn (string $locale) => $this->hasTranslation($slug, $locale)));
    }

    /**
     * Write the parsed content to the cache file.
     */
    public function cache(): void
    {
        $contents = '<?php return '.var_export($this->build(), true).';'.PHP_EOL;

        if (file_put_contents($this->cachePath, $contents) === false) {
            throw new RuntimeException("Unable to write content cache to [{$this->cachePath}].");
        }

        $this->content = null;
    }

    /**
     * Delete the cache file, if any.
     */
    public function clearCache(): void
    {
        if (is_file($this->cachePath)) {
            unlink($this->cachePath);
        }

        $this->content = null;
    }

    public function cachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * @return list<array<string, string>>
     */
    private function loadArticles(): array
    {
        $files = glob($this->articlesPath.'/*.md') ?: [];

        $articles = array_map(
            fn (string $file) => self::parseArticle((string) file_get_contents($file), basename($file, '.md'), $file),
            $files,
        );

        usort($articles, fn (array $a, array $b) => [$b['date'], $a['slug']] <=> [$a['date'], $b['slug']]);

        return $articles;
    }

    /**
     * The translated title and body of each article, keyed by slug.
     *
     * @return array<string, array{title: string, body: string}>
     */
    private function loadTranslations(string $locale): array
    {
        $translations = [];

        foreach (glob($this->articlesPath.'/'.$locale.'/*.md') ?: [] as $file) {
            $article = self::parseArticle((string) file_get_contents($file), basename($file, '.md'), $file);

            $translations[$article['slug']] = ['title' => $article['title'], 'body' => $article['body']];
        }

        ksort($translations);

        return $translations;
    }

    /**
     * Parse one article file into the article array used by the views.
     *
     * @return array<string, string>
     */
    public static function parseArticle(string $contents, string $slug, string $file = ''): array
    {
        $contents = str_replace("\r\n", "\n", $contents);

        if (! str_starts_with($contents, "---\n") || ($end = strpos($contents, "\n---\n", 3)) === false) {
            throw new RuntimeException("Article [{$file}] is missing its front matter block.");
        }

        $meta = [];

        foreach (explode("\n", substr($contents, 4, $end - 4)) as $line) {
            if (trim($line) === '') {
                continue;
            }

            if (! str_contains($line, ':')) {
                throw new RuntimeException("Article [{$file}] has an invalid front matter line: {$line}");
            }

            [$key, $value] = explode(':', $line, 2);
            $meta[trim($key)] = trim($value);
        }

        $article = ['slug' => $slug];

        foreach (self::ARTICLE_FIELDS as $field) {
            if (($meta[$field] ?? '') === '') {
                throw new RuntimeException("Article [{$file}] is missing the [{$field}] field.");
            }

            $article[$field] = $meta[$field];
        }

        $article['body'] = trim(substr($contents, $end + 5), "\n");

        return $article;
    }

    /**
     * Render an article array back into its Markdown file contents.
     *
     * @param  array<string, string>  $article
     */
    public static function renderArticle(array $article): string
    {
        $lines = ['---'];

        foreach (self::ARTICLE_FIELDS as $field) {
            $lines[] = $field.': '.$article[$field];
        }

        $lines[] = '---';

        return implode("\n", $lines)."\n\n".$article['body']."\n";
    }

    /**
     * A plain-text summary of an article body for meta descriptions: Markdown
     * headings, list markers, emphasis and link URLs are removed, whitespace
     * is collapsed, and the result is cut at a word boundary.
     */
    public static function excerpt(string $body, int $limit = 160): string
    {
        $text = preg_replace('/\[([^\]]*)\]\([^)]*\)/', '$1', $body) ?? $body;
        $text = preg_replace('/^\s{0,3}(#{1,6}\s+|[*+-]\s+|\d+[.)]\s+|>\s?)/m', '', $text) ?? $text;
        $text = str_replace(['**', '__', '`'], '', $text);
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(Str::limit($text, $limit - 1, '', preserveWords: true), ' ,.;:-').'…';
    }
}
