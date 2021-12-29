<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;

use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\Action;

class DisableModule extends DestructiveAction
{
    public function __construct() {
        $this->confirmText          = __('admin/resources/module.actions.disable.confirm');
        $this->confirmButtonText    = __('admin/resources/module.actions.ok');
        $this->cancelButtonText     = __('admin/resources/module.actions.cancel');
        $this->runCallback          = function ($request) {
            return $request->user()->can('modules.toggle');
        };
        $this->showOnTableRow       = true;
        $this->showOnIndex          = false;
        $this->showOnDetail         = false;
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $models->each(function ($model) {
            $module = \Module::findOrFail($model->realname);
            if ($module->isEnabled()) {
                $module->disable();
            }
        });
        return Action::message(__('admin/resources/module.actions.disable.success'));
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return __('admin/resources/module.actions.disable.disable');
    }
}
