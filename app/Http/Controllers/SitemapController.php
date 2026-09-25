<?php

namespace App\Http\Controllers;

use App\Content\SiteContent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * An XML sitemap of every public page, for search engines.
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
            ['loc' => route('home'), 'lastmod' => $latest],
            ['loc' => route('articles'), 'lastmod' => $latest],
        ];

        foreach (array_keys($sections) as $section) {
            $urls[] = [
                'loc' => route('section', $section),
                'lastmod' => collect($articles)->where('section', $section)->max('date'),
            ];
        }

        foreach ($articles as $article) {
            $urls[] = ['loc' => route('article', $article['slug']), 'lastmod' => $article['date']];
        }

        foreach ($programs as $program) {
            $urls[] = ['loc' => route('program', $program['slug']), 'lastmod' => null];
        }

        foreach (['team', 'contact', 'privacy-policy', 'terms-of-use'] as $page) {
            $urls[] = ['loc' => route($page), 'lastmod' => null];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>'
                .($url['lastmod'] ? '<lastmod>'.$url['lastmod'].'</lastmod>' : '')
                .'</url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
