<?php

namespace Modules\TgBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

use Modules\TgBot\Entities\TgCleanDate;


class CleanTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keyboard = [
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.yes'),
                    'callback_data' => 'cleanTime  congr'
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.no'),
                    'callback_data' => 'cleanTime  empathy'
                ])
            ]
        ];
        if (!empty(config('TgBot.tg_channel'))) {
            $keyboard[] = [
                Keyboard::button([
                    'text'          => __('tgbot::commands.clean_time.private'),
                    'callback_data' => 'cleanTime  congrPriv'
                ])
            ];
        }

        TgCleanDate::whereRaw('DAYOFMONTH(`date`) = DAYOFMONTH(NOW()) AND TIMESTAMPDIFF(MONTH, `date` - INTERVAL 1 DAY, NOW()) > 0')
            ->each(function($cleanDate) use ($keyboard) {
                if (!$cleanDate->updated_at->isToday()) {
                    Telegram::sendMessage([
                        'chat_id'                   => $cleanDate->tg_user_id,
                        'text'                      => __('tgbot::commands.clean_time.schedule'),
                        'reply_markup'              => new Keyboard([
                            'inline_keyboard' => $keyboard
                        ]),
                        'disable_web_page_preview'  => true,
                        'parse_mode'                => 'HTML'
                    ]);
                    $cleanDate->touch();
                }
            });
    }
}
