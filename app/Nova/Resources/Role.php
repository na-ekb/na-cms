<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Spatie\Permission\PermissionRegistrar;

use App\Nova\Fields\PermissionBooleanGroup;

class Role extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Spatie\Permission\Models\Role::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Custom priority level of the resource.
     *
     * @var int
     */
    public static $priority = 2;

    public static function getModel()
    {
        return app(PermissionRegistrar::class)->getRoleClass();
    }

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return __('admin/resources/groups.users');
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param Request $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Gate::allows('viewAny', app(PermissionRegistrar::class)->getRoleClass());
    }

    public static function label()
    {
        return __('nova-permission-tool::resources.Roles');
    }

    public static function singularLabel()
    {
        return __('nova-permission-tool::resources.Role');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        $fields = [
            ID::make()->sortable(),

            Text::make(__('admin/permissions/roles.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:'.config('permission.table_names.roles'))
                ->updateRules('unique:'.config('permission.table_names.roles').',name,{{resourceId}}')
                ->size('w-1/2'),

            Select::make(__('admin/permissions/roles.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)])
                ->size('w-1/2'),

            PermissionBooleanGroup::make(
                __('admin/permissions/roles.permissions-users'),
                'permissions.users'
            )->filter('users')->onlyOnForms()->size('w-1/2'),

            PermissionBooleanGroup::make(
                __('admin/permissions/roles.permissions-modules'),
                'permissions.modules'
            )->filter('modules')->onlyOnForms()->size('w-1/2'),

            PermissionBooleanGroup::make(
                __('admin/permissions/roles.permissions-meeting'),
                'permissions.meeting',
            )->filter('meeting')->onlyOnForms()->size('w-1/2'),

            PermissionBooleanGroup::make(
                __('admin/permissions/roles.permissions-logs'),
                'permissions.logs',
            )->filter('logs')->onlyOnForms()->size('w-1/2'),

            PermissionBooleanGroup::make(
                __('admin/permissions/roles.permissions-nova'),
                'permissions.nova',
            )->filter('nova')->onlyOnForms()->size('w-1/2'),

            PermissionBooleanGroup::make(__('admin/permissions/roles.permissions'), 'permissions')
                ->exceptOnForms(),

            MorphToMany::make($userResource::label(), 'users', $userResource)
                ->searchable()
                ->singularLabel($userResource::singularLabel()),

            DateTime::make(__('admin/permissions/roles.created_at'), 'created_at')
                ->exceptOnForms(),
            DateTime::make(__('admin/permissions/roles.updated_at'), 'updated_at')
                ->exceptOnForms(),
        ];

        return $fields;
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
