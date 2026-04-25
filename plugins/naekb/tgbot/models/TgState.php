<?php namespace NaEkb\TgBot\Models;

use Model;

use October\Rain\Database\Traits\Validation;

/**
 * Page Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class TgState extends Model
{
    use Validation;

    /**
     * @var string table name
     */
    public $table = 'na_ekb_tg_states';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $fillable = [
        'user_id',
        'state',
        'prev'
    ];
}
