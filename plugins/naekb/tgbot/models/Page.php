<?php namespace NaEkb\TgBot\Models;

use Model;

use October\Rain\Database\Traits\Multisite;
use October\Rain\Database\Traits\Validation;

/**
 * Page Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Page extends Model
{
    use Validation, Multisite;

    /**
     * @var string table name
     */
    public $table = 'na_ekb_tg_pages';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * @var array
     */
    protected $propagatable = [];

    /**
     * Belongs to many relationships
     *
     * @var \string[][]
     */
    public $belongsToMany = [
        'childrens' => [
            self::class,
            'table' => 'na_ekb_tg_page_relations',
            'key' => 'parent_id',
            'otherKey' => 'children_id'
        ],
        'parents' => [
            self::class,
            'table' => 'na_ekb_tg_page_relations',
            'key' => 'children_id',
            'otherKey' => 'parent_id'
        ]
    ];

    public $hasMany = [
        'links' => [
            PageLink::class
        ]
    ];
}
