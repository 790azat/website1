<?php

namespace App\Http\Controllers;

use App\Content\SiteContent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * An XML sitemap of every public page, for search engines. Pages with
     * translations (the "?lang=es" and "?lang=fr" URLs) list them too, and
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
            ['loc' => route('home'), 'lastmod' => $latest, 'locales' => SiteContent::TRANSLATION_LOCALES],
            ['loc' => route('articles'), 'lastmod' => $latest, 'locales' => SiteContent::TRANSLATION_LOCALES],
        ];

        foreach (array_keys($sections) as $section) {
            $urls[] = [
                'loc' => route('section', $section),
                'lastmod' => collect($articles)->where('section', $section)->max('date'),
                'locales' => SiteContent::TRANSLATION_LOCALES,
            ];
        }

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => route('article', $article['slug']),
                'lastmod' => $article['date'],
                'locales' => $content->translatedLocales($article['slug']),
            ];
        }

        foreach ($programs as $program) {
            $urls[] = ['loc' => route('program', $program['slug']), 'lastmod' => null, 'locales' => []];
        }

        foreach (['team', 'contact', 'privacy-policy', 'terms-of-use'] as $page) {
            $urls[] = ['loc' => route($page), 'lastmod' => null, 'locales' => SiteContent::TRANSLATION_LOCALES];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $versions = ['en' => $url['loc']];

            foreach ($url['locales'] as $locale) {
                $versions[$locale] = $url['loc'].'?lang='.$locale;
            }

            $lastmod = $url['lastmod'] ? '<lastmod>'.$url['lastmod'].'</lastmod>' : '';

            $alternates = '';

            if (count($versions) > 1) {
                foreach ($versions + ['x-default' => $url['loc']] as $hreflang => $href) {
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
}
