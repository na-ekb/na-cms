<?php

use NaEkb\Groups\Enums\MeetingDayOnline;
use NaEkb\Groups\Enums\MeetingDayWeekdaysType;
use NaEkb\Groups\Enums\MeetingFormats;
use NaEkb\Groups\Enums\Weekdays;

return [
    MeetingFormats::class => [
        MeetingFormats::Open => 'Open meetings are open to the public, while closed meetings are for members only.',
        MeetingFormats::Closed => 'Closed meetings are for members only, while open meetings are open to the public.',
        MeetingFormats::Business => 'Group business meetings (sometimes called group conscience meetings) allow groups to discuss business in a way that keeps the recovery meeting focused on effectively carrying the NA message.',
        MeetingFormats::Committee => '(Под)комитет',
    ]
];
