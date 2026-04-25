<?php namespace NAEkb\VkBot;

use Illuminate\Support\Carbon;
use System\Classes\PluginBase;

use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiMessagesContactNotFoundException;
use VK\Exceptions\Api\VKApiMessagesGroupPeerAccessException;

use NAEkb\VkBot\Models\VkSettings;
use NAEkb\VkBot\Widgets\ConnectToken;
use NAEkb\VKBot\Models\CleanDate;

class Plugin extends PluginBase
{
    /** @inheritdoc */
    public function pluginDetails()
    {
        return [
            'name' => 'naekb.vkbot::lang.title',
            'description' => 'naekb.vkbot::lang.description',
            'author' => 'NA Ekb',
            'icon' => 'octo-icon-vk'
        ];
    }

    /** @inheritdoc */
    public function registerPermissions()
    {
        return [
            'naekb.vkbot.settings' => [
                'tab' => 'naekb.vkbot::lang.title',
                'label' => 'naekb.vkbot::lang.permissions.settings'
            ],
        ];
    }

    /** @inheritdoc */
    public function registerSettings()
    {
        return [
            'vk' => [
                'label'         => 'naekb.vkbot::lang.title',
                'description'   => 'naekb.vkbot::lang.description',
                'category'      => 'naekb.integrations::lang.settings-group',
                'icon'          => 'octo-icon-vk',
                'class'         => VkSettings::class,
                'order'         => 920,
                'keywords'      => 'VK bot',
                'permissions'   => ['naekb.vkbot.settings'],
            ]
        ];
    }

    /** @inheritdoc */
    public function registerFormWidgets()
    {
        return [
            ConnectToken::class => 'vk-connect'
        ];
    }

    /** @inheritdoc */
    public function registerSchedule($schedule)
    {
        $schedule->call(function () {
            if (empty(VkSettings::get('group_token'))) {
                return;
            }

            $keyboard = [
                'inline'    => true,
                'buttons'   => [
                    [
                        [
                            'action' => [
                                'type'      => 'callback',
                                'payload'   => json_encode([
                                    'command'   => 'cleanTime',
                                    'action'    => 'congr'
                                ], JSON_UNESCAPED_SLASHES),
                                'label'     => __('naekb.vkbot::lang.commands.yes')
                            ]
                        ],
                        [
                            'action' => [
                                'type'      => 'callback',
                                'payload'   => json_encode([
                                    'command'   => 'cleanTime',
                                    'action'    => 'empathy'
                                ], JSON_UNESCAPED_SLASHES),
                                'label'     => __('naekb.vkbot::lang.commands.no')
                            ]
                        ]
                    ],
                    [
                        [
                            'action' => [
                                'type'      => 'callback',
                                'payload'   => json_encode([
                                    'command'   => 'cleanTime',
                                    'action'    => 'congrPriv'
                                ], JSON_UNESCAPED_SLASHES),
                                'label'     => __('naekb.vkbot::lang.commands.clean_time.private')
                            ]
                        ]
                    ],
                ]
            ];

            $vkApi = new VKApiClient(config('naekb.vkbot::vkbot.api_version'));
            $token = VkSettings::get('group_token');

            $today = Carbon::today();
            CleanDate::whereRaw("DAYOFMONTH(`date`) = {$today->day} AND TIMESTAMPDIFF(MONTH, `date` - INTERVAL 1 DAY, '{$today->toDateTimeString()}') > 0")
                ->where('updated_at', '<', Carbon::today())
                ->each(function(CleanDate $cleanDate) use ($keyboard, $vkApi, $token) {
                    try {
                        $vkApi->messages()->setActivity($token, [
                            'type'      => 'typing',
                            'peer_id'   => $cleanDate->user_id
                        ]);
                        $vkApi->messages()->send($token, [
                            'random_id' => 0,
                            'peer_id'   => $cleanDate->user_id,
                            'message'   => __('naekb.vkbot::lang.commands.clean_time.schedule'),
                            'keyboard'  => json_encode($keyboard, JSON_UNESCAPED_SLASHES)
                        ]);
                        $cleanDate->touch();
                    } catch (VKApiMessagesContactNotFoundException|VKApiMessagesGroupPeerAccessException $e) {
                        $cleanDate->delete();
                    } catch (\Throwable $e) {
                        report($e);
                    }
                });
        })->name('vk.cleanDates')->withoutOverlapping()->everyMinute()->between('09:00', '21:00');
    }
}
