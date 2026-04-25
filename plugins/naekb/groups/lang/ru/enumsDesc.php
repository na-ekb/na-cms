<?php

use NaEkb\Groups\Enums\MeetingFormats;

return [
    MeetingFormats::class => [
        MeetingFormats::Open => 'Открытое собрание, которое могут посещать люди, не страдающие от химической зависимости',
        MeetingFormats::Closed => 'Закрытые собрания предназначены только для зависимых или для тех, кто считает, что у них могут быть проблемы с наркотиками',
        MeetingFormats::Business => 'Цель рабочего собрания – дать членам группы возможность обсудить важные для группы темы.',
        MeetingFormats::Committee => '(Под)комитет',
    ]
];
