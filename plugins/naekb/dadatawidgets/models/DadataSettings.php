<?php namespace NAEkb\DadataWidgets\Models;

use Model;
use October\Rain\Database\Traits\Multisite;
use System\Behaviors\SettingsModel;

/**
 * Settings Model
 */
class DadataSettings extends Model
{
    use Multisite;

    public $implement = [
        SettingsModel::class,
    ];

    public $settingsCode = 'na_ekb_dadatawidgets_settings';
    public $settingsFields = 'fields.yaml';
    protected $propagatable = [];
}
