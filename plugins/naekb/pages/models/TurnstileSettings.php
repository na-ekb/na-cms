<?php namespace NAEkb\Pages\Models;

use Model;
use October\Rain\Database\Traits\Multisite;
use System\Behaviors\SettingsModel;

/**
 * Settings Model
 */
class TurnstileSettings extends Model
{
    use Multisite;

    public $implement = [
        SettingsModel::class,
    ];

    public $settingsCode = 'na_ekb_turnstile_settings';
    public $settingsFields = 'fields.yaml';
    protected $propagatable = [];
}
