<?php namespace NaEkb\Groups\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Multisite;

/**
 * Model
 */
class Group extends Model
{
    use Validation;
    use SoftDelete;
    use Multisite;

    protected $propagatable = [
        'slug',
        'type',
        'lat',
        'lon',
        'link',
        'password',
        'meetings',
        'changes'
    ];

    protected $propagatableSync = true;

    /**
     * @var array dates to cast from the database.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string table in the database used by the model.
     */
    public $table = 'naekb_groups';

    /**
     * @var array rules for validation.
     */
    public $rules = [
        'title' => 'required'
    ];

    /**
     * @var array hasMany
     */
    public $hasMany = [
        'meetings' => [
            GroupMeeting::class,
            'key' => 'group_id',
            'other_key' => 'id',
            'delete' => true,
            'softDelete' => true
        ],
        'changes' => [
            GroupChange::class,
            'key' => 'group_id',
            'other_key' => 'id',
            'delete' => true,
            'softDelete' => true
        ]
    ];

    public function getTypeOptions()
    {
        return [
            'live' => 'Живая',
            'online' => 'Онлайн',
            'stream' => 'Живая с трансляцией'
        ];
    }
}
