<?php

namespace Modules\TgBot\Entities;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Model;

class TgTokens extends Model implements Castable
{
    protected $guarded = [
        'id'
    ];

    public static function castUsing(array $arguments)
    {
        dd($arguments);
    }
}
