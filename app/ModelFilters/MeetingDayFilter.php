<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

use App\Enums\MeetingDayWeekdaysType;
use App\Models\MeetingDayOneTime;
use App\Models\MeetingDayOneTimeOnline;

class MeetingDayFilter extends ModelFilter
{
    /**
     * Filter by city
     *
     * @param string $city
     * @return MeetingDayFilter
     */
    public function city(string $city) :MeetingDayFilter
    {
        return $this->related('meeting', 'city', 'LIKE', "%{$city}%");
    }

    public function date(string $date) :MeetingDayFilter
    {
        return $this->day($date);
    }
}
