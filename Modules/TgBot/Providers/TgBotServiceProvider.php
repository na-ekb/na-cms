<?php

namespace Modules\TgBot\Providers;

use Illuminate\Support\ServiceProvider;

use OptimistDigital\NovaSettings\NovaSettings;
use OptimistDigital\NovaSimpleRepeatable\SimpleRepeatable;

use Laravel\Nova\Panel;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Hidden;

use App\Models\Setting;
use Modules\TgBot\Entities\TgTokens;
use Modules\TgBot\Nova\Actions\SetUpWebhook;
use Modules\TgBot\Nova\TgPage;

class TgBotServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'TgBot';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'tgbot';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        config([
            'TgBot' => Setting::where('key', 'like', 'tg_%')
                ->get()
                ->keyBy('key')
                ->transform(function ($setting) {
                    return $setting->value;
                })
                ->toArray()
        ]);

        NovaSettings::addSettingsFields([
            Panel::make(__('tgbot::admin/settings.functions'), [
                Text::make('Status', function () {
                    return view('fields.action-button', [
                        'is_passing' => 1
                    ])->render();
                })->asHtml(),
            ]),
            Panel::make(__('tgbot::admin/settings.primary'), [
                Text::make(__('tgbot::admin/settings.api_key'), 'tg_api_key'),
                Text::make(__('tgbot::admin/settings.webhook_token'), 'tg_webhook_token'),
                Text::make(__('tgbot::admin/settings.default_city'), 'tg_default_city'),
                Text::make(__('tgbot::admin/settings.channel'), 'tg_channel'),
            ]),
            Panel::make('', [
                SimpleRepeatable::make(__('tgbot::admin/settings.tokens'), 'tg_tokens', [
                    Hidden::make('ID', 'id'),
                    Text::make('Токен', 'token'),
                    Text::make('Команда', 'command'),
                    Text::make('Описание', 'description'),
                ])
                ->canAddRows(true)
                ->canDeleteRows(true),
            ]),
        ], [
            'tg_tokens' => TgTokens::class
        ], 'tgModule');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        Nova::resources([
            TgPage::class
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/telegram.php') => config_path('telegram.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/telegram.php'), 'telegram'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            \Telegram\Bot\Laravel\TelegramServiceProvider::class
        ];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
