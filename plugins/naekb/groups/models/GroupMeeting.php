<?php namespace NaEkb\Groups\Models;

use DateInterval;
use NaEkb\Groups\Enums\MeetingDayOnline;
use NaEkb\Groups\Enums\MeetingDayWeekdaysType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Model;
use NaEkb\Groups\Enums\MeetingFormats;
use NaEkb\Groups\Enums\Weekdays;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;
use morphos\Russian\TimeSpeller;
/**
 * Model
 */
class GroupMeeting extends Model
{
    use Validation;
    use SoftDelete;

    /**
     * @var array dates to cast from the database.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string table in the database used by the model.
     */
    public $table = 'naekb_group_meetings';

    /**
     * @var array rules for validation.
     */
    public $rules = [
    ];

    public $belongsTo = [
        'group' => [
            Group::class,
            'key' => 'group_id',
            'otherKey' => 'site_root_id', // Костыль мультисайтововсти
        ]
    ];

    public function getDurationStringAttribute()
    {
        $interval = 'PT';
        $hours = floor($this->duration / 60);
        if ($hours > 0) {
            $interval .= "{$hours}H";
        }
        $minutes = $this->duration % 60;
        if ($minutes > 0) {
            $interval .= "{$minutes}M";
        }
        return TimeSpeller::spellInterval(new DateInterval($interval));
    }

    public function getOnlineOptions()
    {
        return MeetingDayOnline::asSelectArray();
    }

    public function getTypeOptions()
    {
        return MeetingDayWeekdaysType::asSelectArray();
    }

    public function getDayOptions()
    {
        return Weekdays::asSelectArray();
    }

    public function getFormatOptions()
    {
        return MeetingFormats::asSelectArrayNames();
    }

    public function getTypeDisplayAttribute()
    {
        $options = MeetingDayWeekdaysType::asSelectArrayLocalized($this->day ?? 1);
        return $options[$this->type];
    }

    public function getFormatNameAttribute()
    {
        $localized = MeetingFormats::getLocalized($this->format);
        return $localized['name'] ?? '';
    }

    public function getFormatDescriptionAttribute()
    {
        $localized = MeetingFormats::getLocalized($this->format);
        return $localized['description'] ?? '';
    }

    public function getTimeArrayAttribute()
    {
        return str_split(Carbon::parse($this->time)->format('Hi'));
    }

    public function scopeCity(Builder $query, $city = null) :Builder
    {
        return $query->whereHas('group', function (Builder $query) use ($city) {
            $query->where('city', $city);
        });
    }

    public function scopeLocation(Builder $query, $location = null) :Builder
    {
        return $query->whereHas('group', function (Builder $query) use ($location) {
            $query->where('location', $location);
        });
    }

    public function scopeDay(Builder $query, $day = null, $forPublic = true) :Builder
    {
        if ($day == null) {
            $day = Carbon::now();
        } elseif (is_string($day)) {
            $day = Carbon::parse($day);
        } elseif (!is_a($day, Carbon::class)) {
            throw new \Exception('Day argument must be string or Carbon instance');
        }

        $meetings = self::where('day', (string) $day->dayOfWeek)
                ->where(function($sSubQ) use ($day) {
                    $sSubQ
                        ->where('type', MeetingDayWeekdaysType::Regular)
                        ->orWhere(function($ssSubQ) use ($day) {
                            $ssSubQ
                                ->where('type', MeetingDayWeekdaysType::First)
                                ->whereRaw("1 = {$day->weekOfMonth}");
                        })->orWhere(function($ssSubQ) use ($day) {
                            $ssSubQ
                                ->where('type', MeetingDayWeekdaysType::Second)
                                ->whereRaw("2 = {$day->weekOfMonth}");
                        })->orWhere(function($ssSubQ) use ($day) {
                            $ssSubQ
                                ->where('type', MeetingDayWeekdaysType::Third)
                                ->whereRaw("3 = {$day->weekOfMonth}");
                        })->orWhere(function($ssSubQ) use ($day) {
                            $ssSubQ
                                ->where('type', MeetingDayWeekdaysType::Fourth)
                                ->whereRaw("4 = {$day->weekOfMonth}");
                        })->orWhere(function($ssSubQ) use ($day) {
                            $lastWeek = (clone $day)->endOfMonth()->weekOfMonth;
                            $ssSubQ
                                ->where('type', MeetingDayWeekdaysType::Last)
                                ->whereRaw("{$lastWeek} = {$day->weekOfMonth}");
                        });
                });
        if ($forPublic) {
            $meetings = $meetings->whereIn('format', [MeetingFormats::Open, MeetingFormats::Closed])->get();
        } else {
            $meetings = $meetings->whereIn('format', [MeetingFormats::Business, MeetingFormats::Committee])->get();
        }

        foreach ($meetings as $meeting) {
            $filtered = $meetings->where('time', $meeting->time)->where('group_id', $meeting->group_id);
            if ($filtered->count() <= 0) {
                continue;
            }

            $filtered = $filtered->sortBy(function ($item) {
                if ($item->day_type == MeetingDayWeekdaysType::Last) {
                    return 1;
                } elseif ($item->day_type != MeetingDayWeekdaysType::Regular) {
                    return 2;
                }
                return 3;
            }, SORT_NUMERIC);
            $filtered->pop();
            $filtered = $filtered->pluck('id')->toArray();
            $meetings = $meetings->reject(function ($item) use ($filtered) {
                return in_array($item->id, $filtered);
            });
        }

        return $query->whereIn('id', $meetings->pluck('id')->toArray());
    }
}
