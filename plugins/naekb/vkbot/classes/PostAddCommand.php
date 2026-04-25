<?php namespace NAEkb\VkBot\Classes;

use NAEkb\VkBot\Models\VkSettings;
use VK\Exceptions\Api\VKApiAccessException;

use Modules\VkBot\Entities\CleanDate;

class PostAddCommand extends AbstractCommand
{
    /** @inheritdoc */
    public array $text = [
        'main'
    ];

    /** @inheritdoc */
    protected string $name = 'postAdd';

    /** @inheritdoc */
    protected function main()
    {
        $this->reply(__('naekb.vkbot::lang.commands.post_help'), [], false ,true);
    }

    protected function adminMain() {
        $keyboard = [
            'inline' => true,
            'buttons' => [
                [
                    [
                        'action' => [
                            'type' => 'callback',
                            'payload'  => json_encode([
                                'command' => 'postAdd',
                                'action' => 'post',
                                'postId' => $this->object['id'],
                                'fromId' => $this->userId,
                            ], JSON_UNESCAPED_SLASHES),
                            'label' => __('naekb.vkbot::lang.notifications.post_confirm')
                        ],
                        'color' => 'positive'
                    ],
                    [
                        'action' => [
                            'type' => 'callback',
                            'payload' => json_encode([
                                'command' => 'postAdd',
                                'action' => 'delete',
                                'postId' => $this->object['id'],
                                'fromId' => $this->userId,
                            ], JSON_UNESCAPED_SLASHES),
                            'label' => __('naekb.vkbot::lang.notifications.post_delete')
                        ],
                        'color' => 'negative'
                    ]
                ]
            ]
        ];

        $name = $this->getUserName($this->userId);
        $this->sendToAdmins(
            __('naekb.vkbot::lang.notifications.user_post', [
                'id' => $this->userId,
                'name' => $name
            ]),
            $keyboard,
            [
                "wall{$this->object['owner_id']}_{$this->object['id']}"
            ]
        );
    }

    protected function post()
    {
        if (empty($this->payload['postId']) || !$this->isAdmin()) {
            return;
        }

        $groupId = VkSettings::get('group_id') * -1;
        try {
            $post = $this->callApi('wall.post', [
                'owner_id' => VkSettings::get('group_id') * -1,
                'post_id' => $this->payload['postId']
            ], true);

            $name = $this->getUserName($this->payload['fromId']);
            $adminName = $this->getUserName($this->userId);
            $this->sendToAdmins(
                __('naekb.vkbot::lang.notifications.post_confirmed', [
                    'id' => $this->payload['fromId'],
                    'name' => $name
                ]) . "\r\n" . __('naekb.vkbot::lang.notifications.admin', [
                    'id' => $this->userId,
                    'name' => $adminName
                ]),
                [],
                [
                    "wall{$groupId}_{$post['post_id']}"
                ]
            );
        } catch (VKApiAccessException $e) {
            report($e);
            $this->reply(__('naekb.vkbot::lang.notifications.post_confirm_err'), [], false, false);
        }
    }

    protected function delete()
    {
        if (empty($this->payload['postId']) || !$this->isAdmin()) {
            return;
        }

        $name = $this->getUserName($this->payload['fromId']);
        $adminName = $this->getUserName($this->userId);
        $this->sendToAdmins(
            __('naekb.vkbot::lang.notifications.post_deleted', [
                'id' => $this->payload['fromId'],
                'name' => $name
            ]) . "\r\n" . __('naekb.vkbot::lang.notifications.admin', [
                'id' => $this->userId,
                'name' => $adminName
            ])
        );

        $this->replyTo($this->payload['fromId'], __('naekb.vkbot::lang.commands.post_deleted'));

        try {
            $this->callApi('wall.delete', [
                'owner_id' => VkSettings::get('group_id') * -1,
                'post_id' => $this->payload['postId']
            ], true);
        } catch (VKApiAccessException $e) {
            //$this->reply(__('naekb.vkbot::lang.notifications.post_delete_err'), [], false, false);
        }
    }
}
