<?php

use App\Content\SiteContent;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * The link URLs in a Markdown body, in order.
 *
 * @return list<string>
 */
function linkUrls(string $body): array
{
    preg_match_all('/\]\((https?:\/\/[^\s)]+)\)/', $body, $matches);

    return $matches[1];
}

/**
 * The hreflang alternates the sitemap and pages list for a URL that exists
 * in every language.
 */
function sitemapAlternates(string $url): string
{
    return '<xhtml:link rel="alternate" hreflang="en" href="'.$url.'"/>'
        .'<xhtml:link rel="alternate" hreflang="es" href="'.localized($url, 'es').'"/>'
        .'<xhtml:link rel="alternate" hreflang="fr" href="'.localized($url, 'fr').'"/>'
        .'<xhtml:link rel="alternate" hreflang="x-default" href="'.$url.'"/>';
}

dataset('translation locales', [
    'Spanish' => ['es', 'Referencias'],
    'French' => ['fr', 'Références'],
]);

test('every article has a translation with the same front matter, links and references', function (string $locale, string $references) {
    $articlesPath = resource_path('content/articles');

    foreach (siteData()['articles'] as $article) {
        $file = "{$articlesPath}/{$locale}/{$article['slug']}.md";

        expect($file)->toBeFile("{$article['slug']} has no {$locale} translation");

        $translation = SiteContent::parseArticle((string) file_get_contents($file), $article['slug'], $file);

        foreach (['section', 'author', 'date', 'image'] as $field) {
            expect($translation[$field])->toBe($article[$field], "{$article['slug']}: {$field} differs in {$locale}");
        }

        expect($translation['title'])->not->toBe($article['title'], "{$article['slug']}: title is not translated into {$locale}")
            ->and(linkUrls($translation['body']))->toBe(linkUrls($article['body']), "{$article['slug']}: links differ in {$locale}");

        preg_match('/^(?:## )?References\n\n(.+)/ms', $article['body'], $english);

        expect(preg_match('/^(?:## )?'.$references.'\n\n(.+)/ms', $translation['body'], $translated))
            ->toBe(1, "{$article['slug']} has no {$references} section in {$locale}")
            ->and(trim($translated[1]))->toBe(trim($english[1]), "{$article['slug']}: references differ in {$locale}");
    }
})->with('translation locales');

test('there are no translation files without an English article', function (string $locale) {
    $slugs = array_column(siteData()['articles'], 'slug');

    foreach (glob(resource_path("content/articles/{$locale}/*.md")) as $file) {
        expect($slugs)->toContain(basename($file, '.md'));
    }
})->with('translation locales');

test('articles are shown translated on the translated site', function (string $locale) {
    $article = siteData()['articles'][0];
    $translation = siteData()['translations'][$locale][$article['slug']];
    $url = route('article', $article['slug']);

    $response = $this->get(localized($url, $locale))->assertOk();

    $response->assertSee($translation['title'])
        ->assertDontSee(trans('This article is currently available in English only.', [], $locale))
        ->assertSee('<link rel="canonical" href="'.localized($url, $locale).'" />', false)
        ->assertSee('<link rel="alternate" hreflang="en" href="'.$url.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="'.localized($url, 'es').'" />', false)
        ->assertSee('<link rel="alternate" hreflang="fr" href="'.localized($url, 'fr').'" />', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="'.$url.'" />', false)
        ->assertSee('"inLanguage":"'.$locale.'"', false);

    preg_match('/<meta name="description" content="([^"]*)"/', $response->getContent(), $description);

    expect(html_entity_decode($description[1]))->toBe(SiteContent::excerpt($translation['body']));

    $this->get($url)
        ->assertOk()
        ->assertSee($article['title'])
        ->assertSee('<link rel="canonical" href="'.$url.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="'.$locale.'" href="'.localized($url, $locale).'" />', false);
})->with('translation locales');

test('translated reference lists render as links', function (string $locale, string $references) {
    $article = siteData()['articles'][0];
    $urls = linkUrls(siteData()['translations'][$locale][$article['slug']]['body']);

    $response = $this->get(localized(route('article', $article['slug']), $locale))
        ->assertOk()
        ->assertSee($references);

    foreach ($urls as $url) {
        $response->assertSee('href="'.e($url).'"', false);
    }
})->with('translation locales');

test('listings and search use the translated titles on the translated site', function (string $locale) {
    $article = siteData()['articles'][0];
    $translation = siteData()['translations'][$locale][$article['slug']];

    $this->get(localized(route('articles'), $locale))
        ->assertOk()
        ->assertSee($translation['title']);

    $this->get(localized(route('articles', ['q' => $translation['title']]), $locale))
        ->assertOk()
        ->assertSee('href="'.localized(route('article', $article['slug']), $locale).'"', false);
})->with('translation locales');

test('program pages, which are English only, have no translated alternates', function (string $locale) {
    $program = siteData()['programs'][0];

    $this->get(localized(route('program', $program['slug']), $locale))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('program', $program['slug']).'" />', false)
        ->assertDontSee('hreflang="'.$locale.'" href', false);
})->with('translation locales');

test('the sitemap lists translated pages with hreflang alternates', function () {
    $article = siteData()['articles'][0];
    $url = route('article', $article['slug']);
    $alternates = sitemapAlternates($url);

    $response = $this->get('/sitemap.xml')->assertOk();

    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();

    $response->assertSee('<loc>'.$url.'</loc><lastmod>'.$article['date'].'</lastmod>'.$alternates, false)
        ->assertSee('<loc>'.localized($url, 'es').'</loc><lastmod>'.$article['date'].'</lastmod>'.$alternates, false)
        ->assertSee('<loc>'.localized($url, 'fr').'</loc><lastmod>'.$article['date'].'</lastmod>'.$alternates, false)
        ->assertSee('<loc>'.localized(route('home'), 'es').'</loc>', false)
        ->assertSee('<loc>'.localized(route('home'), 'fr').'</loc>', false)
        ->assertDontSee(localized(route('program', siteData()['programs'][0]['slug']), 'es'), false);
});

test('an article translated into only some languages lists only those as alternates', function () {
    $article = siteData()['articles'][0];

    // A copy of the content with this article's English and Spanish files only.
    $articles = sys_get_temp_dir().'/partly-translated-'.uniqid();
    mkdir($articles.'/es', recursive: true);
    file_put_contents($articles.'/'.$article['slug'].'.md', SiteContent::renderArticle($article));
    copy(resource_path('content/articles/es/'.$article['slug'].'.md'), $articles.'/es/'.$article['slug'].'.md');
    app()->instance(SiteContent::class, new SiteContent(resource_path('data/site.php'), $articles, ''));

    $url = route('article', $article['slug']);

    $this->get(localized($url, 'fr'))
        ->assertOk()
        ->assertSee('Pour l’instant, cet article n’est disponible qu’en anglais.')
        ->assertSee('<link rel="canonical" href="'.$url.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="'.localized($url, 'es').'" />', false)
        ->assertDontSee('hreflang="fr" href', false);

    unlink($articles.'/es/'.$article['slug'].'.md');
    unlink($articles.'/'.$article['slug'].'.md');
    rmdir($articles.'/es');
    rmdir($articles);
});
