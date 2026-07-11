<?php namespace NAEkb\TgBot;

use Illuminate\Support\Carbon;
use System\Classes\PluginBase;

use Telegram\Bot\Api;

use NAEkb\TgBot\Widgets\SetWebHook;
use NAEkb\TgBot\Models\TgSettings;
use NaEkb\Groups\Models\GroupMeeting;
use NAEkb\Pages\Models\Jft;

class Plugin extends PluginBase
{
    /** @inheritdoc */
    public $require = ['NaEkb.Groups', 'NAEkb.Pages'];

    /** @inheritdoc */
    public function pluginDetails()
    {
        return [
            'name' => 'naekb.tgbot::lang.title',
            'description' => 'naekb.tgbot::lang.description',
            'author' => 'NA Ekb',
            'icon' => 'ph ph-telegram-logo'
        ];
    }

    /** @inheritdoc */
    public function boot()
    {
        /*
            [
                'mybot' => [
                    'token' => env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN'),
                    'certificate_path' => env('TELEGRAM_CERTIFICATE_PATH', 'YOUR-CERTIFICATE-PATH'),
                    'webhook_url' => env('TELEGRAM_WEBHOOK_URL', 'YOUR-BOT-WEBHOOK-URL'),
                    'commands' => [
                        //Acme\Project\Commands\MyTelegramBot\BotCommand::class
                    ],
                ],

                'bot' => [
                    'token'               => env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN'),
                    'commands'            => [
                        Modules\TgBot\Commands\StartCommand::class,
                        Modules\TgBot\Commands\GroupsCommand::class,
                        Modules\TgBot\Commands\GeoCommand::class,
                        Modules\TgBot\Commands\CleanTimeCommand::class,
                        Modules\TgBot\Commands\JftCommand::class,
                        Modules\TgBot\Commands\PageCommand::class,
                    ],
                ],
            ];
        $bots = [];
        TgSettings::withSites()->each(function () {

        });
        $connections = array_merge(config('database.connections'), config('ltg.lif::database.connections') ?? []);
        Config::set('database.connections', $connections);
        */
        \Config::set('telegram', config('naekb.tgbot::telegram'));
    }

    /** @inheritdoc */
    public function registerPermissions()
    {
        return [
            'naekb.tgbot.settings' => [
                'tab' => 'naekb.tgbot::lang.title',
                'label' => 'naekb.tgbot::lang.permissions.settings'
            ],
            'naekb.tgbot.pages' => [
                'tab' => 'naekb.tgbot::lang.title',
                'label' => 'naekb.tgbot::lang.permissions.pages'
            ],
        ];
    }

    /** @inheritdoc */
    public function registerSettings()
    {
        return [
            'tg' => [
                'label'         => 'naekb.tgbot::lang.title',
                'description'   => 'naekb.tgbot::lang.description',
                'category'      => 'naekb.integrations::lang.settings-group',
                'icon'          => 'ph ph-telegram-logo',
                'class'         => TgSettings::class,
                'order'         => 920,
                'keywords'      => 'Tg bot',
                'permissions'   => ['naekb.tgbot.settings'],
            ]
        ];
    }

    /** @inheritdoc */
    public function registerNavigation()
    {
        return [
            'bot' => [
                'label'       => 'naekb.tgbot::lang.title',
                'icon'        => 'ph ph-telegram-logo',
                'permissions' => ['naekb.tgbot.pages'],
                'order'       => 500,
                'sideMenu' => [
                    'pages' => [
                        'label'       => 'naekb.tgbot::lang.pages',
                        'icon'        => 'icon-file-text-o',
                        'url'         => \Backend::url('naekb/tgbot/pages'),
                        'permissions' => ['naekb.tgbot.pages']
                    ],
                ]
            ]
        ];
    }

    /** @inheritdoc */
    public function registerFormWidgets()
    {
        return [
            SetWebHook::class => 'tg-connect'
        ];
    }

