<?php
namespace Modules\TgBot\Commands;


use Illuminate\Support\Collection;

use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

use Modules\TgBot\Entities\TgMeeting;
use Modules\TgBot\Entities\TgState;
use Modules\TgBot\Helpers\GroupFormatter;

class GroupsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'groups';

    /** @var string Command Argument Pattern */
    protected $pattern = 'groups{region}{type?}';

    /**
     * @var string Command Description
     */
    protected $description;

    public function __construct() {
        $this->description = __('tgbot::commands.group.description');
    }

    /**
     * @inheritdoc
     */
    public function handle(array $arguments = [])
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $arguments['region'] = $arguments['region'] ?? 0;
        $arguments['type'] = $arguments['type'] ?? null;

        $keyboard = [
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.today'),
                    'callback_data' => "groups  {$arguments['region']} today"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.tomorrow'),
                    'callback_data' => "groups  {$arguments['region']} tomorrow"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.after'),
                    'callback_data' => "groups  {$arguments['region']} after"
                ])
            ],
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.mon'),
                    'callback_data' => "groups  {$arguments['region']} 1"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.tue'),
                    'callback_data' => "groups  {$arguments['region']} 2"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.wed'),
                    'callback_data' => "groups  {$arguments['region']} 3"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.thu'),
                    'callback_data' => "groups  {$arguments['region']} 4"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.fri'),
                    'callback_data' => "groups  {$arguments['region']} 5"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.sat'),
                    'callback_data' => "groups  {$arguments['region']} 6"
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.sun'),
                    'callback_data' => "groups  {$arguments['region']} 0"
                ])
            ],
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.group.geo'),
                    'callback_data' => "geo  {$arguments['region']}"
                ])
            ],
            [
                Keyboard::button([
                    'text'          => __('tgbot::commands.main'),
                    'callback_data' => 'start'
                ])
            ]
        ];
        if ($arguments['type'] !== null) {
            $text = '';
            if (is_numeric($arguments['type'])) {
                $day = (int) $arguments['type'];
            } else {
                switch ($arguments['type']) {
                    case 'today':
                        $text = __('tgbot::commands.group.today') . ' (' . Carbon::now()->format('d.m.Y H:i') . ')' . PHP_EOL . PHP_EOL;
                        $day = Carbon::now()->dayOfWeek;
                        break;
                    case 'tomorrow':
                        $text = __('tgbot::commands.group.tomorrow') . ' (' . Carbon::now()->addDay()->format('d.m.Y H:i') . ')' . PHP_EOL . PHP_EOL;
                        $day = Carbon::now()->addDay()->dayOfWeek;
                        break;
                    case 'after':
                        $text = __('tgbot::commands.group.after') . ' (' . Carbon::now()->addDays(2)->format('d.m.Y H:i') . ')' . PHP_EOL . PHP_EOL;
                        $day = Carbon::now()->addDays(2)->dayOfWeek;
                        break;
                    default:
                        $text = __('tgbot::commands.group.error');
                        break;
                }
            }

            if (isset($day)) {
                TgMeeting::where('day', $day)
                    ->orderBy('time')
                    ->withCasts([
                        'time' => 'datetime',
                        'end_time' => 'datetime'
                    ])->where(function($q) use ($arguments) {
                        if (!empty(config('TgBot.tg_default_city'))) {
                            if ((int) $arguments['region'] == 1) {
                                return $q->where('region', '!=', config('TgBot.tg_default_city'));
                            }
                            return $q->where('region', config('TgBot.tg_default_city'));
                        }
                        return $q;
                    })->each(function($meeting) use (&$text) {
                        $text .= GroupFormatter::format($meeting);
                    });
            }

            $this->description = $text;
        }


        $this->replyWithMessage([
            'text'          => $this->description,
            'reply_markup'  => new Keyboard([
                'inline_keyboard' => $keyboard
            ]),
            'parse_mode'    => 'HTML',
            'disable_web_page_preview' => 1
        ]);

        return true;
    }
}
