<?php namespace NAEkb\VkBot\Classes;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use VK\CallbackApi\VKCallbackApiHandler;

use NAEkb\VKBot\Models\State;
use NAEkb\VKBot\Models\Stat;

class VkBotService extends VKCallbackApiHandler {

    /** @var string $secret */
    protected string $secret;

    /** @var string $secret */
    protected string $groupId;

    /** @var string $secret */
    protected string $confirmToken;

    /** @var string $groupToken */
    protected string $groupToken;

    /** @var string|null $adminToken */
    protected ?string $adminToken;

    /**
     * @param string $secret
     * @param string $groupId
     * @param string $confirmToken
     * @param string $groupToken
     * @param string|null $adminToken
     */
    public function __construct(string $secret, string $groupId, string $confirmToken, string $groupToken, ?string $adminToken = null)
    {
        $this->secret = $secret;
        $this->groupId = $groupId;
        $this->confirmToken = $confirmToken;
        $this->groupToken = $groupToken;
        $this->adminToken = $adminToken;
    }

    /**
     * @throws \Exception
     */
    public function messageNew($group_id, $secret, $object): string
    {
        if (!empty($object['message']->peer_id) && $object['message']->peer_id > 2000000000) {
            return 'ok';
        }
        if (!empty($object['message'])) {
            if (!empty($object['message']->payload)) {
                $payload = json_decode($object['message']->payload);
                $command = ucfirst($payload->command);
                if (!empty($payload->command) && array_key_exists($command, config('naekb.vkbot::vkbot.commands'))) {
                    $commandClass = config('naekb.vkbot::vkbot.commands')[$command];
                    if (!empty($payload->argument))
                        return (new $commandClass($object, $this->groupToken, $this->adminToken))->handle();
                }
            } else {
                $state = State::where([
                    'user_id' => $object['message']->from_id
                ])->first();

                if (!empty($state) && $state->action == 'conversation') {
                    return 'ok';
                }

                if ($state) {
                    $arguments = [];
                    if (!empty($state->action)) {
                        $arguments['action'] = $state->action;
                    }

                    $command = ucfirst($state->state);
                    if (array_key_exists($command, config('naekb.vkbot::vkbot.commands'))) {
                        $commandClass = new (config('naekb.vkbot::vkbot.commands')[$command])($object, $this->groupToken, $this->adminToken);
                        if (in_array($arguments['action'] ?? $commandClass->defaultAction, $commandClass->text)) {
                            return $commandClass->handle($arguments);
                        }
                    }
                }

                return (new StartCommand($object, $this->groupToken, $this->adminToken))->handle();
            }
        }
        return 'ok';
    }

    public function messageEvent($group_id, $secret, $object): string
    {
        if (!empty($object['message']->peer_id) && $object['message']->peer_id > 2000000000) {
            return 'ok';
        }
        if (!empty($object['user_id']) && !empty($object['payload'])) {
            $command = ucfirst($object['payload']->command);
            if (array_key_exists($command, config('naekb.vkbot::vkbot.commands'))) {
                $commandClass = config('naekb.vkbot::vkbot.commands')[$command];
                return (new $commandClass($object, $this->groupToken, $this->adminToken))->handleCallback();
            }
        }
        return 'ok';
    }

    public function messageReply($group_id, $secret, $object): string
    {
        if (!empty($object['message']->peer_id) && $object['message']->peer_id > 2000000000) {
            return 'ok';
        }
        if (!empty($object['admin_author_id'])) {
            $state = State::firstOrCreate([
                'user_id'   => $object['peer_id']
            ], [
                'state' => 'start',
                'action' => 'conversation'
            ]);

            if (!$state->wasRecentlyCreated) {
                $state->update([
                    'state'         => 'start',
                    'action'        => 'conversation',
                    'prev'          => $state->state,
                    'prev_action'   => $state->action
                ]);
            }
        }
        return 'ok';
    }

    public function wallPostNew($group_id, $secret, $object): string
    {
        if ($object['post_type'] == 'suggest') {
            return (new PostAddCommand($object, $this->groupToken, $this->adminToken))->handle([
                'action' => 'adminMain',
                'noActivity' => true
            ]);
        }
        return 'ok';
    }

    public function groupJoin($group_id, $secret, $object) {
        return (new GroupAddCommand($object, $this->groupToken, $this->adminToken))->handle([
            'noActivity' => true
        ]);
    }

    public function groupLeave($group_id, $secret, $object): string
    {
        return (new GroupRemoveCommand($object, $this->groupToken, $this->adminToken))->handle([
            'noActivity' => true
        ]);
    }

    public function userBlock($group_id, $secret, $object): string
    {
        return (new UserChangeCommand($object, $this->groupToken, $this->adminToken))->handle([
            'action' => 'ban',
            'noActivity' => true
        ]);
    }

    public function userUnblock($group_id, $secret, $object): string
    {
        return (new UserChangeCommand($object, $this->groupToken, $this->adminToken))->handle([
            'action' => 'unban',
            'noActivity' => true
        ]);
    }

    public function messageAllow(int $group_id, ?string $secret, array $object)
    {
        return (new UserChangeCommand($object, $this->groupToken, $this->adminToken))->handle([
            'action' => 'allow',
            'noActivity' => true
        ]);
    }

    public function messageDeny(int $group_id, ?string $secret, array $object)
    {
        return (new UserChangeCommand($object, $this->groupToken, $this->adminToken))->handle([
            'action' => 'deny',
            'noActivity' => true
        ]);
    }

    /**
     * @param $event
     * @return mixed|string
     */
    public function parse($event) {
        if (empty($event)) {
            return 'ok';
        }

        if ($event->type == 'confirmation') {
            return $this->confirmation($event->group_id, $event->secret, (array) $event);
        } else {
            return $this->parseObject($event->group_id, $event->secret, $event->type, (array) $event->object);
        }
    }

    /**
     * @param int $group_id
     * @param null|string $secret
     * @param string $type
     * @param array $object
     * @return mixed
     */
    public function parseObject(int $group_id, ?string $secret, string $type, array $object) {
        $method = Str::camel($type);
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $stat = Stat::firstOrCreate([
            'type'  => $method,
            'month' => $month,
            'year'  => $year
        ], [
            'count' => 1
        ]);

        if (!$stat->wasRecentlyCreated) {
            $stat->count += 1;
            $stat->save();
        }

        if (method_exists($this, $method)) {
            return $this->{$method}($group_id, $secret, $object);
        }

        return 'ok';
    }

    /**
     * @param int $group_id
     * @param string|null $secret
     * @param array $object
     * @return string
     */
    public function confirmation(int $group_id, ?string $secret, array $object) {
        if ($secret == $this->secret && $group_id == $this->groupId) {
            return $this->confirmToken;
        } else {
            return 'Error groupId or webHook secret';
        }
    }
}
