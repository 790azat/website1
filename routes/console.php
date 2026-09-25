<?php

use App\Content\SiteContent;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('content:cache', function (SiteContent $content) {
    $content->cache();

    $this->components->info('Site content cached successfully.');
})->purpose('Parse the article Markdown files into a single cached PHP file');

Artisan::command('content:clear', function (SiteContent $content) {
    $content->clearCache();

    $this->components->info('Site content cache cleared successfully.');
})->purpose('Remove the cached site content file');
