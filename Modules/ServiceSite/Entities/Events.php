<?php

namespace Modules\ServiceSite\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'date'
    ];
}
