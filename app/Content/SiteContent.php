<?php

namespace App\Content;

use RuntimeException;

/**
 * Loads the site's content: sections, authors and programs from
 * resources/data/site.php, and articles from one Markdown file per article
 * in resources/content/articles/<slug>.md.
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
     * the current locale. Article and program text is left as written.
     *
     * @return array<string, mixed>
     */
    public function localized(): array
    {
        $data = $this->all();

        foreach ($data['sections'] as $key => $section) {
            $data['sections'][$key]['title'] = __($section['title']);
        }

        foreach ($data['authors'] as $key => $author) {
            $data['authors'][$key]['role'] = __($author['role']);
        }

        return $data;
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

        return $data;
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
}
