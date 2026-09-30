<?php

use App\Content\SiteContent;

beforeEach(function () {
    $this->withoutVite();
});

test('pages are in English by default with all language flags', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="en">', false)
        ->assertSee('Latest Articles')
        ->assertSee('href="'.localized(route('home'), 'es').'"', false)
        ->assertSee('href="'.localized(route('home'), 'fr').'"', false)
        ->assertSee('href="'.route('home').'"', false);
});

test('the /es prefix shows the site in Spanish and links stay in Spanish', function () {
    $this->get(localized(route('home'), 'es'))
        ->assertOk()
        ->assertSee('href="'.localized(route('team'), 'es').'"', false)
        ->assertSee('<html lang="es">', false)
        ->assertSee('Últimos artículos')
        ->assertSee('Inteligencia de Datos');

    $this->get(localized(route('team'), 'es'))
        ->assertOk()
        ->assertSee('Nuestro equipo editorial');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Latest Articles');
});

test('the /fr prefix shows the site in French', function () {
    $this->get(localized(route('home'), 'fr'))
        ->assertOk()
        ->assertSee('<html lang="fr">', false)
        ->assertSee('Derniers articles')
        ->assertSee('Intelligence des données');

    $this->get(localized(route('team'), 'fr'))
        ->assertOk()
        ->assertSee('Notre équipe éditoriale');
});

test('old lang links redirect permanently to the prefixed address', function () {
    $this->get(route('team', ['lang' => 'es']))->assertStatus(301)->assertRedirect(localized(route('team'), 'es'));
    $this->get(route('home', ['lang' => 'fr']))->assertStatus(301)->assertRedirect(localized(route('home'), 'fr'));
    $this->get(localized(route('team'), 'es').'?lang=en')->assertStatus(301)->assertRedirect(route('team'));
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

    $this->get(localized(route('article', $article['slug']), 'es'))
        ->assertOk()
        ->assertSee($article['title'])
        ->assertSee('Por ahora, este artículo solo está disponible en inglés.')
        ->assertDontSee('hreflang="es" href', false);

    $this->get(route('article', $article['slug']))
        ->assertOk()
        ->assertDontSee('available in English only');

    unlink($articles.'/'.$article['slug'].'.md');
    rmdir($articles);
});

test('every translation keeps the placeholders of its English key', function (string $locale) {
    $translations = json_decode(file_get_contents(lang_path($locale.'.json')), true, flags: JSON_THROW_ON_ERROR);

    foreach ($translations as $english => $translated) {
        preg_match_all('/:[a-z]+/', $english, $expected);
        preg_match_all('/:[a-z]+/', $translated, $actual);

        expect(array_unique($actual[0]))->toEqualCanonicalizing(array_unique($expected[0]), $english);
    }
})->with(['es', 'fr']);

test('the French and Spanish UI strings cover the same English keys', function () {
    $spanish = json_decode(file_get_contents(lang_path('es.json')), true, flags: JSON_THROW_ON_ERROR);
    $french = json_decode(file_get_contents(lang_path('fr.json')), true, flags: JSON_THROW_ON_ERROR);

    expect(array_keys($french))->toEqualCanonicalizing(array_keys($spanish));
});
