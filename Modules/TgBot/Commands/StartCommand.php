<?php
namespace Modules\TgBot\Commands;

use Modules\TgBot\Entities\TgPage;
use Telegram\Bot\Keyboard\Keyboard;
use Modules\TgBot\Entities\TgTokens;

class StartCommand extends AbstractCommand
{
    /** @inheritdoc */
    protected $name = 'start';

    /** @inheritdoc */
    protected $pattern = 'start{token?}';

    /** @inheritdoc */
    protected function main() {
        if (
            !empty($this->arguments['token']) &&
            TgTokens::where('token', $this->arguments['token'])->exists()
        ) {
            $token = TgTokens::where('token', $this->arguments['token'])->first();
            $this->triggerCommand($token->command);
            return;
        }

        $this->reply(null, null, false);
    }
}
