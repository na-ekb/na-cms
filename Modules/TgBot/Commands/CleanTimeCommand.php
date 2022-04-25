<?php
namespace Modules\TgBot\Commands;

use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use function morphos\Russian\pluralize;
use function morphos\English\pluralize as engPluralize;

use Modules\TgBot\Entities\TgCleanDate;
use Modules\TgBot\Entities\TgState;

class CleanTimeCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected $name = 'cleanTime';

    /** @inheritdoc */
    protected $pattern = 'cleanTime{action?}';

    /** @inheritdoc */
    protected function main()
    {
        if (TgCleanDate::where('tg_user_id', $this->userId)->exists()) {
            $this->start();
        } else {
            $this->set(true);
        }
    }

    protected function start() {
        $from = TgCleanDate::where('tg_user_id', $this->userId)->first()->date;
        $diff = Carbon::now()->diff($from);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        $date = '';
        if($years > 0) {
            if (app()->getLocale() == 'ru') {
                $date .= pluralize($years, 'год');
            } else {
                $date .= engPluralize($years, 'year');
            }
        }

        if($months > 0) {
            if (app()->getLocale() == 'ru') {
                $date .= ' ' . pluralize($months, 'месяц');
            } else {
                $date .= ' ' . engPluralize($months, 'month');
            }
        }

        if($days > 0) {
            if ($months > 0 || $years > 0) {
                $date .= ' ' . __('tgbot::commands.and');
            }
            if (app()->getLocale() == 'ru') {
                $date .= ' ' . pluralize($days, 'день');
            } else {
                $date .= ' ' . engPluralize($days, 'day');
            }
        }

        if (empty($date)) {
            $date = 'tgbot::commands.clean_time.jft';
        }

        $text = __('tgbot::commands.clean_time.clean', [
            'date' => $date
        ]);

        $keyboard = [
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.clean_time.delete'),
                    'callback_data' => 'cleanTime  del'
                ]),
            ]
        ];

        $this->reply($text, $keyboard, true, false);
    }

    protected function set(bool $first = false)
    {
        //$this->setCommandState('cleanTime  parse');

        if (empty(config('TgBot.tg_channel'))) {
            $text = __('tgbot::commands.clean_time.set');
        } else {
            $text = __('tgbot::commands.clean_time.set_channel');
        }

        $keyboard = [
            [
                Keyboard::button([
                    'text'      => __('tgbot::commands.clean_time.set_btn'),
                    'web_app'   => route('tgbot.webapp', [
                        'command'   => $this->name,
                        'action'    => 'parseData',
                        'token'     => config('telegram.webhook_secret_token')
                    ])
                ]),
            ]
        ];

        $this->reply($text, $keyboard, true, false);
    }

    protected function parse(array $arguments = [])
    {
        $user_id = $this->getUpdate()->getMessage()->getChat()->getId();
        $text = $this->getUpdate()->getMessage()->getText();
        if (!empty($text)) {
            try {
                $time = Carbon::parse($text);
                TgCleanDate::updateOrCreate([
                    'tg_user_id' => $user_id
                ], [
                    'date' => $time
                ]);
                if (empty(config('TgBot.tg_channel'))) {
                    $text = __('tgbot::commands.clean_time.setted');
                } else {
                    $text = __('tgbot::commands.clean_time.setted_channel');
                }
            } catch (\Throwable $e) {
                $text = __('tgbot::commands.clean_time.error');
            }
        }

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => new Keyboard([
                'inline_keyboard' => [
                    [
                        Keyboard::button([
                            'text'          => __('tgbot::commands.back'),
                            'callback_data' => "cleanTime"
                        ]),
                        Keyboard::button([
                            'text'          => __('tgbot::commands.main'),
                            'callback_data' => 'start'
                        ])
                    ]
                ]
            ])
        ]);
    }

    protected function del(array $arguments = [])
    {
        $arguments['empathy'] = $arguments['empathy'] ?? 0;

        $user_id = $this->getUpdate()->getMessage()->getChat()->getId();
        TgCleanDate::where('tg_user_id', $user_id)->delete();

        $this->replyWithMessage([
            'text' => $arguments['empathy'] ? __('tgbot::commands.clean_time.empathy') : __('tgbot::commands.clean_time.deleted'),
            'reply_markup' => new Keyboard([
                'inline_keyboard' => [
                    [
                        Keyboard::button([
                            'text'          => __('tgbot::commands.clean_time.set_again'),
                            'callback_data' => 'cleanTime  set'
                        ]),
                        Keyboard::button([
                            'text'          => __('tgbot::commands.main'),
                            'callback_data' => 'start'
                        ])
                    ]
                ]
            ])
        ]);
    }

    protected function congr(array $arguments = []) {
        $arguments['private'] = $arguments['private'] ?? 0;

        if (!empty(config('TgBot.tg_channel')) && !$arguments['private']) {
            $user = $this->getUpdate()->getMessage()->getChat();
            $months = TgCleanDate::selectRaw('TIMESTAMPDIFF(MONTH, `date` - INTERVAL 1 DAY, NOW()) as `clean`')
                ->where('tg_user_id', $user->getId())->first();

            if (!empty($months->clean)) {
                $months = $months->clean;
                if ($months > 11) {
                    if ($months % 12 > 0) {
                        $years = ($months - ($months % 12)) / 12;
                        $months_remnant = $months % 12;
                        if (app()->getLocale() == 'ru') {
                            $yDate = pluralize($years, 'год');
                            $mDate = pluralize($months_remnant, 'месяц');
                            $text = "{$yDate} и {$mDate}";
                        } else {
                            $yDate = engPluralize($years, 'year');
                            $mDate = engPluralize($months_remnant, 'month');
                            $text = "{$yDate} and {$mDate}";
                        }
                    } else {
                        if (app()->getLocale() == 'ru') {
                            $years = $months / 12;
                            $yDate = pluralize($years, 'год');
                        } else {
                            $years = $months / 12;
                            $yDate = engPluralize($years, 'year');
                        }

                        $text = $yDate;
                    }
                } else {
                    if (app()->getLocale() == 'ru') {
                        $mDate = pluralize($months, 'месяц');
                    } else {
                        $mDate = engPluralize($months, 'year');
                    }

                    $text = $mDate;
                }


                if (empty($user->getUsername())) {
                    $name = $user->getFirstName() . ' ' . $user->getLastName();
                    if ($name == ' ') {
                        $name = 'Аноним';
                    }
                } else {
                    $name = '@' . $user->getUsername();
                }

                $this->telegram->sendMessage([
                    'chat_id'                   => config('TgBot.tg_channel'),
                    'text'                      => __('tgbot::commands.channel.congr', [
                        'userId'    => $user->getId(),
                        'userName'  => $name,
                        'date'      => $text
                    ]),
                    'disable_web_page_preview'  => true,
                    'parse_mode'                => 'HTML'
                ]);
            }
        }

        $this->replyWithMessage([
            'text' => $arguments['private'] ? __('tgbot::commands.clean_time.congr') : __('tgbot::commands.clean_time.congr_channel'),
            'reply_markup' => new Keyboard([
                'inline_keyboard' => [
                    [
                        Keyboard::button([
                            'text'          => __('tgbot::commands.main'),
                            'callback_data' => 'start'
                        ])
                    ]
                ]
            ])
        ]);
    }

    protected function congrPriv(array $arguments = []) {
        $arguments['private'] = 1;
        $this->congr($arguments);
    }

    protected function empathy(array $arguments = []) {
        $arguments['empathy'] = 1;
        $this->del($arguments);
    }
}
