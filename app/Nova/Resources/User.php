<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;

use Vyuldashev\NovaPermission\PermissionBooleanGroup;
use Vyuldashev\NovaPermission\RoleBooleanGroup;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Nova\Filters\UserRole;

class User extends Resource
{
    use PermissionsBasedAuthTrait;

    /**
     * {@inheritdoc}
     */
    public static $model = \App\Models\User::class;

    /**
     * {@inheritdoc}
     */
    public static $title = 'name';

    /**
     * {@inheritdoc}
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    /**
     * Abilities by permissions
     *
     * @var array
     */
    public static $permissionsForAbilities = [
        'viewAny'           => 'users.view',
        'view'              => 'users.view',
        'create'            => 'users.create',
        'update'            => 'users.update',
        'delete'            => 'users.delete',
        'restore'           => 'users.restore',
        'forceDelete'       => 'users.forceDelete',
        'addAttribute'      => 'users.addAttributes',
        'attachAttribute'   => 'users.attachAttributes',
        'detachAttribute'   => 'users.detachAttributes',
    ];

    /**
     * Custom priority level of the resource.
     *
     * @var int
     */
    public static $priority = 1;

    /**
     * {@inheritdoc}
     */
    public static function group()
    {
        return __('admin/resources/groups.users');
    }

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('admin/resources/user.users');
    }

    /**
     * {@inheritdoc}
     */
    public static function singularLabel()
    {
        return __('admin/resources/user.user');
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            RoleBooleanGroup::make('Roles'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request)
    {
        return [
            new UserRole
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(Request $request)
    {
        return [];
    }
}
