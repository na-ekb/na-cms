<?php namespace NAEkb\VkBot\Classes;

use NAEkb\VKBot\Models\State;
use NAEkb\VkBot\Models\VkSettings;

class GroupAddCommand extends AbstractCommand
{
    /** @var string Default action method name */
    protected string $name = 'groupAdd';

    protected function main()
    {
        if ($this->object['join_type'] == 'request') {
            $keyboard = [
                'buttons'   => [
                    [
                        [
                            'action' => [
                                'type' => 'open_link',
                                'link' => __('naekb.vkbot::lang.notifications.add_user_link', [
                                    'id' => VkSettings::get('group_id')
                                ]),
                                'label' => __('naekb.vkbot::lang.notifications.add_user_link_text')
                            ]
                        ]
                    ],
                ]
            ];
            $this->sendToAdmins(__('naekb.vkbot::lang.notifications.new_user', [
                'id'    => $this->userId,
                'name'  => $this->getUserName()
            ]), $keyboard) ;
        } elseif($this->object['join_type'] == 'approved') {
            $this->sendToAdmins(__('naekb.vkbot::lang.notifications.add_user', [
                'id'    => $this->userId,
                'name'  => $this->getUserName()
            ]));
        }

        return 'ok';
    }
}
