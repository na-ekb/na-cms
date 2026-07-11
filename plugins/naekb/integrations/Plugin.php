<?php namespace NAEkb\Integrations;

use System\Classes\PluginBase;

use NAEkb\Integrations\Models\IntegrationsSettings;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Cms\Classes\Controller;
use Cms\Classes\Theme;
use October\Rain\Foundation\Exception\Handler;

class Plugin extends PluginBase
{
    public function boot()
    {
        \Event::listen('backend.page.beforeDisplay', function ($controller) {
            $controller->addJs('/plugins/naekb/integrations/assets/js/metrika-redactor.js');
        });

        \App::error(function(\Throwable $exception) {
            // Сообщаем об исходном исключении в первую очередь, чтобы оно не потерялось,
            // если кастомный рендер страницы ошибки сам упадёт.
            resolve(Handler::class)->report($exception);

            // Кастомную CMS-страницу /error рендерим только при наличии активной темы.
            // Без темы (не выбрана/не найдена, либо не-CMS запрос вроде вебхука)
            // new Controller(null) бросает CmsException и маскирует реальную ошибку —
            // в этом случае отдаём стандартную обработку исключения фреймворком.
            $theme = Theme::getActiveTheme();
            if (empty($theme)) {
                return null;
            }

            try {
                return (new Controller($theme))->run('/error');
            } catch (\Throwable $e) {
                resolve(Handler::class)->report($e);
                return null;
            }
        });
    }

    /** @inheritdoc */
    public function pluginDetails()
    {
        return [
            'name' => 'naekb.integrations::lang.title',
            'description' => 'naekb.integrations::lang.description',
            'author' => 'NA Ekb',
            'icon' => 'ph ph-puzzle-piece'
        ];
    }

    /** @inheritdoc */
    public function registerPermissions()
    {
        return [
            'naekb.integrations.settings' => [
                'tab' => 'naekb.integrations::lang.title',
                'label' => 'naekb.integrations::lang.permissions.settings'
            ],
        ];
    }

    /** @inheritdoc */
    public function registerSettings()
    {
        return [
            'integrations' => [
                'label'         => 'naekb.integrations::lang.title',
                'description'   => 'naekb.integrations::lang.description',
                'category'      => 'naekb.integrations::lang.settings-group',
                'icon'          => 'ph ph-puzzle-piece',
                'class'         => IntegrationsSettings::class,
                'order'         => 0,
                'keywords'      => 'Integrations',
                'permissions'   => ['naekb.integrations.settings'],
            ]
        ];
    }

    /**
     * registerMarkupTags — доступ к общим настройкам из любого Twig-шаблона
     * (в т.ч. partial, куда переменные layout не прокидываются).
     */
    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'integrationsSetting' => function ($key, $default = null) {
                    return IntegrationsSettings::get($key, $default);
                },
            ],
        ];
    }

    /** @inheritdoc */
    public function registerReportWidgets()
    {
        return [
            \NAEkb\Integrations\ReportWidgets\DeployVersion::class => [
                'label'   => 'naekb.integrations::lang.deploy_version.label',
                'context' => 'dashboard',
            ],
        ];
    }
}
