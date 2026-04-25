<?php namespace NAEkb\TgBot\models;

use System\Models\SettingModel;
use October\Rain\Database\Traits\Multisite;

/**
 * VkSettings Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class TgSettings extends SettingModel
{
    use Multisite;

    public $settingsCode = 'na_ekb_tg_settings';
    public $settingsFields = 'fields.yaml';
    protected $propagatable = [];
}
