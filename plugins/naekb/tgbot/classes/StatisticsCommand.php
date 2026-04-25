<?php
namespace NAEkb\TgBot\Classes;

use Telegram\Bot\Actions;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;

use NAEkb\TgBot\Helpers\AdminCheck;

class StatisticsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'admin';

    /**
     * @var string Command Description
     */
    protected $description;

    public function __construct() {
        $this->description = __('naekb.tgbot::commands.admin');
    }

    /**
     * @inheritdoc
     */
    public function handle(array $arguments = [])
    {
        if (AdminCheck::isAdmin()) {
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage([
                'text'          => $this->description,
                'reply_markup'  => new Keyboard([
                    'inline_keyboard' => [
                        [
                            Keyboard::button([
                                'text'          => 'Посты',
                                'callback_data' => 'channel  posts'
                            ])
                        ],
                        [
                            Keyboard::button([
                                'text'          => 'Заявки на вступление в канал',
                                'callback_data' => 'channelAdminCommand.phpjoins'
                            ])
                        ],
                        [
                            Keyboard::button([
                                'text'          => 'Статистика',
                                'callback_data' => 'statistics'
                            ]),
                        ],
                        [
                            Keyboard::button([
                                'text'          => __('naekb.tgbot::commands.main'),
                                'callback_data' => 'start'
                            ])
                        ]
                    ],
                ])
            ]);
        }


        return true;
    }
}
