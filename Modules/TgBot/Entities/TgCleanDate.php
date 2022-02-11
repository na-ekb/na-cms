<?php

namespace Modules\TgBot\Entities;

use Illuminate\Database\Eloquent\Model;

class TgCleanDate extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $dates = ['date'];
}
