<?php namespace NaEkb\TgBot\Models;

use Model;

use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Sortable;

/**
 * Page Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class PageLink extends Model
{
    use Validation, Sortable;

    const SORT_ORDER = 'order';

    /**
     * @var string table name
     */
    public $table = 'na_ekb_tg_page_links';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * @var array
     */
    protected $propagatable = [];

    public $timestamps = false;

    /**
     * Belongs to  relationships
     *
     * @var string[][]
     */
    public $belongsTo = [
        'page' => [
            Page::class
        ]
    ];
}
