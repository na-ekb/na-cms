<?php namespace NAEkb\Pages\Classes;

use Illuminate\Contracts\Config\Repository;
use October\Rain\Support\Facades\Site;
use Vdlp\Sitemap\Classes\Contracts\ConfigResolver;
use Vdlp\Sitemap\Classes\Dto\SitemapConfig;

final class MultisiteConfigResolver implements ConfigResolver
{
    public function __construct(private Repository $config)
    {
    }

    public function getConfig(): SitemapConfig
    {
        $site = Site::getActiveSite();
        return new SitemapConfig(
            'vdlp_sitemap_cache_' . $site->code,
            'vdlp_sitemap_definitions_' . $site->code,
            sprintf('vdlp/sitemap/sitemap_%s.xml', $site->code),
            (int) $this->config->get('sitemap.cache_time', 3600),
            (bool) $this->config->get('sitemap.cache_forever', false)
        );
    }
}
