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
        \App::error(function(\Throwable $exception) {
            $controller = new Controller(Theme::getActiveTheme());
            //$controller->setStatusCode($exception->getStatusCode());
            $handler = resolve(Handler::class);
            $handler->report($exception);

            return $controller->run('/error');
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
}
