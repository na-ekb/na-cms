<?php namespace NAEkb\VKBot\Models;

use Model;
use October\Rain\Database\Traits\Validation;
/**
 * Stat Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Stat extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_vk_stats';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $fillable = [
        'type',
        'year',
        'month',
        'count'
    ];

    /** @inheritdoc */
    public $timestamps = false;
}
