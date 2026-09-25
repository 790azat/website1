<?php

use App\Content\SiteContent;

test('every article has valid front matter, a known section and author, and an existing image', function () {
    $content = app(SiteContent::class)->build();

    expect($content['articles'])->not->toBeEmpty();

    foreach ($content['articles'] as $article) {
        expect($content['sections'])->toHaveKey($article['section'])
            ->and($content['authors'])->toHaveKey($article['author'])
            ->and($article['date'])->toMatch('/^\d{4}-\d{2}-\d{2}$/')
            ->and($article['body'])->not->toBeEmpty()
            ->and(public_path('images/'.$article['image']))->toBeFile();
    }
});

test('every program links to an existing article', function () {
    $content = app(SiteContent::class)->build();
    $slugs = array_column($content['articles'], 'slug');

    foreach ($content['programs'] as $program) {
        if ($program['related_slug'] ?? null) {
            expect($slugs)->toContain($program['related_slug']);
        }
    }
});

test('every article page renders', function () {
    foreach (app(SiteContent::class)->all()['articles'] as $article) {
        $this->get(route('article', $article['slug']))
            ->assertOk()
            ->assertSee($article['title']);
    }
});

test('listing pages render', function () {
    $content = app(SiteContent::class)->all();

    $this->get(route('articles'))->assertOk();

    foreach (array_keys($content['sections']) as $section) {
        $this->get(route('section', $section))->assertOk();
    }

    foreach ($content['programs'] as $program) {
        $this->get(route('program', $program['slug']))->assertOk();
    }
});

test('an unknown article returns 404', function () {
    $this->get(route('article', 'no-such-article'))->assertNotFound();
});

test('articles round-trip through their Markdown format', function () {
    $article = [
        'slug' => 'example',
        'title' => 'Example: A Title With A Colon',
        'section' => 'digital-horizons',
        'author' => 'james-mitchell',
        'date' => '2026-01-10',
        'image' => 'articles/example.jpg',
        'body' => "First paragraph.\n\n## Heading\n\n* One\n* Two",
    ];

    expect(SiteContent::parseArticle(SiteContent::renderArticle($article), 'example'))->toBe($article);
});

test('an article without front matter is rejected', function () {
    SiteContent::parseArticle('Just a body.', 'broken', 'broken.md');
})->throws(RuntimeException::class, 'missing its front matter');

test('an article missing a field is rejected', function () {
    SiteContent::parseArticle("---\ntitle: Only a title\n---\n\nBody", 'broken', 'broken.md');
})->throws(RuntimeException::class, 'missing the [section] field');

test('content can be cached and cleared', function () {
    $content = app(SiteContent::class);

    $this->artisan('content:cache')->assertSuccessful();

    expect($content->cachePath())->toBeFile()
        ->and(require $content->cachePath())->toBe($content->build());

    $this->artisan('content:clear')->assertSuccessful();

    expect($content->cachePath())->not->toBeFile();
});

test('every article has a References section whose links render', function () {
    foreach (app(SiteContent::class)->all()['articles'] as $article) {
        expect(preg_match('/^(?:## )?References\n\n(.+)/ms', $article['body'], $m))
            ->toBe(1, "{$article['slug']} has no References section");

        $response = $this->get(route('article', $article['slug']))->assertOk();

        if (preg_match('/\]\((https?:\/\/[^\s)]+)\)/', $m[1], $link)) {
            $response->assertSee('href="'.e($link[1]).'"', false);
        }
    }
});
