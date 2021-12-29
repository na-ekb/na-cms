<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use MannikJ\Laravel\SingleTableInheritance\Traits\SingleTableInheritance;
use Spatie\Translatable\HasTranslations;

use App\Enums\MeetingDayWeekdaysType;
use App\Models\MeetingDayRegular;
use App\Models\MeetingDayOneTime;
use App\Models\MeetingDayRegularOnline;
use App\Models\MeetingDayOneTimeOnline;

class MeetingDay extends Model
{
    use SingleTableInheritance, HasTranslations;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'meeting_id',
        'type',
        'format',
        'format_second',
        'day_type',
        'day',
        'date',
        'time',
        'duration'
    ];

    /**
     * The attributes that are translatable
     *
     * @var string[]
     */
    public $translatable = [
        'format_second'
    ];

    public $dates = [
        'date'
    ];

    public function meeting() {
        return $this->belongsTo(Meeting::class);
    }

    public function meetingDayFormat()
    {
        return $this->belongsTo(
            MeetingDayFormat::class,
            'format',
            'id'
        );
    }

    public function scopeDay(Builder $query, $day = null) :Builder
    {
        if ($day == null) {
            $day = Carbon::now();
        } elseif (is_string($day)) {
            $day = Carbon::parse($day);
        } elseif (!is_a($day, Carbon::class)) {
            throw new \Exception('Day argument must be string or Carbon instance');
        }

        return
            $query->where(function($subQ) use ($day) {
                $subQ
                    ->where('day', $day->dayOfWeek)
                    ->whereIn('type', [MeetingDayRegular::class, MeetingDayRegularOnline::class])
                    ->where(function($sSubQ) use ($day) {
                        $sSubQ
                            ->where('day_type', MeetingDayWeekdaysType::Regular)
                            ->orWhere(function($ssSubQ) use ($day) {
                                $ssSubQ
                                    ->where('day_type', MeetingDayWeekdaysType::First)
                                    ->whereRaw("1 = {$day->weekOfMonth}");
                            })->orWhere(function($ssSubQ) use ($day) {
                                $ssSubQ
                                    ->where('day_type', MeetingDayWeekdaysType::Second)
                                    ->whereRaw("2 = {$day->weekOfMonth}");
                            })->orWhere(function($ssSubQ) use ($day) {
                                $ssSubQ
                                    ->where('day_type', MeetingDayWeekdaysType::Third)
                                    ->whereRaw("3 = {$day->weekOfMonth}");
                            })->orWhere(function($ssSubQ) use ($day) {
                                $ssSubQ
                                    ->where('day_type', MeetingDayWeekdaysType::Fourth)
                                    ->whereRaw("4 = {$day->weekOfMonth}");
                            })->orWhere(function($ssSubQ) use ($day) {
                                $lastWeek = (clone $day)->endOfMonth()->weekOfMonth;
                                $ssSubQ
                                    ->where('day_type', MeetingDayWeekdaysType::Last)
                                    ->whereRaw("$lastWeek = $day->weekOfMonth");
                            });
                    });
            })->orWhere(function($subQ) use ($day) {
                $subQ
                    ->whereIn('type', [MeetingDayOneTime::class, MeetingDayOneTimeOnline::class])
                    ->where('date', $day->startOfDay()->toDateTimeString());
            });
    }
}
