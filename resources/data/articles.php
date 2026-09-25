<?php

use App\Content\SiteContent;

/**
 * Backwards-compatible entry point that returns all site content
 * (sections, authors, articles, programs) in the old array shape.
 *
 * Articles now live in resources/content/articles/<slug>.md and the rest in
 * resources/data/site.php. Application code should use
 * app(\App\Content\SiteContent::class)->all() instead, which is cached.
 */

return (new SiteContent(__DIR__.'/site.php', dirname(__DIR__).'/content/articles', ''))->build();
