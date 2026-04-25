<?php namespace NAEkb\Pages\classes;

use Carbon\Carbon;
use Cms\Classes\Theme;
use Illuminate\Contracts\Routing\UrlGenerator;
use October\Rain\Support\Facades\Site;
use Psr\Log\LoggerInterface;
use Throwable;
use Vdlp\Sitemap\Classes\Contracts\DefinitionGenerator;
use Vdlp\Sitemap\Classes\Dto;

final class SitemapGenerator implements DefinitionGenerator
{
    private UrlGenerator $urlGenerator;
    private LoggerInterface $log;

    public function __construct(UrlGenerator $urlGenerator, LoggerInterface $log)
    {
        $this->urlGenerator = $urlGenerator;
        $this->log = $log;
    }

    public function getDefinitions(): Dto\Definitions
    {
        $definitions = new Dto\Definitions();

        if (
            !class_exists('\NAEkb\Pages\Classes\Page')
            || !class_exists('\NAEkb\Pages\Classes\PageList')
        ) {
            return $definitions;
        }

        $site = Site::getActiveSite();

        $pageList = new PageList($site->theme);

        /** @var Page $page */
        foreach ($pageList->listPages() as $page) {
            try {
                if ((bool) $page->getViewBag()->property('is_hidden')) {
                    continue;
                }

                $definitions->addItem(
                    (new Dto\Definition())
                        ->setUrl($this->urlGenerator->to($page->getViewBag()->property('url')))
                        ->setPriority($page->getViewBag()->property('sitemap_priority') ?? 2)
                        ->setChangeFrequency($page->getViewBag()->property('sitemap_priority') ?? Dto\Definition::CHANGE_FREQUENCY_DAILY)
                        ->setModifiedAt(Carbon::createFromTimestamp($page->getAttribute('mtime')))
                );
            } catch (Throwable $e) {
                $this->log->error('Vdlp.SitemapGenerators: Unable to add sitemap definition: ' . $e->getMessage());
            }
        }

        return $definitions;
    }
}
