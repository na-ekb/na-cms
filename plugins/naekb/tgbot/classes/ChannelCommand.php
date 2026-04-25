<?php
namespace NAEkb\TgBot\Classes;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

use Telegram\Bot\Keyboard\Keyboard;

class ChannelCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected $name = 'start';

    /** @inheritdoc */
    protected $pattern = 'channel{action?}';

    public function __construct() {
        $this->description = __('naekb.tgbot::commands.channel.description');
    }

    /**
     * @inheritdoc
     */
    public function start(array $arguments = [])
    {
        $arguments['action'] = $arguments['action'] ?? 'addToChannel';

        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $this->{$arguments['action']}($arguments);

        return true;
    }

    public function addToChannel(array $arguments = [])
    {

    }

    public function postToChannel(array $arguments = [])
    {

    }
}
