<?php

namespace App\Http\Controllers;

use App\Content\SiteContent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * An XML sitemap of every public page, for search engines. Pages with a
     * Spanish version (the "?lang=es" URL) list it too, and both versions
     * name each other as hreflang alternates.
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
            ['loc' => route('home'), 'lastmod' => $latest, 'es' => true],
            ['loc' => route('articles'), 'lastmod' => $latest, 'es' => true],
        ];

        foreach (array_keys($sections) as $section) {
            $urls[] = [
                'loc' => route('section', $section),
                'lastmod' => collect($articles)->where('section', $section)->max('date'),
                'es' => true,
            ];
        }

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => route('article', $article['slug']),
                'lastmod' => $article['date'],
                'es' => $content->hasTranslation($article['slug'], 'es'),
            ];
        }

        foreach ($programs as $program) {
            $urls[] = ['loc' => route('program', $program['slug']), 'lastmod' => null, 'es' => false];
        }

        foreach (['team', 'contact', 'privacy-policy', 'terms-of-use'] as $page) {
            $urls[] = ['loc' => route($page), 'lastmod' => null, 'es' => true];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";

        foreach ($urls as $url) {
            $versions = $url['es'] ? ['en' => $url['loc'], 'es' => $url['loc'].'?lang=es'] : ['en' => $url['loc']];
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
