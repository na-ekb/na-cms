<?php namespace NAEkb\VkBot\Classes;

use Exception;
use Illuminate\Support\Str;

use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiAccessException;
use VK\Exceptions\Api\VKApiMessagesDenySendException;
use VK\Exceptions\Api\VKApiMessagesGroupPeerAccessException;
use VK\Exceptions\Api\VKApiParamException;

use NAEkb\VKBot\Models\State;
use NAEkb\VkBot\Models\VkSettings;

abstract class AbstractCommand
{
    /** @var array Detect can user send text messages with this command */
    public array $text = [];

    /** @var string Default action method name */
    protected string $name = 'start';

    /** @var string Default action method name */
    protected string $defaultAction = 'main';

    /** @var array Payload parsed from button */
    protected array $payload = [];

    /** @var string Command Description */
    protected string $description;

    /** @var VKApiClient $api */
    protected VKApiClient $api;

    /** @var int User id */
    protected int $userId;

    /** @var string $groupToken */
    protected string $groupToken;

    /** @var string|null $adminToken */
    protected ?string $adminToken;

    /** @var array $object */
    protected array $object;

    /** @var State $state */
    protected State $state;

    /**
     * Set content for description and fetch whole page if exists
     */
    public function __construct(array $object, string $groupToken, ?string $adminToken = null) {
        $this->description  =  __('naekb.vkbot::lang.commands.' . Str::snake($this->name) . '.description');

        if (!empty($object['user_id'])) {
            $this->userId = $object['user_id'];
        } elseif (!empty($object['message'])) {
            $this->userId = $object['message']->from_id;
        } elseif (!empty($object['from_id'])) {
            $this->userId = $object['from_id'];
        } else {
            throw new Exception('Error: not found user_id');
        }

        $this->groupToken = $groupToken;
        $this->adminToken = $adminToken;
        $this->object = $object;
        $this->state = State::firstOrCreate([
            'user_id'   => $this->userId
        ], [
            'state'     => 'start',
            'action'    => $this->defaultAction
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function handleCallback(): string
    {
        $options = [
            'event_id'  => $this->object['event_id'],
            'user_id'   => $this->object['user_id'],
            'peer_id'   => $this->userId
        ];

        $this->payload = (array) $this->object['payload'];

        $eventData = $this->{$this->object['payload']->action ?? $this->defaultAction}();

        if (!empty($eventData)) {
            $options['event_data'] = json_encode($eventData, JSON_UNESCAPED_SLASHES);
        }

        $this->setCommandState($this->object['payload']->action ?? $this->defaultAction);
        try {
            $this->callApi('messages.sendMessageEventAnswer', $options);
        } catch (VKApiParamException $e) {}

        return 'ok';
    }

    /**
     * @param array $arguments
     * @return string
     * @throws Exception
     */
    public function handle(array $arguments = []): string
    {
        if (empty($arguments['noActivity'])) {
            $this->callApi('messages.setActivity', [
                'type'      => 'typing',
                'peer_id'   => $this->userId
            ]);
        }

        $this->{$arguments['action'] ?? $this->defaultAction}();
        return 'ok';
    }

    protected function replyTo(?int $userId, ?string $msg = '', ?array $keyboard = [], ?bool $back = true, ?bool $main = true) {
        $oldId = $this->userId;
        $this->userId = $userId;
        $response = $this->reply($msg, $keyboard, $back, $main);
        $this->userId = $oldId;
        return $response;
    }

    protected function reply(?string $msg = '', ?array $keyboard = [], ?bool $back = true, ?bool $main = true, ?array $attachments = [])
    {
        if ($this->state->allow == 0) {
            return false;
        }

        if ($back) {
            $state = State::where('user_id', $this->userId)->first();

            if ($main && $state->prev !== 'start') {
                $back = [
                    'command' => $state->prev
                ];
                if (!empty($state->prev_action)) {
                    $back['action'] = $state->prev_action;
                }
                $keyboard['buttons'][] = [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode($back, JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.back')
                        ]
                    ],
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode(['command' => 'start'], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.main')
                        ]
                    ]
                ];
            } else {
                $keyboard['buttons'][] = [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode(['command' => 'start'], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.main')
                        ]
                    ]
                ];
            }
        }

        if (!$back && $main) {
            $keyboard['buttons'][] = [
                [
                    'action' => [
                        'type'      => 'callback',
                        'payload'   => json_encode(['command' => 'start'], JSON_UNESCAPED_SLASHES),
                        'label'     => __('naekb.vkbot::lang.commands.main')
                    ]
                ]
            ];
        }

        if (!empty($keyboard) && empty($keyboard['inline'])) {
            $keyboard['inline'] = true;
        }

        $params = [
            'random_id' => 0,
            'peer_id'   => $this->userId,
            'message'   => $msg ?? $this->description
        ];

        if (!empty($attachments)) {
            $params['attachment'] = implode(',', $attachments);
        }

        if (!empty($keyboard)) {
            $params['keyboard'] = json_encode($keyboard, JSON_UNESCAPED_SLASHES);
        } else {
            $params['keyboard'] = json_encode(['buttons' => [], 'one_time' => true], JSON_UNESCAPED_SLASHES);
        }

        try {
            $result = $this->callApi('messages.send', $params);
        } catch (VKApiMessagesDenySendException $e) {
            $this->state->update([
                'allow' => 0
            ]);
            return false;
        }

        return $result;
    }

