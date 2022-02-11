<?php
namespace Modules\TgBot\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

use Modules\TgBot\Helpers\AdminCheck;
use Modules\TgBot\Entities\TgPage;
use Modules\TgBot\Entities\TgState;

abstract class AbstractCommand extends Command
{
    /** @var string Default action method name */
    protected $defaultAction = 'main';

    /** @var string Arguments parsed from bot */
    protected $arguments = [];

    /** @var string Command Argument Pattern */
    protected $pattern;

    /** @var string Command Name */
    protected $name;

    /** @var string Command Description */
    protected $description;

    /** @var TgPage Page for command */
    protected $page;

    /**
     * Set content for description and fetch whole page if exists
     */
    public function __construct() {
        $this->page = TgPage::whereLike('command', $this->name)->first();
        $this->description = $this->page->content ?? __('tgbot::commands.start.description');
    }

    /**
     * @inheritdoc
     */
    public function handle(array $arguments = [])
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $arguments['action'] = $arguments['action'] ?? $this->defaultAction;
        $this->arguments = $arguments;

        $this->{$arguments['action']}();
        return true;
    }

    protected function reply(?string $msg = '', ?array $keyboard = [], ?bool $back = true, ?string $parseMode = 'HTML')
    {
        if (!empty($this->page)) {
            foreach ($this->page->childrens()->orderBy('order') as $child) {
                $keyboard[] = [
                    Keyboard::button([
                        'text'          => $child->title,
                        'callback_data' => $child->command . ($child->command == 'page' ? "  {$child->id}" : '')
                    ])
                ];
            }
        }

        if (empty($keyboard)) {
            $keyboard[] = [
                Keyboard::button([
                    'text'          => __('tgbot::commands.main'),
                    'callback_data' => 'start'
                ])
            ];
        }

        if ($back) {
            $user_id = $this->getUpdate()->getMessage()->getChat()->getId();
            $state = TgState::where('tg_user_id', $user_id)->first();

            $keyboard[] = [
                Keyboard::button([
                    'text'          => __('tgbot::commands.back'),
                    'callback_data' => $state->prev
                ]),
                Keyboard::button([
                    'text'          => __('tgbot::commands.main'),
                    'callback_data' => 'start'
                ])
            ];
        }

        $this->replyWithMessage([
            'text'          => $msg ?? $this->description,
            'reply_markup'  => new Keyboard([
                'inline_keyboard' => [
                    $keyboard
                ]
            ]),
            'parse_mode'    => 'HTML'
        ]);
    }

    abstract protected function main();
}
