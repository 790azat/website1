<?php

use App\Content\SiteContent;

beforeEach(function () {
    $this->withoutVite();
});

test('page titles name the site once', function () {
    $siteName = config('app.name');
    $article = siteData()['articles'][0];

    $this->get(route('article', $article['slug']))
        ->assertOk()
        ->assertSee('<title>'.e($article['title']).' — '.e($siteName).'</title>', false);

    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('<title>Contact — '.e($siteName).'</title>', false);
});

test('article pages have a plain-text description, canonical, Open Graph and JSON-LD', function () {
    $article = siteData()['articles'][0];
    $url = route('article', $article['slug']);

    $response = $this->get($url.'?lang=en')->assertOk();

    $response->assertSee('<link rel="canonical" href="'.$url.'" />', false)
        ->assertSee('<meta property="og:type" content="article" />', false)
        ->assertSee('<meta property="og:image" content="'.asset('images/'.$article['image']).'" />', false)
        ->assertSee('"@type":"Article"', false)
        ->assertSee('"@type":"BreadcrumbList"', false);

    preg_match('/<meta name="description" content="([^"]*)"/', $response->getContent(), $match);

    expect($match[1] ?? '')->not->toBe('')
        ->and($match[1])->not->toContain('##')
        ->and($match[1])->not->toContain('](');
});

test('the excerpt strips Markdown and cuts at a word boundary', function () {
    $body = "## Heading\n\nSome **bold** text with a [link](https://example.com).\n\n* First item\n* Second item";

    expect(SiteContent::excerpt($body))->toBe('Heading Some bold text with a link. First item Second item')
        ->and(SiteContent::excerpt(str_repeat('word ', 100), 30))->toBe('word word word word word word…');
});

test('the header search finds articles by title and body words', function () {
    $article = siteData()['articles'][0];
    $words = preg_split('/\s+/', mb_strtolower($article['title']));
    $other = collect(siteData()['articles'])->first(function ($a) use ($words) {
        $text = mb_strtolower($a['title'].' '.$a['body']);

        return collect($words)->contains(fn ($word) => ! str_contains($text, $word));
    });

    $this->get(route('home'))
        ->assertSee('action="'.route('articles').'"', false)
        ->assertSee('name="q"', false);

    $this->get(route('articles', ['q' => $article['title']]))
        ->assertOk()
        ->assertSee('href="'.route('article', $article['slug']).'"', false)
        ->assertDontSee('href="'.route('article', $other['slug']).'"', false)
        ->assertSee('<meta name="robots" content="noindex, follow" />', false);
});

test('a search with no matches says so', function () {
    $this->get(route('articles', ['q' => 'zzqxnomatchword']))
        ->assertOk()
        ->assertSee('No articles match your search.');
});

test('a malformed search parameter is ignored', function () {
    $this->get(route('articles').'?q[]=x')->assertOk();
});

test('the sitemap lists every article, section and program', function () {
    $data = siteData();

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $xml = simplexml_load_string($response->getContent());
    expect($xml)->not->toBeFalse();

    $response->assertSee('<loc>'.route('home').'</loc>', false);

    foreach ($data['articles'] as $article) {
        $response->assertSee('<loc>'.route('article', $article['slug']).'</loc><lastmod>'.$article['date'].'</lastmod>', false);
    }

    foreach (array_keys($data['sections']) as $section) {
        $response->assertSee('<loc>'.route('section', $section).'</loc>', false);
    }

    foreach ($data['programs'] as $program) {
        $response->assertSee('<loc>'.route('program', $program['slug']).'</loc>', false);
    }
});

test('account pages from the starter kit are gone', function (string $path) {
    $this->get($path)->assertNotFound();
})->with(['/login', '/register', '/dashboard', '/settings', '/settings/profile', '/forgot-password']);
