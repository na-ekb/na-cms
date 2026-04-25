<?php namespace NAEkb\VKBot\Models;

use Model;
use October\Rain\Database\Traits\Validation;
/**
 * Stat Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Photo extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_vk_photos';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $fillable = [
        'original_url',
        'file_url'
    ];

    /** @inheritdoc */
    public $timestamps = false;
}
