<?php

namespace App\Models;

use App\Enums\MeetingDayWeekdaysType;
use App\Models\Meeting as MeetingModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Spatie\Translatable\HasTranslations;

class Meeting extends Model
{
    use HasTranslations;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'title',
        'city',
        'location',
        'type',
        'address',
        'address_description',
        'lat',
        'long',
        'link',
        'link_text',
        'online',
        'password',
        'description'
    ];

    /**
     * The attributes that are translatable
     *
     * @var string[]
     */
    public $translatable = [
        'title',
        'city',
        'location',
        'address',
        'address_description',
        'link_text',
        'online',
        'description'
    ];

    public function meetingDays() {
        return $this->hasMany(MeetingDay::class);
    }

    public function meetingDayRegular() {
        return $this->meetingDays()
            ->where(function (Builder $query) {
                return $query->where('type', MeetingDayRegular::class);
            });
    }

    public function meetingDayOneTime() {
        return $this->meetingDays()
            ->where(function (Builder $query) {
                return $query->where('type', MeetingDayOneTime::class);
            });
    }

    public function MeetingDayRegularOnline() {
        return $this->meetingDays()
            ->where(function (Builder $query) {
                return $query->where('type', MeetingDayRegularOnline::class);
            });
    }

    public function MeetingDayOneTimeOnline() {
        return $this->meetingDays()
            ->where(function (Builder $query) {
                return $query->where('type', MeetingDayOneTimeOnline::class);
            });
    }

    /**
     * @param Builder $query
     * @param null|Carbon|string $day
     * @return Builder
     * @throws \Exception
     */
    public function scopeDay(Builder $query, $day = null) :Builder
    {
        if ($day == null) {
            $day = Carbon::now();
        } elseif (is_string($day)) {
            $day = Carbon::parse($day);
        } elseif (!is_a($day, Carbon::class)) {
            throw new \Exception('Day argument must be string or Carbon instance');
        }

        return $query->whereHas('meetingDays', function($subQ) use ($day) {
            return $subQ->day($day);
        });
    }

    /**
     * Get all unique attributes for search fields
     *
     * @param string $attribute
     * @return array
     */
    public static function getAllUnique(string $attribute) :array
    {
        $values = [];
        static::all()
            ->pluck($attribute)
            ->each(function ($value) use (&$values) {
                if (!empty($value) && !in_array($value, $values)) {
                    $values[] = $value;
                }
            });
        return $values;
    }
}
