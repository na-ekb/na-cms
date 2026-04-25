<?php namespace NAEkb\Pages;

use Cms\Classes\SnippetManager;
use Event;
use Backend;
use NAEkb\Pages\Models\TurnstileSettings;
use NAEkb\Pages\Classes\Controller;
use NAEkb\Pages\Classes\Page as StaticPage;
use NAEkb\Pages\Classes\Router;
use Cms\Classes\Theme;
use Cms\Classes\Controller as CmsController;
use NAEkb\Pages\classes\SitemapGenerator as NaEkbSitemapGenerator;
use System\Classes\PluginBase;
use Vdlp\Sitemap\Classes\Contracts\SitemapGenerator;

class Plugin extends PluginBase
{
    public $require = ['Vdlp.Sitemap'];


    public function pluginDetails()
    {
        return [
            'name' => 'naekb.pages::lang.plugin.name',
            'description' => 'naekb.pages::lang.plugin.description',
            'author' => 'Alexey Bobkov, Samuel Georges, NA Ekb',
            'icon' => 'ph ph-file-text',
            'homepage' => 'https://github.com/rainlab/pages-plugin'
        ];
    }

    public function registerComponents()
    {
        return [
            \NAEkb\Pages\Components\ChildPages::class => 'childPages',
            \NAEkb\Pages\Components\StaticPage::class => 'staticPage',
            \NAEkb\Pages\Components\StaticMenu::class => 'staticMenu',
            \NAEkb\Pages\Components\StaticBreadcrumbs::class => 'staticBreadcrumbs',
            \NAEkb\Pages\Components\Meetings::class => 'meetings',
            \NAEkb\Pages\Components\Jft::class => 'jft',
            \NAEkb\Pages\Components\Share::class => 'share',
            \NAEkb\Pages\Components\VCard::class => 'vcard',
            \NAEkb\Pages\Components\Feedback::class => 'feedback',
        ];
    }

    public function registerPermissions()
    {
        return [
            'naekb.pages.manage_pages' => [
                'tab'   => 'naekb.pages::lang.page.tab',
                'order' => 200,
                'label' => 'naekb.pages::lang.page.manage_pages'
            ],
            'naekb.pages.manage_menus' => [
                'tab'   => 'naekb.pages::lang.page.tab',
                'order' => 300,
                'label' => 'naekb.pages::lang.page.manage_menus'
            ],
            'naekb.pages.manage_content' => [
                'tab'   => 'naekb.pages::lang.page.tab',
                'order' => 400,
                'label' => 'naekb.pages::lang.page.manage_content'
            ],
            'naekb.groups.view' => [
                'tab'   => 'naekb.pages::lang.groups.tab',
                'order' => 400,
                'label' => 'naekb.pages::lang.groups.view'
            ],
            'naekb.groups.add' => [
                'tab'   => 'naekb.pages::lang.groups.tab',
                'order' => 400,
                'label' => 'naekb.pages::lang.groups.add'
            ],
            'naekb.groups.edit' => [
                'tab'   => 'naekb.pages::lang.groups.tab',
                'order' => 400,
                'label' => 'naekb.pages::lang.groups.edit'
            ],
            'naekb.groups.delete' => [
                'tab'   => 'naekb.pages::lang.groups.tab',
                'order' => 400,
                'label' => 'naekb.pages::lang.groups.delete'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'pages' => [
                'label'       => 'naekb.pages::lang.plugin.name',
                'url'         => Backend::url('naekb/pages'),
                'icon'        => 'ph ph-file-text',
                'permissions' => ['naekb.pages.*'],
                'order'       => 200,
                'useDropdown' => false,

                'sideMenu' => [
                    'pages' => [
                        'label'       => 'naekb.pages::lang.page.menu_label',
                        'icon'        => 'icon-files-o',
                        'url'         => 'javascript:;',
                        'attributes'  => ['data-menu-item' => 'pages'],
                        'permissions' => ['naekb.pages.manage_pages']
                    ],
                    'menus' => [
                        'label'       => 'naekb.pages::lang.menu.menu_label',
                        'icon'        => 'icon-sitemap',
                        'url'         => 'javascript:;',
                        'attributes'  => ['data-menu-item' => 'menus'],
                        'permissions' => ['naekb.pages.manage_menus']
                    ],
                    'content' => [
                        'label'       => 'naekb.pages::lang.content.menu_label',
                        'icon'        => 'icon-file-text-o',
                        'url'         => 'javascript:;',
                        'attributes'  => ['data-menu-item' => 'content'],
                        'permissions' => ['naekb.pages.manage_content']
                    ]
                ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            \NAEkb\Pages\FormWidgets\PagePicker::class => 'staticpagepicker',
            \NAEkb\Pages\FormWidgets\MenuPicker::class => 'staticmenupicker',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            \NAEkb\Pages\Components\Meetings::class => 'meetings',
            \NAEkb\Pages\Components\Jft::class => 'jft',
            \NAEkb\Pages\Components\Share::class => 'share',
            \NAEkb\Pages\Components\VCard::class => 'vcard',
            \NAEkb\Pages\Components\Feedback::class => 'feedback',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'naekb.pages::lang.settings.title',
                'description' => 'naekb.pages::lang.settings.description',
                'category'    => 'naekb.integrations::lang.settings-group',
                'icon'        => 'ph ph-textbox',
                'class'       => TurnstileSettings::class,
                'order'       => 500,
                'keywords'    => 'cloudflare turnstile captcha',
            ]
        ];
    }

    public function boot()
    {
        Event::listen('cms.router.beforeRoute', function($url) {
            return Controller::instance()->initCmsPage($url);
        });

        Event::listen('cms.page.beforeRenderPage', function($controller, $page) {
            // Before twig renders
            $twig = $controller->getTwig();
            $loader = $controller->getLoader();
            Controller::instance()->injectPageTwig($page, $loader, $twig);

            // Get rendered content
            $contents = Controller::instance()->getPageContents($page);
            if (strlen($contents)) {
                return $contents;
            }
        });

        Event::listen('cms.block.render', function($blockName, $blockContents) {
            $page = CmsController::getController()->getPage();

            if (!isset($page->apiBag['staticPage'])) {
                return;
            }

            $contents = Controller::instance()->getPlaceholderContents($page, $blockName, $blockContents);
            if (strlen($contents)) {
                return $contents;
            }
        });

        Event::listen('cms.pageLookup.listTypes', function() {
            return [
                'static-page'      => 'naekb.pages::lang.menuitem.static_page',
                'all-static-pages' => ['naekb.pages::lang.menuitem.all_static_pages', true]
            ];
        });

        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'static-page'      => 'naekb.pages::lang.menuitem.static_page',
                'all-static-pages' => 'naekb.pages::lang.menuitem.all_static_pages'
            ];
        });

