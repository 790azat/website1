<?php

use App\Content\SiteContent;

beforeEach(function () {
    $this->withoutVite();
});

test('pages are in English by default with both language flags', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="en">', false)
        ->assertSee('Latest Articles')
        ->assertSee('lang=es', false)
        ->assertSee('lang=en', false);
});

test('the lang parameter switches to Spanish and is remembered in the session', function () {
    $this->get(route('home', ['lang' => 'es']))
        ->assertOk()
        ->assertSessionHas('locale', 'es')
        ->assertSee('<html lang="es">', false)
        ->assertSee('Últimos artículos')
        ->assertSee('Inteligencia de Datos');

    $this->get(route('team'))
        ->assertOk()
        ->assertSee('Nuestro equipo editorial');

    $this->get(route('home', ['lang' => 'en']))
        ->assertOk()
        ->assertSessionHas('locale', 'en')
        ->assertSee('Latest Articles');
});

test('an unsupported language is ignored', function () {
    $this->get(route('home', ['lang' => 'xx']))
        ->assertOk()
        ->assertSessionMissing('locale')
        ->assertSee('Latest Articles');
});

test('articles without a Spanish translation say they are available in English only', function () {
    $article = siteData()['articles'][0];

    // A copy of the content with this article's English file only.
    $articles = sys_get_temp_dir().'/untranslated-'.uniqid();
    mkdir($articles);
    file_put_contents($articles.'/'.$article['slug'].'.md', SiteContent::renderArticle($article));
    app()->instance(SiteContent::class, new SiteContent(resource_path('data/site.php'), $articles, ''));

    $this->get(route('article', ['slug' => $article['slug'], 'lang' => 'es']))
        ->assertOk()
        ->assertSee($article['title'])
        ->assertSee('Por ahora, este artículo solo está disponible en inglés.')
        ->assertDontSee('hreflang="es" href', false);

    $this->get(route('article', ['slug' => $article['slug'], 'lang' => 'en']))
        ->assertOk()
        ->assertDontSee('available in English only');

    unlink($articles.'/'.$article['slug'].'.md');
    rmdir($articles);
});

test('every Spanish translation keeps the placeholders of its English key', function () {
    $translations = json_decode(file_get_contents(lang_path('es.json')), true, flags: JSON_THROW_ON_ERROR);

    foreach ($translations as $english => $spanish) {
        preg_match_all('/:[a-z]+/', $english, $expected);
        preg_match_all('/:[a-z]+/', $spanish, $actual);

        expect(array_unique($actual[0]))->toEqualCanonicalizing(array_unique($expected[0]), $english);
    }
});
