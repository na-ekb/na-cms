<?php

namespace Modules\TgBot\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TgPage extends Model
{
    use HasTranslations;

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that are translatable
     *
     * @var string[]
     */
    public $translatable = [
        'title', 'content'
    ];

    public function childrens()
    {
        return $this->belongsToMany(self::class, 'tg_page_relations', 'parent_id', 'children_id');
    }

    public function parents()
    {
        return $this->belongsToMany(self::class, 'tg_page_relations', 'children_id', 'parent_id');
    }
}
