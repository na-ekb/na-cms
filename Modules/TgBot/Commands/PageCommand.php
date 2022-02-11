<?php
namespace Modules\TgBot\Commands;

use Modules\TgBot\Entities\TgPage;
use Telegram\Bot\Keyboard\Keyboard;
use Modules\TgBot\Entities\TgTokens;

class PageCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected $name = 'page';

    /** @inheritdoc */
    protected $pattern = 'page{id}';

    public function handle(array $arguments = [])
    {
        $this->page = TgPage::find($arguments['id']);
        $this->description = $this->page->content ?? __('tgbot::commands.404');

        parent::handle($arguments);
    }

    protected function main() {
        $this->reply();
    }
}
