<?php namespace NAEkb\VkBot\Classes;

use Modules\TgBot\Entities\TgPage;
use Telegram\Bot\Keyboard\Keyboard;
use Modules\TgBot\Entities\TgTokens;

class HelpCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected string $name = 'start';

    /** @inheritdoc */
    protected function main() {
        $this->sendToAdmins(__('naekb.vkbot::lang.commands.help.message', [
            'userId'    => $this->userId,
            'userName'  => $this->getUserName()
        ]));

        return [
            'type'  => 'show_snackbar',
            'text'  => __('naekb.vkbot::lang.commands.help.snackbar')
        ];
    }
}
