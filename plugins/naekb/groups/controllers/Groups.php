<?php namespace NaEkb\Groups\Controllers;

use Backend;
use BackendMenu;
use Backend\Classes\Controller;
use Backend\Behaviors\ListController;
use Backend\Behaviors\FormController;
use Backend\Behaviors\RelationController;
use NaEkb\Groups\Models\Group;
use NaEkb\Groups\Models\GroupMeeting;

class Groups extends Controller
{
    public $implement = [
        ListController::class,
        FormController::class,
        RelationController::class
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'naekb.groups.manage_group'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('NaEkb.Groups', 'group', 'group');
    }

    public function relationExtendManageWidget($widget, $field, $model)
    {
        if (!$model instanceof Group || $field !== 'meetings') {
            return;
        }

        $widget->model->bindEvent('model.form.filterFields', function ($formWidget, $fields, $context) use ($model) {
            $group = request()->get('Group');
            $type = $group['type'] ?? $model->type;
            if ($type === 'online') {
                $fields->online->readOnly = true;
                $fields->online->value = 2;
            } elseif ($type !== 'stream') {
                $fields->online->readOnly = true;
                $fields->online->value = 3;
            }
        });
    }
}
