<?php

namespace Modules\TgBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use Carbon\Carbon;
use Telegram\Bot\Laravel\Facades\Telegram;

use Modules\TgBot\Helpers\GroupFormatter;
use Modules\TgBot\Entities\TgMeeting;

class SendMeetingsToChannel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!empty(config('TgBot.tg_channel'))) {
            $text = __('tgbot::commands.daily.text') . ' (' . Carbon::now()->format('d.m.Y H:i') . ')' . PHP_EOL . PHP_EOL;
            $day = Carbon::now()->dayOfWeek;

            TgMeeting::where('day', $day)
                ->orderBy('time')
                ->withCasts([
                    'time' => 'datetime',
                    'end_time' => 'datetime'
                ])->each(function($meeting) use (&$text) {
                    $text .= GroupFormatter::format($meeting);
                });

            Telegram::sendMessage([
                'chat_id'                   => config('TgBot.tg_channel'),
                'text'                      => $text,
                'disable_web_page_preview'  => true,
                'parse_mode'                => 'HTML'
            ]);
        }
    }
}
