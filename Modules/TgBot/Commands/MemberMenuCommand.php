<?php
namespace Modules\TgBot\Commands;

use morphos\Russian\GeographicalNamesInflection;
use morphos\Russian\Cases;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Exceptions\TelegramResponseException;

use Modules\TgBot\Helpers\AdminCheck;

class MemberMenuCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'member';

    /**
     * @var string Command Description
     */
    protected $description;

    public function __construct() {
        $this->description = __('tgbot::commands.member.description');
    }

    /**
     * @inheritdoc
     */
    public function handle(array $arguments = [])
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $keyboard = [];

        if (!empty(config('primary.primary_city'))) {
            $where = GeographicalNamesInflection::getCase(config('primary.primary_city'), Cases::LOCATIVE);
            $keyboard[] = [
                Keyboard::button([
                    'text'          => __('tgbot::commands.member.groups_in') . " {$where}",
                    'callback_data' => 'groups  0'
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.member.groups_region'),
                    'callback_data' => 'groups  1'
                ])
            ];
        } else {
            $keyboard[] = [
                Keyboard::button([
                    'text'          => __('tgbot::commands.member.groups'),
                    'callback_data' => 'groups  0'
                ])
            ];
        }

        $keyboard[1][] = Keyboard::button([
            'text'          => __('tgbot::commands.member.clean_time'),
            'callback_data' => 'cleanTime'
        ]);

        $keyboard[2][] = Keyboard::button([
            'text'          => __('tgbot::commands.member.jft'),
            'callback_data' => 'jft'
        ]);

        $row = 3;
        if (!empty(config('TgBot.tg_channel'))) {
            // Проверка подписан ли человек на рассылку
            $user_id = $this->getUpdate()->getMessage()->getFrom()->getId();
            $chatMember = $this->getTelegram()->getChatMember([
                'chat_id' => config('TgBot.tg_channel'),
                'user_id' => $user_id
            ]);

            if ($chatMember == false) {
                $keyboard[$row][] = Keyboard::button([
                    'text'          => __('tgbot::commands.member.channel'),
                    'callback_data' => 'channel  addToChannel'
                ]);
            } else {
                $keyboard[$row][] = Keyboard::button([
                    'text'          => __('tgbot::commands.member.channel_post'),
                    'callback_data' => 'channel  postToChannel'
                ]);
            }
            $row++;
        }

        // Проверка на админа
        if (!empty(config('TgBot.tg_channel')) && AdminCheck::isAdmin(config('TgBot.tg_channel'), $user_id)) {
            $keyboard[$row][] = Keyboard::button([
                'text'          => __('tgbot::commands.member.admin'),
                'callback_data' => 'admin'
            ]);
            $row++;
        }

        $this->replyWithMessage([
            'text'          => $this->description,
            'reply_markup'  => new Keyboard([
                'inline_keyboard' => $keyboard
            ]),
            'parse_mode'    => 'HTML'
        ]);

        return true;
    }
}
