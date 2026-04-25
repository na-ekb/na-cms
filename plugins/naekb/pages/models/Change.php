<?php namespace NAEkb\Pages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Support\Facades\Site;

class Change extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_group_changes';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $guarded = ['id'];
}
