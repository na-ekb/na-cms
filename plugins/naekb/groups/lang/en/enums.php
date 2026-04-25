<?php

use NaEkb\Groups\Enums\MeetingFormats;
use NaEkb\Groups\Enums\Weekdays;
use NaEkb\Groups\Enums\MeetingDayWeekdaysType;

return [
    MeetingFormats::class => [
        MeetingFormats::Open => 'Open',
        MeetingFormats::Closed => 'Closed',
        MeetingFormats::Business => 'Business',
        MeetingFormats::Committee => 'Committee',
    ],
    Weekdays::class => [
        Weekdays::Monday => 'Monday',
        Weekdays::Tuesday => 'Tuesday',
        Weekdays::Wednesday => 'Wednesday',
        Weekdays::Thursday => 'Thursday',
        Weekdays::Friday => 'Friday',
        Weekdays::Saturday => 'Saturday',
        Weekdays::Sunday => 'Sunday'
    ],
];