        Event::listen(['cms.pageLookup.getTypeInfo', 'pages.menuitem.getTypeInfo'], function($type) {
            if ($type == 'url') {
                return [];
            }

            if ($type == 'static-page'|| $type == 'all-static-pages') {
                return StaticPage::getMenuTypeInfo($type);
            }
        });

        Event::listen(['cms.pageLookup.resolveItem', 'pages.menuitem.resolveItem'], function($type, $item, $url, $theme) {
            if ($type == 'static-page' || $type == 'all-static-pages') {
                return StaticPage::resolveMenuItem($item, $url, $theme);
            }
        });

        Event::listen('cms.template.save', function($controller, $template, $type) {
            Plugin::clearCache();
        });

        Event::listen('cms.template.processTwigContent', function($template, $dataHolder) {
            if ($template instanceof \Cms\Classes\Layout) {
                $dataHolder->content = Controller::instance()->parseSyntaxFields($dataHolder->content);
            }
        });

        Event::listen('backend.richeditor.listTypes', function () {
            return [
                'static-page' => 'naekb.pages::lang.menuitem.static_page',
            ];
        });

        Event::listen('backend.richeditor.getTypeInfo', function ($type) {
            if ($type === 'static-page') {
                return StaticPage::getRichEditorTypeInfo($type);
            }
        });

        Event::listen('system.console.theme.sync.getAvailableModelClasses', function () {
            return [
                \NAEkb\Pages\Classes\Menu::class,
                \NAEkb\Pages\Classes\Page::class,
            ];
        });

        Event::listen(SitemapGenerator::GENERATE_EVENT, static function(): NaEkbSitemapGenerator {
            return resolve(NaEkbSitemapGenerator::class);
        });

        $sitemapGenerator = resolve(SitemapGenerator::class);
        $sitemapGenerator->invalidateCache();
    }

    /**
     * Register new Twig variables
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'staticPage' => [\NAEkb\Pages\Classes\Page::class, 'url']
            ]
        ];
    }

    public static function clearCache()
    {
        $theme = Theme::getEditTheme();

        $router = new Router($theme);
        $router->clearCache();

        StaticPage::clearMenuCache($theme);
        SnippetManager::clearCache($theme);
    }
}
