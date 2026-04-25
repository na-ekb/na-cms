<?php namespace NAEkb\VkBot\Classes;

use Illuminate\Support\Carbon;
use Carbon\Exceptions\InvalidFormatException;

use NAEkb\VKBot\Models\Stat;
use NAEkb\VkBot\Models\VkSettings;

class StatisticsCommand extends AbstractCommand
{
    /** @inheritdoc  */
    public array $text = [
        'main'
    ];

    /** @inheritdoc  */
    protected string $name = 'statistics';

    /** @inheritdoc */
    public function main()
    {
        if (!empty($this->object['message']) && !empty($this->object['message']->text)) {
            try {
                $message = trim($this->object['message']->text);
                if (str_contains($message, ' ')) {
                    $message = explode(' ', $message);
                    $from = Carbon::parse(array_shift($message))->startOfDay();
                    $to = Carbon::parse(array_pop($message))->startOfDay();
                } else {
                    $from = Carbon::parse($message)->startOfDay();
                    $to = Carbon::now();
                }

                if (!$from->lessThan(Carbon::now()->subDay()->startOfDay())) {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error_future');
                    $this->reply($text, [], false);
                    return;
                }
                if ($to->lessThan($from)) {
                    $text = __('naekb.vkbot::lang.commands.statistics.error_future');
                    $this->reply($text, [], false);
                    return;
                }

                $countMembers = $this->callApi('groups.getMembers', [
                    'group_id' => VkSettings::get('group_id'),
                ]);
                $stats = $this->callApi('stats.get', [
                    'group_id'          => VkSettings::get('group_id'),
                    'timestamp_from'    => $from->getTimestamp(),
                    'timestamp_to'      => $to->getTimestamp(),
                    'extended'          => 1
                ], true);

                $statistics = [
                    'members'           => $countMembers['count'],
                    'comments'          => 0,
                    'copies'            => 0,
                    'hidden'            => 0,
                    'likes'             => 0,
                    'subscribed'        => 0,
                    'unsubscribed'      => 0,
                    'views'             => 0,
                    'mobile_views'      => 0,
                    'visitors'          => 0,
                    'visitors_ages'     => [
                        '12-18' => 0,
                        '18-21' => 0,
                        '21-24' => 0,
                        '24-27' => 0,
                        '27-30' => 0,
                        '30-35' => 0,
                        '35-45' => 0,
                        '45-100'=> 0
                    ],
                    'visitors_sex'      => [
                        'f'     => 0,
                        'm'     => 0
                    ],
                    'reach'             => 0,
                    'reach_subscribers' => 0,
                    'mobile_reach'      => 0,
                    'reach_ages'        => [
                        '12-18' => 0,
                        '18-21' => 0,
                        '21-24' => 0,
                        '24-27' => 0,
                        '27-30' => 0,
                        '30-35' => 0,
                        '35-45' => 0,
                        '45-100'=> 0
                    ],
                    'reach_sex'         => [
                        'f'     => 0,
                        'm'     => 0
                    ],
                ];

                foreach ($stats as $period) {
                    if (!empty($period['activity'])) {
                        $activityFields = [
                            'comments',
                            'copies',
                            'hidden',
                            'likes',
                            'subscribed',
                            'unsubscribed',
                        ];
                        foreach ($activityFields as $field) {
                            if (!empty($period['activity'][$field])) {
                                $statistics[$field] += $period['activity'][$field];
                            }
                        }
                    }

                    $fieldsWithAddStat = [
                        'reach' => [
                            'reach',
                            'reach_subscribers',
                            'mobile_reach'
                        ],
                        'visitors' => [
                            'views',
                            'mobile_views',
                            'visitors',
                        ]
                    ];
                    foreach ($fieldsWithAddStat as $fieldWithAddName => $fieldWithAddFields) {
                        if (!empty($period[$fieldWithAddName])) {
                            foreach ($fieldWithAddFields as $field) {
                                if (!empty($period[$fieldWithAddName][$field])) {
                                    $statistics[$field] += $period[$fieldWithAddName][$field];
                                }
                            }

                            if (!empty($period[$fieldWithAddName]['age'])) {
                                foreach ($period[$fieldWithAddName]['age'] as $field) {
                                    if ($field['count'] > 0) {
                                        $statistics["{$fieldWithAddName}_ages"][$field['value']] += $field['count'];
                                    }
                                }
                            }

                            if (!empty($period[$fieldWithAddName]['sex'])) {
                                foreach ($period[$fieldWithAddName]['sex'] as $field) {
                                    if ($field['count'] > 0) {
                                        $statistics["{$fieldWithAddName}_sex"][$field['value']] += $field['count'];
                                    }
                                }
                            }
                        }
                    }
                }

                $text = __('naekb.vkbot::lang.commands.statistics.show', [
                        'start' => $from->format('d.m.Y'),
                        'end'   => $to->format('d.m.Y'),
                    ]) . "\r\n\r\n";

                foreach ($statistics as $statName => $statValue) {
                    if (is_array($statValue)) {
                        $text .= __("naekb.vkbot::lang.commands.statistics.{$statName}") . ":\r\n";
                        foreach ($statValue as $subStatName => $subStatValue) {
                            if ($subStatName == 'm' || $subStatName == 'f') {
                                $text .= "\t• " . __("naekb.vkbot::lang.commands.statistics.{$subStatName}") . " — {$subStatValue}\r\n";
                            } else {
                                $text .= "\t• {$subStatName} — {$subStatValue}\r\n";
                            }
                        }
                    } else {
                        $text .= __("naekb.vkbot::lang.commands.statistics.{$statName}") . " — {$statValue}\r\n";
                    }
                    $text .= "\r\n";
                }

                $messageNew = Stat::where('type', 'messageNew')->sum('count');
                $text .= __('naekb.vkbot::lang.commands.statistics.messageNew') . " — {$messageNew}\r\n";

                $messageReply = Stat::where('type', 'messageReply')->sum('count');
                $text .= __('naekb.vkbot::lang.commands.statistics.messageReply') . " — {$messageReply}\r\n";

                $messageEvent = Stat::where('type', 'messageEvent')->sum('count');
                $text .= __('naekb.vkbot::lang.commands.statistics.messageEvent') . " — {$messageEvent}\r\n";
            } catch (\Throwable $e) {
                if (!is_a($e, InvalidFormatException::class)) {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error_500');
                    report($e);
                } else {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error');
                }
            }
        } else {
            $text = __('naekb.vkbot::lang.commands.statistics.get');
        }

        $this->reply($text, [], false);
    }
}
