<?php

namespace App\Http\Controllers;

use App\Content\SiteContent;
use App\Http\Middleware\SetLocale;
use App\Support\Seo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * An XML sitemap of every public page, for search engines. Pages with
     * translations (the "/es/..." and "/fr/..." URLs) list them too, and
     * all versions name each other as hreflang alternates.
     */
    public function __invoke(SiteContent $content): Response
    {
        $data = $content->all();

        /** @var list<array{slug: string, section: string, date: string}> $articles */
        $articles = $data['articles'];

        /** @var array<string, mixed> $sections */
        $sections = $data['sections'];

        /** @var list<array{slug: string}> $programs */
        $programs = $data['programs'];

        $latest = collect($articles)->max('date');

        $urls = [
            ['loc' => $this->path('home'), 'lastmod' => $latest, 'locales' => SiteContent::TRANSLATION_LOCALES],
            ['loc' => $this->path('articles'), 'lastmod' => $latest, 'locales' => SiteContent::TRANSLATION_LOCALES],
        ];

        foreach (array_keys($sections) as $section) {
            $urls[] = [
                'loc' => $this->path('section', $section),
                'lastmod' => collect($articles)->where('section', $section)->max('date'),
                'locales' => SiteContent::TRANSLATION_LOCALES,
            ];
        }

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => $this->path('article', $article['slug']),
                'lastmod' => $article['date'],
                'locales' => $content->translatedLocales($article['slug']),
            ];
        }

        foreach ($programs as $program) {
            $urls[] = ['loc' => $this->path('program', $program['slug']), 'lastmod' => null, 'locales' => []];
        }

        foreach (['team', 'contact', 'privacy-policy', 'terms-of-use'] as $page) {
            $urls[] = ['loc' => $this->path($page), 'lastmod' => null, 'locales' => SiteContent::TRANSLATION_LOCALES];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $versions = ['en' => Seo::url($url['loc'], 'en')];

            foreach ($url['locales'] as $locale) {
                $versions[$locale] = Seo::url($url['loc'], $locale);
            }

            $lastmod = $url['lastmod'] ? '<lastmod>'.$url['lastmod'].'</lastmod>' : '';

            $alternates = '';

            if (count($versions) > 1) {
                foreach ($versions + ['x-default' => $versions['en']] as $hreflang => $href) {
                    $alternates .= '<xhtml:link rel="alternate" hreflang="'.$hreflang.'" href="'.htmlspecialchars($href, ENT_XML1).'"/>';
                }
            }

            foreach ($versions as $loc) {
                $xml .= '  <url><loc>'.htmlspecialchars($loc, ENT_XML1).'</loc>'.$lastmod.$alternates.'</url>'."\n";
            }
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $private = ['/captcha', '/articles/search-index', '/dashboard', '/settings', '/login', '/register'];
        $lines = ['User-agent: *'];

        foreach (SetLocale::SUPPORTED as $locale) {
            foreach ($private as $path) {
                $lines[] = 'Disallow: '.Seo::localizePath($path, $locale);
            }
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.Seo::origin().'/sitemap.xml';

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * A route's path without the current language prefix.
     */
    protected function path(string $name, mixed $parameters = []): string
    {
        return Seo::unlocalizePath(route($name, $parameters, false));
    }
}
