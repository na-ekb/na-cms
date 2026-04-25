<?php namespace NAEkb\Integrations\Models;

use System\Models\SettingModel;
use October\Rain\Database\Traits\Multisite;

/**
 * VkSettings Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class IntegrationsSettings extends SettingModel
{
    use Multisite;

    public $settingsCode = 'na_ekb_shared_settings';
    public $settingsFields = 'fields.yaml';
    protected $propagatable = [];
}
