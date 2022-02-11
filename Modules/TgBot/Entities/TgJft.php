<?php

namespace Modules\TgBot\Entities;

use Illuminate\Database\Eloquent\Model;

class TgJft extends Model
{
    public $table = 'jfts';
    protected $guarded = [
        'id'
    ];
}
