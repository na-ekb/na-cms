<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MeetingDayFormat extends Model
{
    use HasTranslations;

    /**
     * The attributes that are translatable
     *
     * @var string[]
     */
    public $translatable = [
        'title',
        'description'
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'title'
    ];

    public function meetingDays()
    {
        return $this->belongsToMany(
            MeetingDay::class,
            'meeting_days_formats',
            'id',
            'meeting_day_format_id'
        );
    }
}
