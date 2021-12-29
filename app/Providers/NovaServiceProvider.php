<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Panel;
use OptimistDigital\MultiselectField\Multiselect;
use OptimistDigital\NovaSettings\NovaSettings;
use KABBOUCHI\LogsTool\LogsTool;
use CodencoDev\NovaGridSystem\NovaGridSystem;
use Vyuldashev\NovaPermission\NovaPermissionTool;

use App\Models\Language;
use App\Nova\Resources\Role;
use App\Nova\Resources\Permission;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        NovaSettings::addSettingsFields([
            Panel::make(__('admin/resources/settings.languages'), [
                Multiselect::make(__('admin/resources/settings.languages'), 'languages')
                    ->options(Language::get()->pluck('native_name', 'code'))
                    ->reorderable()
                    ->help(__('admin/resources/settings.languages_help')),
            ]),
        ], [
           'languages' => 'array'
        ]);

        Nova::serving(function () {
            $locales = [];
            foreach (nova_get_setting('languages') as $language) {
                $language = Language::where('code', $language)->first();
                $locales[$language->code] = $language->native_name;
            }

            config([
                'nova-translatable' => array_merge(config('nova-translatable'), [
                    'locales' => $locales
                ])
            ]);


            Nova::translations(resource_path('lang/ru/admin/nova.json'));


            Nova::sortResourcesBy(function ($resource) {
                return $resource::$priority ?? 99999;
            });

            config([
                'nova-group-order' => array_merge(
                    config('nova-group-order') ?? [],
                    [
                        __('admin/resources/groups.users') => 9000,
                        __('admin/resources/groups.other') => 9001,
                    ]
                )
            ]);
        });

        Nova::userTimezone(function (Request $request) {
            return config('app.timezone');
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('nova.view', function ($user) {
            return $user->can('nova.view');
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            (new LogsTool())->canSee(function ($request) {
                return $request->user()->can('logs.view');
            })->canDownload(function ($request) {
                return $request->user()->can('logs.view');
            })->canDelete(function ($request) {
                return $request->user()->can('logs.delete');
            }),
            new NovaSettings,
            new NovaGridSystem,
            NovaPermissionTool::make()
                ->roleResource(Role::class)
                ->permissionResource(Permission::class),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Nova::style('admin', asset('css/admin.css'));
    }

    /**
     * Register the application's Nova resources.
     *
     * @return void
     */
    protected function resources()
    {
        Nova::resourcesIn(app_path('Nova/Resources'));
    }
}
