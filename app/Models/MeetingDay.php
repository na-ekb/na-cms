<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MannikJ\Laravel\SingleTableInheritance\Traits\SingleTableInheritance;
use Spatie\Translatable\HasTranslations;
use EloquentFilter\Filterable;

use App\Enums\MeetingDayWeekdaysType;
use App\Models\MeetingDayRegular;
use App\Models\MeetingDayOneTime;
use App\Models\MeetingDayRegularOnline;
use App\Models\MeetingDayOneTimeOnline;
use App\ModelFilters\MeetingDayFilter;

class MeetingDay extends Model
{
    use Filterable, SingleTableInheritance, HasTranslations;

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

    /**
     * {@inheritdoc}
     */
    public $dates = [
        'date'
    ];

    /**
     * Filter class for model
     *
     * @return string|null
     */
    public function modelFilter()
    {
        return $this->provideFilter(MeetingDayFilter::class);
    }

    /**
     * Meeting
     *
     * @return BelongsTo
     */
    public function meeting() {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Format of day
     *
     * @return BelongsTo
     */
    public function meetingDayFormat()
    {
        return $this->belongsTo(
            MeetingDayFormat::class,
            'format',
            'id'
        );
    }

    /**
     * Filter by weekday
     *
     * @param Builder $query
     * @param $day
     *
     * @return Builder
     *
     * @throws \Exception
     */
    public function scopeDay(Builder $query, $day = null, $forPublic = true) :Builder
    {
        if (!$forPublic && !auth()->check()) {
            abort(403);
        }

        if ($day == null) {
            $day = Carbon::now();
        } elseif (is_string($day)) {
            $day = Carbon::parse($day);
        } elseif (!is_a($day, Carbon::class)) {
            throw new \Exception('Day argument must be string or Carbon instance');
        }

        $meetings = self::where(function($subQ) use ($day) {
                $subQ
                    ->where('day', (string) $day->dayOfWeek)
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
            })->get();

        if ($forPublic) {
            $meetings = $meetings->whereIn('format', [1, 2]);
        } else {
            $meetings = $meetings->whereIn('format', [3, 4]);
        }

        foreach ($meetings as $meeting) {
            $filtered = $meetings->where('time', $meeting->time)->where('meeting_id', $meeting->meeting_id);
            if (count($filtered) > 1) {
                $filtered = $filtered->sortBy(function ($item) {
                    if (in_array($item->type, [
                        Str::kebab(class_basename(MeetingDayOneTime::class)),
                        Str::kebab(class_basename(MeetingDayOneTimeOnline::class)),
                    ])) {
                        return 0;
                    } elseif ($item->day_type == MeetingDayWeekdaysType::Last) {
                        return 1;
                    } elseif ($item->day_type != MeetingDayWeekdaysType::Regular) {
                        return 2;
                    }
                    return 3;
                });
                $filtered->shift();
                $filtered = $filtered->pluck('id')->toArray();
                $meetings = $meetings->reject(function ($item) use ($filtered) {
                    return in_array($item->id, $filtered);
                });
            }
        }

        return $query->whereIn('id', $meetings->pluck('id')->toArray());
    }
}
