<?php

use NaEkb\Groups\Enums\MeetingDayOnline;
use NaEkb\Groups\Enums\MeetingDayWeekdaysType;
use NaEkb\Groups\Enums\MeetingFormats;
use NaEkb\Groups\Enums\Weekdays;

return [
    MeetingFormats::class => [
        MeetingFormats::Open => 'Открытое',
        MeetingFormats::Closed => 'Закрытое',
        MeetingFormats::Business => 'Рабочее',
        MeetingFormats::Committee => '(Под)комитет',
    ],
    Weekdays::class => [
        Weekdays::Monday => 'Понедельник',
        Weekdays::Tuesday => 'Вторник',
        Weekdays::Wednesday => 'Среда',
        Weekdays::Thursday => 'Четверг',
        Weekdays::Friday => 'Пятница',
        Weekdays::Saturday => 'Суббота',
        Weekdays::Sunday => 'Воскресенье'
    ],
    MeetingDayWeekdaysType::class => [
        MeetingDayWeekdaysType::First => 'Первый',
        MeetingDayWeekdaysType::Second => 'Второй',
        MeetingDayWeekdaysType::Third => 'Третий',
        MeetingDayWeekdaysType::Fourth => 'Четвёртый',
        MeetingDayWeekdaysType::Last => 'Последний',
        MeetingDayWeekdaysType::Regular => 'Каждый',
    ],
    MeetingDayWeekdaysType::class . '_display' => [
        MeetingDayWeekdaysType::First => '{0}Первый :weekday|{1}Первая :weekday|{2}Первое :weekday',
        MeetingDayWeekdaysType::Second => '{0}Второй :weekday|{1}Вторая :weekday|{2}Второе :weekday',
        MeetingDayWeekdaysType::Third => '{0}Третий :weekday|{1}Третья :weekday|{2}Третье :weekday',
        MeetingDayWeekdaysType::Fourth => '{0}Четвёртый :weekday|{1}Четвёртая :weekday|{2}Четвёртое :weekday',
        MeetingDayWeekdaysType::Last => '{0}Последний :weekday|{1}Последняя :weekday|{2}Последнее :weekday',
        MeetingDayWeekdaysType::Regular => '{0}Каждый :weekday|{1}Каждая :weekday|{2}Каждое :weekday',
    ],
    MeetingDayOnline::class => [
        MeetingDayOnline::Online => 'Живая с трансляцией',
        MeetingDayOnline::OnlyOnline => 'Только трансляция',
        MeetingDayOnline::Offline => 'Без трансляции',
    ],


];
