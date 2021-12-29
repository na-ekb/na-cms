<?php

use App\Enums\MeetingsType;
use App\Enums\MeetingDayWeekdaysType;
use App\Enums\Weekdays;
use App\Enums\MeetingDayOnline;

return [

    MeetingsType::class => [
        MeetingsType::Live          => 'Живая',
        MeetingsType::LiveAndStream => 'Живая с трансляцией',
        MeetingsType::OnlyStream    => 'Онлайн'
    ],

    MeetingDayWeekdaysType::class => [
        MeetingDayWeekdaysType::Regular => 'Каждый',
        MeetingDayWeekdaysType::First   => 'Первый',
        MeetingDayWeekdaysType::Second  => 'Второй',
        MeetingDayWeekdaysType::Third   => 'Третий',
        MeetingDayWeekdaysType::Fourth  => 'Четвёртый',
        MeetingDayWeekdaysType::Last    => 'Последний'
    ],

    Weekdays::class => [
        Weekdays::Monday    => 'Понедельник',
        Weekdays::Tuesday   => 'Вторник',
        Weekdays::Wednesday => 'Среда',
        Weekdays::Thursday  => 'Четверг',
        Weekdays::Friday    => 'Пятница',
        Weekdays::Saturday  => 'Суббота',
        Weekdays::Sunday    => 'Воскресенье',
    ],

    MeetingDayOnline::class => [
        MeetingDayOnline::Online        => 'С трансляцией',
        MeetingDayOnline::Offline       => 'Без трансляции',
        MeetingDayOnline::OnlyOnline    => 'Только трансляция',
    ]

];