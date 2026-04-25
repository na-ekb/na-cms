<?php namespace NAEkb\VkBot\Classes;

use NAEkb\VkBot\Models\VkSettings;

class StartCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected string $name = 'start';

    /** @inheritdoc */
    protected function main() {
        $keyboard = [
            'buttons'   => [
                [
                    [
                        'action' => [
                            'type'  => 'open_link',
                            'link'  => VkSettings::get('groups_link'),
                            'label' => __('naekb.vkbot::lang.commands.start.groups')
                        ]
                    ],
                    [
                        'action' => [
                            'type'   => 'open_link',
                            'link'   => VkSettings::get('jft_link'),
                            'label'  => __('naekb.vkbot::lang.commands.start.jft')
                        ]
                    ]
                ],
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode(['command' => 'postAdd'], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.post')
                        ]
                    ]
                ],
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode(['command' => 'cleanTime'], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.start.clean_time')
                        ]
                    ]
                ],
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode(['command' => 'help'], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.start.help')
                        ]
                    ],
                ]
            ]
        ];

        if ($this->isAdmin() && !empty(VkSettings::get('admin_token'))) {
            $keyboard['buttons'][3] = [
                [
                    'action' => [
                        'type'      => 'callback',
                        'payload'   => json_encode(['command' => 'statistics'], JSON_UNESCAPED_SLASHES),
                        'label'     => __('naekb.vkbot::lang.commands.start.stats')
                    ]
                ]
            ];
        }

        $this->reply($this->description, $keyboard, false, false);
    }

    public function conversation() {}
}
