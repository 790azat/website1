<?php

use Carbon\Carbon;

beforeEach(function () {
    $this->withoutVite();
});

dataset('articles', fn () => collect(siteData()['articles'])
    ->mapWithKeys(fn ($article) => [$article['slug'] => [$article]])
    ->all());

dataset('sections', fn () => collect(siteData()['sections'])
    ->mapWithKeys(fn ($meta, $key) => [$key => [$key, $meta]])
    ->all());

dataset('programs', fn () => collect(siteData()['programs'])
    ->mapWithKeys(fn ($program) => [$program['slug'] => [$program]])
    ->all());

test('content data references only known sections and authors', function () {
    $data = siteData();

    foreach ($data['articles'] as $article) {
        expect($data['sections'])->toHaveKey($article['section'])
            ->and($data['authors'])->toHaveKey($article['author']);
    }

    foreach ($data['programs'] as $program) {
        expect($data['sections'])->toHaveKey($program['section']);

        if ($program['related_slug']) {
            expect(collect($data['articles'])->pluck('slug'))->toContain($program['related_slug']);
        }
    }
});

test('article and program slugs are unique', function () {
    $data = siteData();
    $slugs = collect($data['articles'])->pluck('slug');
    $programSlugs = collect($data['programs'])->pluck('slug');

    expect($slugs->duplicates())->toBeEmpty()
        ->and($programSlugs->duplicates())->toBeEmpty();
});

test('article page renders', function (array $article) {
    $data = siteData();
    $author = $data['authors'][$article['author']];

    $this->get(route('article', $article['slug']))
        ->assertOk()
        ->assertSee($article['title'])
        ->assertSee($data['sections'][$article['section']]['title'])
        ->assertSee($author['name'])
        ->assertSee(Carbon::parse($article['date'])->format('F j, Y'));
})->with('articles');

test('article page links to related articles in the same section', function () {
    $data = siteData();
    $article = $data['articles'][0];

    $related = collect($data['articles'])
        ->where('section', $article['section'])
        ->where('slug', '!=', $article['slug'])
        ->sortByDesc('date')
        ->take(3);

    $response = $this->get(route('article', $article['slug']))->assertOk();

    expect($related)->not->toBeEmpty();

    foreach ($related as $relatedArticle) {
        $response->assertSee('href="'.route('article', $relatedArticle['slug']).'"', false);
    }
});

test('unknown article returns 404', function () {
    $this->get(route('article', 'this-article-does-not-exist'))->assertNotFound();
});

test('section page renders', function (string $key, array $meta) {
    $count = collect(siteData()['articles'])->where('section', $key)->count();

    $this->get(route('section', $key))
        ->assertOk()
        ->assertSee($meta['title'])
        ->assertSee($count.' '.Str::plural('article', $count));
})->with('sections');

test('section page paginates twelve articles per page, newest first', function () {
    $data = siteData();
    $section = array_key_first($data['sections']);

    $articles = collect($data['articles'])
        ->where('section', $section)
        ->sortByDesc('date')
        ->values();

    expect($articles->count())->toBeGreaterThan(12);

    $link = fn ($article) => 'href="'.route('article', $article['slug']).'"';

    $this->get(route('section', $section))
        ->assertOk()
        ->assertSee($link($articles[0]), false)
        ->assertSee($link($articles[11]), false)
        ->assertDontSee($link($articles[12]), false);

    $this->get(route('section', ['section' => $section, 'page' => 2]))
        ->assertOk()
        ->assertSee($link($articles[12]), false)
        ->assertDontSee($link($articles[0]), false);
});

test('section page beyond the last page shows the last page', function () {
    $data = siteData();
    $section = array_key_first($data['sections']);

    $last = collect($data['articles'])
        ->where('section', $section)
        ->sortByDesc('date')
        ->last();

    $this->get(route('section', ['section' => $section, 'page' => 999]))
        ->assertOk()
        ->assertSee('href="'.route('article', $last['slug']).'"', false);
});

test('unknown section returns 404', function () {
    $this->get(route('section', 'no-such-section'))->assertNotFound();
});

test('program page renders', function (array $program) {
    $response = $this->get(route('program', $program['slug']))
        ->assertOk()
        ->assertSee($program['title'])
        ->assertSee($program['intro'])
        ->assertSee($program['cta_url']);

    if ($program['related_slug']) {
        $response->assertSee('href="'.route('article', $program['related_slug']).'"', false);
    }
})->with('programs');

test('unknown program returns 404', function () {
    $this->get(route('program', 'no-such-program'))->assertNotFound();
});

test('articles index renders and can be filtered by section', function () {
    $data = siteData();
    $section = array_key_last($data['sections']);

    $newestInSection = collect($data['articles'])
        ->where('section', $section)
        ->sortByDesc('date')
        ->first();

    $this->get(route('articles'))->assertOk();

    $this->get(route('articles', ['section' => $section]))
        ->assertOk()
        ->assertSee('href="'.route('article', $newestInSection['slug']).'"', false);
});

test('articles index ignores an unknown section filter', function () {
    $this->get(route('articles', ['section' => 'no-such-section']))->assertOk();
});