    /**
     * @param string|null $msg
     * @param array|null $keyboard
     * @param array|null $attachments
     * @param array|null $forwarded
     * @param array|null $exclude
     * @return void
     * @throws Exception
     */
    protected function sendToAdmins(?string $msg = '', ?array $keyboard = [], ?array $attachments = [], ?array $forwarded = [], ?array $exclude = []): void
    {
        $admins = $this->callApi('groups.getMembers', [
            'group_id'  => VkSettings::get('group_id'),
            'filter'    => 'managers',
            'fields'    => 'can_write_private_message'
        ]);

        foreach ($admins['items'] as $admin) {
            if (in_array($admin['id'], $exclude)) {
                continue;
            }

            try {
                $this->callApi('messages.setActivity', [
                    'type' => 'typing',
                    'peer_id' => $admin['id']
                ]);
            } catch (VKApiMessagesDenySendException|VKApiMessagesGroupPeerAccessException $e) {
                report($e);
                $exclude[] = $admin['id'];
                $this->sendToAdmins(__('naekb.vkbot::lang.notifications.admin_access', [
                    'id' => $admin['id'],
                    'name' => "{$admin['first_name']} {$admin['last_name']}"
                ]), [], [], [], $exclude);
                continue;
            }

            $options = [
                'random_id' => 0,
                'peer_id'   => $admin['id'],
                'message'   => $msg
            ];

            if (!empty($keyboard)) {
                if (empty($keyboard['inline'])) {
                    $keyboard['inline'] = true;
                }
                $options['keyboard'] = json_encode($keyboard, JSON_UNESCAPED_SLASHES);
            } else {
                $options['keyboard'] = json_encode(['buttons' => [], 'one_time' => true], JSON_UNESCAPED_SLASHES);
            }

            if(!empty($attachments)) {
                $options['attachment'] = implode(',', $attachments);
            }

            if (!empty($forwarded)) {
                $options['forward_messages'] = $forwarded;
            }

            try {
                $this->callApi('messages.send', $options);
            } catch (VKApiMessagesDenySendException|VKApiMessagesGroupPeerAccessException $e) {
                report($e);
                continue;
            } catch (VKApiAccessException $e) {
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (str_contains($attachment, 'wall')) {
                            $keyboard['buttons'][] = [
                                [
                                    'action' => [
                                        'type' => 'open_link',
                                        'link' => __('naekb.vkbot::lang.notifications.post_link', [
                                            'id' => VkSettings::get('group_id'),
                                            'wall' => $attachment
                                        ]),
                                        'label' => __('naekb.vkbot::lang.notifications.post')
                                    ]
                                ]
                            ];
                            if (empty($msg)) {
                                $msg = __('naekb.vkbot::lang.notifications.forward_error');
                            }
                            $this->sendToAdmins($msg, $keyboard, [], $forwarded);
                            return;
                        }
                    }
                }
                report($e);
                return;
            }
        }
    }

    /**
     * Set state to some action in command
     *
     * @param string|null $action
     * @param string|null $command
     * @return void
     */
    protected function setCommandState(?string $action = null, ?string $command = null): void
    {
        $this->state->update([
            'state'         => $command ?? $this->name,
            'action'        => $action ?? $this->defaultAction,
            'prev'          => $this->state->state,
            'prev_action'   => $this->state->action
        ]);
    }

    /**
     * @param string $action
     * @param array $params
     * @param bool $admin
     * @return mixed
     * @throws Exception
     */
    protected function callApi(string $action, array $params, bool $admin = false): mixed
    {
        if (empty($this->api)) {
            $this->api = new VKApiClient(config('naekb.vkbot::vkbot.api_version'));
        }

        $action = explode('.', $action);
        if (count($action) != 2) {
            throw new Exception('Incorrect api endpoint');
        }
        return $this->api->{$action[0]}()->{$action[1]}($admin ? $this->adminToken : $this->groupToken, $params);
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isAdmin(): bool
    {
        $admins = $this->callApi('groups.getMembers', [
            'group_id'  => VkSettings::get('group_id'),
            'filter'    => 'managers',
        ]);
        foreach ($admins['items'] as $admin) {
            if ($admin['id'] == $this->userId) {
                return true;
            }
        }

        return false;
    }

    protected function getUserName($id = null): string
    {
        if (empty($id) && !empty($this->userId)) {
            $id = $this->userId;
        }

        $user = $this->callApi('users.get', [
            'user_ids' => $id
        ]);
        $user = array_shift($user);
        return "{$user['first_name']} {$user['last_name']}";
    }

    abstract protected function main();
}
