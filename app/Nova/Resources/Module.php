<?php

namespace App\Nova\Resources;

use Illuminate\Http\Request;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\ActionRequest;

use App\Nova\Concerns\PermissionsBasedAuthTrait;
use App\Nova\Actions\EnableModule;
use App\Nova\Actions\DisableModule;
use App\Models\Module as ModuleModel;

class Module extends Resource
{
    use PermissionsBasedAuthTrait;

    /**
     * {@inheritdoc}
     */
    public static $model = ModuleModel::class;

    /**
     * {@inheritdoc}
     */
    public static $title = 'title';

    /**
     * {@inheritdoc}
     */
    public static $search = [
        'title', 'realname'
    ];

    /**
     * Abilities by permissions
     *
     * @var array
     */
    public static $permissionsForAbilities = [
        'viewAny'           => 'modules.view',
        'view'              => 'modules.view',
        'create'            => 'modules.create',
        'update'            => 'modules.update',
        'delete'            => 'modules.delete',
        'restore'           => 'modules.restore',
        'forceDelete'       => 'modules.forceDelete',
        'addAttribute'      => 'modules.addAttributes',
        'attachAttribute'   => 'modules.attachAttributes',
        'detachAttribute'   => 'modules.detachAttributes',
    ];

    /**
     * {@inheritdoc}
     */
    public static function group()
    {
        return __('admin/resources/groups.other');
    }

    /**
     * {@inheritdoc}
     */
    public static function label()
    {
        return __('admin/resources/module.modules');
    }

    /**
     * {@inheritdoc}
     */
    public static function singularLabel()
    {
        return __('admin/resources/module.module');
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('admin/resources/module.fields.title'), 'title')
                ->translatable()
                ->rules(['max:50'])
                ->size('w-full'),
            Text::make(__('admin/resources/module.fields.realname'), 'realname')
                ->rules(['max:50'])
                ->size('w-full'),
            Trix::make(__('admin/resources/module.fields.description'), 'description')
                ->translatable()
                ->size('w-full'),
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
        return [];
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
        return [
            (new EnableModule)
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }

                    return
                        $this->resource instanceof ModuleModel &&
                        !$this->resource->enabled &&
                        $request->user()->can('modules.toggle');
                }),
            (new DisableModule)
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }

                    return
                        $this->resource instanceof ModuleModel &&
                        $this->resource->enabled &&
                        $request->user()->can('modules.toggle');
                }),
        ];
    }
}
