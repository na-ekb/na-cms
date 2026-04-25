<?php namespace NAEkb\Pages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Support\Facades\Site;

class Jft extends Model
{
    use Validation;

    /** @inheritdoc */
    public $table = 'na_ekb_jfts';

    /** @inheritdoc */
    public $rules = [];

    /** @inheritdoc */
    protected $fillable = [];

    /**
     * Scope for get today JFT
     *
     * @param  Builder $query
     * @return void
     */
    public static function scopeToday(Builder $query) :void {
        $site = Site::getActiveSite();
        $today = Carbon::now()->setTimezone($site->timezone);
        $query->whereMonth('created_at', $today->month)->whereDay('created_at', $today->day);
    }
}
