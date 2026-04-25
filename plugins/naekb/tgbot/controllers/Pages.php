<?php namespace NAEkb\TgBot\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;

/**
 * Pages Backend Controller
 *
 * @link https://docs.octobercms.com/3.x/extend/system/controllers.html
 */
class Pages extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        RelationController::class
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string relationConfig file
     */
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['naekb.tgbot.pages'];


    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('NAEkb.TgBot', 'bot', 'pages');
    }
}
