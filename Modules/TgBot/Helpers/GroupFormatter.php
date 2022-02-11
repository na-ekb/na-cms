<?php

namespace Modules\TgBot\Helpers;

use Modules\TgBot\Entities\TgMeeting;

Use Carbon\Carbon;
class GroupFormatter {

    public static function format(TgMeeting $meeting) :string {
        $text = '';
        $text .= "• <a href=\"{$meeting->url}\">{$meeting->name}</a>";
        $text .= " c {$meeting->time->format('H:i')} до {$meeting->end_time->format('H:i')}" . PHP_EOL;

        if (mb_strtolower($meeting->location) != 'онлайн') {
            $address = explode(',', $meeting->formatted_address);
            if ($meeting->region !== config('TgBot.tg_default_city')) {
                $text .= trim($address[2]) . ', ' . array_shift($address) . array_shift($address);
            } else {
                $text .= array_shift($address) . array_shift($address);
            }

            if (!empty($meeting->location_notes)) {
                $text .= PHP_EOL . $meeting->location_notes;
            }
            $text .= PHP_EOL;
        } else {
            if (!empty($meeting->conference_url)) {
                $text .= $meeting->conference_url . PHP_EOL;
            }
            if (!empty($meeting->conference_url_notes)) {
                $text .= $meeting->conference_url_notes . PHP_EOL;
            }
            if (!empty($meeting->conference_phone)) {
                $text .= $meeting->conference_phone . PHP_EOL;
            }
            if (!empty($meeting->conference_phone_notes)) {
                $text .= $meeting->conference_phone_notes . PHP_EOL;
            }
        }

        return $text . PHP_EOL;
    }
}