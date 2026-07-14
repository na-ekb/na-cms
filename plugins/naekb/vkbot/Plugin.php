<?php namespace NAEkb\VkBot;

use Illuminate\Support\Carbon;
use System\Classes\PluginBase;

use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiAccessGroupException;
use VK\Exceptions\Api\VKApiMessagesContactNotFoundException;
use VK\Exceptions\Api\VKApiMessagesGroupPeerAccessException;
use VK\Exceptions\Api\VKApiUserDeletedException;

use NAEkb\VkBot\Models\VkSettings;
use NAEkb\VkBot\Widgets\ConnectToken;
use NAEkb\VKBot\Models\CleanDate;
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
                    } catch (VKApiAccessGroupException|VKApiMessagesContactNotFoundException|VKApiMessagesGroupPeerAccessException|VKApiUserDeletedException $e) {
                        $cleanDate->delete();
                    } catch (\Throwable $e) {
                        report($e);
                    }
                });
        })->name('vk.cleanDates')->withoutOverlapping()->everyMinute()->between('09:00', '21:00');

        // Ежедневный автопост расписания собраний на стену группы
        try {
            $schedulePostEnabled = VkSettings::get('schedule_post');
            $schedulePostTime = VkSettings::get('schedule_post_time');
        } catch (\Throwable $e) {
            $schedulePostEnabled = false;
            $schedulePostTime = null;
        }

        if ($schedulePostEnabled && !empty($schedulePostTime)) {
            $postTime = Carbon::parse($schedulePostTime)->format('H:i');
            $schedule->call(function () {
                if (empty(VkSettings::get('group_token'))) {
                    return;
                }

                $meetings = GroupMeeting::with('group')->day(Carbon::today())->orderBy('time')->get();
                if ($meetings->isEmpty()) {
                    return;
                }

                $lines = [VkSettings::get('schedule_post_header')];
                foreach ($meetings as $meeting) {
                    $time = Carbon::parse($meeting->time)->format('H:i');
                    $line = "🕘 {$time} — {$meeting->group->title}";
                    $place = trim(($meeting->group->city ? $meeting->group->city . ', ' : '') . $meeting->group->address);
                    if ($place !== '') {
                        $line .= "\n📍 {$place}";
                    }
                    if (!empty($meeting->format_name)) {
                        $line .= "\n{$meeting->format_name}";
                    }
                    $lines[] = $line;
                }

                if (!empty(VkSettings::get('groups_link'))) {
                    $lines[] = 'Полное расписание: ' . VkSettings::get('groups_link');
                }

                try {
                    $vkApi = new VKApiClient(config('naekb.vkbot::vkbot.api_version'));
                    $vkApi->wall()->post(VkSettings::get('group_token'), [
                        'owner_id'   => VkSettings::get('group_id') * -1,
                        'from_group' => 1,
                        'message'    => implode("\n\n", array_filter($lines)),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            })->name('vk.dailySchedule')->withoutOverlapping()->dailyAt($postTime);
        }

        // Ежедневный автопост размышления Just For Today на стену группы
        try {
            $jftPostEnabled = VkSettings::get('jft_post');
            $jftPostTime = VkSettings::get('jft_post_time');
        } catch (\Throwable $e) {
            $jftPostEnabled = false;
            $jftPostTime = null;
        }

        if ($jftPostEnabled && !empty($jftPostTime)) {
            $jftTime = Carbon::parse($jftPostTime)->format('H:i');
            $schedule->call(function () {
                if (empty(VkSettings::get('group_token'))) {
                    return;
                }

                $jft = Jft::today()->first();
                if (empty($jft)) {
                    return;
                }

                $clean = fn ($html) => trim(strip_tags(str_ireplace(['<br>', '<br/>', '<br />', '</p>'], "\n", (string) $html)));

                $parts = [
                    VkSettings::get('jft_post_header'),
                    $clean($jft->header),
                    $clean($jft->quote),
                ];
                if (!empty($jft->from)) {
                    $parts[] = '— ' . $clean($jft->from);
                }
                if (!empty(VkSettings::get('jft_link'))) {
                    $parts[] = 'Читать полностью: ' . VkSettings::get('jft_link');
                }

                try {
                    $vkApi = new VKApiClient(config('naekb.vkbot::vkbot.api_version'));
                    $vkApi->wall()->post(VkSettings::get('group_token'), [
                        'owner_id'   => VkSettings::get('group_id') * -1,
                        'from_group' => 1,
                        'message'    => implode("\n\n", array_filter($parts)),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                }
            })->name('vk.dailyJft')->withoutOverlapping()->dailyAt($jftTime);
        }
    }
}
