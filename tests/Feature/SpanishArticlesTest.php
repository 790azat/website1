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

test('every article has a Spanish translation with the same front matter, links and references', function () {
    $articlesPath = resource_path('content/articles');

    foreach (siteData()['articles'] as $article) {
        $file = "{$articlesPath}/es/{$article['slug']}.md";

        expect($file)->toBeFile("{$article['slug']} has no Spanish translation");

        $spanish = SiteContent::parseArticle((string) file_get_contents($file), $article['slug'], $file);

        foreach (['section', 'author', 'date', 'image'] as $field) {
            expect($spanish[$field])->toBe($article[$field], "{$article['slug']}: {$field} differs in Spanish");
        }

        expect($spanish['title'])->not->toBe($article['title'], "{$article['slug']}: title is not translated")
            ->and(linkUrls($spanish['body']))->toBe(linkUrls($article['body']), "{$article['slug']}: links differ in Spanish");

        preg_match('/^(?:## )?References\n\n(.+)/ms', $article['body'], $english);

        expect(preg_match('/^(?:## )?Referencias\n\n(.+)/ms', $spanish['body'], $translated))
            ->toBe(1, "{$article['slug']} has no Referencias section in Spanish")
            ->and(trim($translated[1]))->toBe(trim($english[1]), "{$article['slug']}: references differ in Spanish");
    }
});

test('there are no Spanish files without an English article', function () {
    $slugs = array_column(siteData()['articles'], 'slug');

    foreach (glob(resource_path('content/articles/es/*.md')) as $file) {
        expect($slugs)->toContain(basename($file, '.md'));
    }
});

test('articles are shown in Spanish on the Spanish site', function () {
    $article = siteData()['articles'][0];
    $spanish = siteData()['translations']['es'][$article['slug']];
    $url = route('article', $article['slug']);

    $response = $this->get($url.'?lang=es')->assertOk();

    $response->assertSee($spanish['title'])
        ->assertDontSee('Por ahora, este artículo solo está disponible en inglés.')
        ->assertSee('<link rel="canonical" href="'.$url.'?lang=es" />', false)
        ->assertSee('<link rel="alternate" hreflang="en" href="'.$url.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="'.$url.'?lang=es" />', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="'.$url.'" />', false)
        ->assertSee('"inLanguage":"es"', false);

    preg_match('/<meta name="description" content="([^"]*)"/', $response->getContent(), $description);

    expect(html_entity_decode($description[1]))->toBe(SiteContent::excerpt($spanish['body']));

    $this->get($url.'?lang=en')
        ->assertOk()
        ->assertSee($article['title'])
        ->assertSee('<link rel="canonical" href="'.$url.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="es" href="'.$url.'?lang=es" />', false);
});

test('Spanish reference lists render as links', function () {
    $article = siteData()['articles'][0];
    $references = linkUrls(siteData()['translations']['es'][$article['slug']]['body']);

    $response = $this->get(route('article', ['slug' => $article['slug'], 'lang' => 'es']))
        ->assertOk()
        ->assertSee('Referencias');

    foreach ($references as $url) {
        $response->assertSee('href="'.e($url).'"', false);
    }
});

test('listings and search use the Spanish titles on the Spanish site', function () {
    $article = siteData()['articles'][0];
    $spanish = siteData()['translations']['es'][$article['slug']];

    $this->get(route('articles', ['lang' => 'es']))
        ->assertOk()
        ->assertSee($spanish['title']);

    $this->get(route('articles', ['q' => $spanish['title'], 'lang' => 'es']))
        ->assertOk()
        ->assertSee('href="'.route('article', $article['slug']).'"', false);
});

test('program pages, which are English only, have no Spanish alternate', function () {
    $program = siteData()['programs'][0];

    $this->get(route('program', ['slug' => $program['slug'], 'lang' => 'es']))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('program', $program['slug']).'" />', false)
        ->assertDontSee('hreflang="es" href', false);
});

test('the sitemap lists Spanish pages with hreflang alternates', function () {
    $article = siteData()['articles'][0];
    $url = route('article', $article['slug']);

    $response = $this->get('/sitemap.xml')->assertOk();

    expect(simplexml_load_string($response->getContent()))->not->toBeFalse();

    $alternates = '<xhtml:link rel="alternate" hreflang="en" href="'.$url.'"/>'
        .'<xhtml:link rel="alternate" hreflang="es" href="'.$url.'?lang=es"/>'
        .'<xhtml:link rel="alternate" hreflang="x-default" href="'.$url.'"/>';

    $response->assertSee('<loc>'.$url.'</loc><lastmod>'.$article['date'].'</lastmod>'.$alternates, false)
        ->assertSee('<loc>'.$url.'?lang=es</loc><lastmod>'.$article['date'].'</lastmod>'.$alternates, false)
        ->assertSee('<loc>'.route('home').'?lang=es</loc>', false)
        ->assertDontSee(route('program', siteData()['programs'][0]['slug']).'?lang=es', false);
});
