<?php namespace NAEkb\TgBot;

use NAEkb\TgBot\Widgets\SetWebHook;
use System\Classes\PluginBase;

use NAEkb\TgBot\Models\TgSettings;

class Plugin extends PluginBase
{
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
        if(TgSettings::get('send_meetings')) {
            $schedule->call(function () {

            })->daily();
        }
    }
}
