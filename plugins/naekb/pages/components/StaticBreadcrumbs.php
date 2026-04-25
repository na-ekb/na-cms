<?php namespace NAEkb\Pages\Components;

use Cms\Classes\ComponentBase;
use NAEkb\Pages\Classes\Router;
use NAEkb\Pages\Classes\MenuItemReference;
use NAEkb\Pages\Classes\Page as StaticPageClass;
use Cms\Classes\Theme;
use Request;
use Url;

/**
 * The static breadcrumbs component.
 *
 * @package naekb\pages
 * @author Alexey Bobkov, Samuel Georges, NA Ekb
 */
class StaticBreadcrumbs extends ComponentBase
{
    /**
     * @var array An array of the NAEkb\Pages\Classes\MenuItemReference class.
     */
    public $breadcrumbs = [];

    public function componentDetails()
    {
        return [
            'name'        => 'naekb.pages::lang.component.static_breadcrumbs_name',
            'description' => 'naekb.pages::lang.component.static_breadcrumbs_description'
        ];
    }

    public function onRun()
    {
        $url = $this->getRouter()->getUrl();

        if (!strlen($url)) {
            $url = '/';
        }

        $theme = Theme::getActiveTheme();
        $router = new Router($theme);
        $page = $router->findByUrl($url);

        if ($page) {
            $tree = StaticPageClass::buildMenuTree($theme);

            $code = $startCode = $page->getBaseFileName();
            $breadcrumbs = [];

            while ($code) {
                if (!isset($tree[$code])) {
                    break;
                }

                $pageInfo = $tree[$code];

                if ($pageInfo['navigation_hidden']) {
                    $code = $pageInfo['parent'];
                    continue;
                }

                $reference = new MenuItemReference();
                $reference->title = $pageInfo['title'];
                $reference->url = StaticPageClass::url($code);
                $reference->isActive = $code == $startCode;

                $breadcrumbs[] = $reference;

                $code = $pageInfo['parent'];
            }

            $breadcrumbs = array_reverse($breadcrumbs);

            $this->breadcrumbs = $this->page['breadcrumbs'] = $breadcrumbs;
        }
    }
}
