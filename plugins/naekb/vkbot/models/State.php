<?php namespace NAEkb\VKBot\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * State Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class State extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_vk_states';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $fillable = [
        'user_id',
        'state',
        'action',
        'prev',
        'prev_action',
        'banned',
        'allow'
    ];
}
