<?php namespace NAEkb\VKBot\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * CleanDate Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class CleanDate extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_vk_clean_dates';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'date',
        'last'
    ];

    /** @inheritdoc */
    protected $dates = [
        'date',
        'last'
    ];
}