    /** @inheritdoc */
    public function registerSchedule($schedule)
    {
        // Ежедневный автопост расписания собраний в канал
        try {
            $sendMeetings = TgSettings::get('send_meetings');
            $sendTime = TgSettings::get('send_time');
        } catch (\Throwable $e) {
            $sendMeetings = false;
            $sendTime = null;
        }

        if ($sendMeetings && !empty($sendTime)) {
            $postTime = Carbon::parse($sendTime)->format('H:i');
            $schedule->call(function () {
                $token = TgSettings::get('bot_secret');
                $channel = TgSettings::get('channel_id');
                if (empty($token) || empty($channel)) {
                    return;
                }

                $meetings = GroupMeeting::with('group')->day(Carbon::today())->orderBy('time')->get();
                if ($meetings->isEmpty()) {
                    return;
                }

                $lines = [];
                if (!empty(TgSettings::get('send_meetings_header'))) {
                    $lines[] = '<b>' . e(TgSettings::get('send_meetings_header')) . '</b>';
                }
                foreach ($meetings as $meeting) {
                    $time = Carbon::parse($meeting->time)->format('H:i');
                    $line = "🕘 {$time} — <b>" . e($meeting->group->title) . '</b>';
                    $place = trim(($meeting->group->city ? $meeting->group->city . ', ' : '') . $meeting->group->address);
                    if ($place !== '') {
                        $line .= "\n📍 " . e($place);
                    }
                    if (!empty($meeting->format_name)) {
                        $line .= "\n" . e($meeting->format_name);
                    }
                    $lines[] = $line;
                }

                $message = [
                    'chat_id'                  => $channel,
                    'text'                     => implode("\n\n", $lines),
                    'parse_mode'               => 'HTML',
                    'disable_web_page_preview' => 1,
                ];
                if (!empty(TgSettings::get('groups_link'))) {
                    $message['reply_markup'] = json_encode([
                        'inline_keyboard' => [[
                            [
                                'text' => __('naekb.tgbot::lang.settings.send_meetings_more'),
                                'url'  => TgSettings::get('groups_link'),
                            ],
                        ]],
                    ]);
                }

                try {
                    (new Api($token))->sendMessage($message);
                } catch (\Throwable $e) {
                    report($e);
                }
            })->name('tg.dailySchedule')->withoutOverlapping()->dailyAt($postTime);
        }

        // Ежедневный автопост размышления Just For Today в канал
        try {
            $sendJft = TgSettings::get('send_jft');
            $sendJftTime = TgSettings::get('send_jft_time');
        } catch (\Throwable $e) {
            $sendJft = false;
            $sendJftTime = null;
        }

        if ($sendJft && !empty($sendJftTime)) {
            $jftTime = Carbon::parse($sendJftTime)->format('H:i');
            $schedule->call(function () {
                $token = TgSettings::get('bot_secret');
                $channel = TgSettings::get('channel_id');
                if (empty($token) || empty($channel)) {
                    return;
                }

                $jft = Jft::today()->first();
                if (empty($jft)) {
                    return;
                }

                $clean = fn ($html) => trim(strip_tags(str_ireplace(['<br>', '<br/>', '<br />', '</p>'], "\n", (string) $html)));

                $lines = [];
                if (!empty(TgSettings::get('send_jft_header'))) {
                    $lines[] = '<b>' . e(TgSettings::get('send_jft_header')) . '</b>';
                }
                $lines[] = '<b>' . e($clean($jft->header)) . '</b>';
                $lines[] = '<i>' . e($clean($jft->quote)) . '</i>';
                if (!empty($jft->from)) {
                    $lines[] = '<b>' . e($clean($jft->from)) . '</b>';
                }

                try {
                    (new Api($token))->sendMessage([
                        'chat_id'                  => $channel,
                        'text'                     => implode("\n\n", $lines),
                        'parse_mode'               => 'HTML',
                        'disable_web_page_preview' => 1,
                        'reply_markup'             => json_encode([
                            'inline_keyboard' => [[
                                [
                                    'text' => __('naekb.tgbot::lang.settings.send_jft_more'),
                                    'url'  => config('Site.site_jft_link', 'https://na-russia.org/eg'),
                                ],
                            ]],
                        ]),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            })->name('tg.dailyJft')->withoutOverlapping()->dailyAt($jftTime);
        }
    }
}
